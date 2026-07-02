<?php

namespace ANZ\BitUmc\SDK\Infrastructure\Soap\Parser;

final class ScheduleXmlParser extends AbstractSoapXmlParser
{
    private const DEFAULT_DURATION = 1800;
    private const KNOWN_KEYS = [
        'Клиника',
        'СотрудникФИО',
        'СотрудникID',
        'Специализация',
        'ПериодыГрафика',
        'ДлительностьПриема',
    ];
    private const PERIOD_KNOWN_KEYS = [
        'Клиника',
        'Дата',
        'ВремяНачала',
        'ВремяОкончания',
        'ВидВремени',
    ];

    public function parse(string $xml): array
    {
        $reader = $this->elementReader->createReader($xml);
        $schedule = [];
        $errorMessage = '';
        $parameterError = '';

        while ($reader->read()) {
            if ($reader->nodeType !== \XMLReader::ELEMENT) {
                continue;
            }

            if ($reader->localName === 'ОписаниеОшибки') {
                $errorMessage = $this->stringValue($this->elementReader->readCurrentElementValue($reader));
                continue;
            }

            if ($reader->localName === 'ОшибкаПараметров') {
                $parameterError = $this->stringValue($this->elementReader->readCurrentElementValue($reader));
                continue;
            }

            if ($reader->localName !== 'ГрафикДляСайта') {
                continue;
            }

            $item = $this->elementReader->readCurrentElementValue($reader);
            if (!is_array($item)) {
                continue;
            }

            $clinicUid = $this->stringValue($item['Клиника'] ?? '');
            $employeeUid = $this->stringValue($item['СотрудникID'] ?? '');
            if ($clinicUid === '' || $employeeUid === '') {
                continue;
            }

            $specialtyName = $this->stringValue($item['Специализация'] ?? '');
            if ($specialtyName === '') {
                $specialtyName = 'Без основной специализации';
            }
            $specialtyUid = preg_replace('/[^a-z0-9\s]/', '', strtolower(base64_encode($specialtyName))) ?? '';

            $durationFrom1C = $this->stringValue($item['ДлительностьПриема'] ?? '');
            $durationSeconds = $durationFrom1C !== '' ? $this->parseIsoDurationToSeconds($durationFrom1C) : self::DEFAULT_DURATION;

            if (!isset($schedule[$clinicUid][$specialtyUid][$employeeUid])) {
                $schedule[$clinicUid][$specialtyUid][$employeeUid] = $this->attachExtraFields([
                    'specialtyName' => $specialtyName,
                    'employeeName' => $this->stringValue($item['СотрудникФИО'] ?? ''),
                    'durationFrom1C' => $durationFrom1C,
                    'durationInSeconds' => $durationSeconds,
                    'timetable' => [
                        'freeFormatted' => [],
                        'busy' => [],
                        'free' => [],
                    ],
                ], $item, self::KNOWN_KEYS);
            }

            $periods = is_array($item['ПериодыГрафика'] ?? null) ? $item['ПериодыГрафика'] : [];
            $freeTime = $periods['СвободноеВремя']['ПериодГрафика'] ?? [];
            $busyTime = $periods['ЗанятоеВремя']['ПериодГрафика'] ?? [];

            $existing = $schedule[$clinicUid][$specialtyUid][$employeeUid]['timetable'];
            $schedule[$clinicUid][$specialtyUid][$employeeUid]['timetable'] = [
                'free' => array_merge($existing['free'], $this->formatTimetable($freeTime, 0, true)),
                'busy' => array_merge($existing['busy'], $this->formatTimetable($busyTime, 0, true)),
                'freeFormatted' => array_merge($existing['freeFormatted'], $this->formatTimetable($freeTime, $durationSeconds, false)),
            ];

            unset($item, $periods, $freeTime, $busyTime, $existing);
        }

        $reader->close();
        $this->failIfErrorMessage($errorMessage !== '' ? $errorMessage : $parameterError);

        return $schedule;
    }

    private function parseIsoDurationToSeconds(string $value): int
    {
        $timestamp = strtotime($value);

        return $timestamp === false ? self::DEFAULT_DURATION : ((int) date('H', $timestamp) * 3600) + ((int) date('i', $timestamp) * 60);
    }

    private function formatTimetable(mixed $items, int $duration, bool $useDefaultInterval): array
    {
        $items = $this->listify($items);
        if ($items === []) {
            return [];
        }

        if ($duration <= 0) {
            $duration = self::DEFAULT_DURATION;
        }

        $formatted = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $date = $this->stringValue($item['Дата'] ?? '');
            $start = strtotime($this->stringValue($item['ВремяНачала'] ?? ''));
            $end = strtotime($this->stringValue($item['ВремяОкончания'] ?? ''));
            if ($date === '' || $start === false || $end === false || $end <= $start) {
                continue;
            }

            $formattedDate = date('d-m-Y', strtotime($date));
            $slots = [];

            if ($useDefaultInterval) {
                $slots[] = $this->buildSlot($item, $start, $end);
            } else {
                $parts = max(1, (int) round(($end - $start) / $duration));
                for ($index = 0; $index < $parts; $index++) {
                    $slotStart = $start + ($duration * $index);
                    $slotEnd = min($end, $start + ($duration * ($index + 1)));
                    if ($slotEnd <= $slotStart) {
                        continue;
                    }
                    $slots[] = $this->buildSlot($item, $slotStart, $slotEnd);
                }
            }

            foreach ($slots as $slot) {
                $formatted[$formattedDate][] = $slot;
            }
        }

        return $formatted;
    }

    private function buildSlot(array $item, int $start, int $end): array
    {
        $date = $this->stringValue($item['Дата'] ?? '');

        return [
            'typeOfTimeUid' => $this->stringValue($item['ВидВремени'] ?? ''),
            'date' => $date,
            'timeBegin' => date('Y-m-d', $start) . 'T' . date('H:i:s', $start),
            'timeEnd' => date('Y-m-d', $end) . 'T' . date('H:i:s', $end),
            'formattedDate' => date('d-m-Y', strtotime($date)),
            'formattedTimeBegin' => date('H:i', $start),
            'formattedTimeEnd' => date('H:i', $end),
            '_extra' => $this->extractExtraFields($item, self::PERIOD_KNOWN_KEYS),
        ];
    }
}
