<?php

namespace ANZ\BitUmc\SDK\Infrastructure\Soap\Parser;

use Generator;
use XMLReader;

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

    /**
     * @return array<string, mixed>
     */
    public function parse(string $xml): array
    {
        return $this->aggregate($this->iterate($xml));
    }

    /**
     * @return Generator<int, array<string, mixed>, mixed, void>
     */
    public function iterate(string $xml): Generator
    {
        $errorReader = $this->elementReader->createReader($xml);
        $metadataReader = $this->elementReader->createReader($xml);
        $periodReader = $this->elementReader->createReader($xml);

        try {
            $this->failIfErrorMessage($this->readErrorMessage($errorReader));

            yield from $this->iterateReader($metadataReader, $periodReader);
        } finally {
            $errorReader->close();
            $metadataReader->close();
            $periodReader->close();
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function parseFile(string $path): array
    {
        return $this->aggregate($this->iterateFile($path));
    }

    /**
     * @return Generator<int, array<string, mixed>, mixed, void>
     */
    public function iterateFile(string $path): Generator
    {
        $errorReader = $this->elementReader->createReaderFromFile($path);
        $metadataReader = $this->elementReader->createReaderFromFile($path);
        $periodReader = $this->elementReader->createReaderFromFile($path);

        try {
            $this->failIfErrorMessage($this->readErrorMessage($errorReader));

            yield from $this->iterateReader($metadataReader, $periodReader);
        } finally {
            $errorReader->close();
            $metadataReader->close();
            $periodReader->close();
        }
    }

    /**
     * @return Generator<int, array<string, mixed>, mixed, void>
     */
    private function iterateReader(XMLReader $metadataReader, XMLReader $periodReader): Generator
    {
        $recordIndex = 0;

        while ($this->moveToNextSchedule($metadataReader) && $this->moveToNextSchedule($periodReader)) {
            $metadata = $this->readScheduleMetadata($metadataReader);

            if ($metadata === null) {
                $this->skipCurrentElement($periodReader);
                continue;
            }

            yield $recordIndex++ => $this->buildEmployeeRecord($metadata);

            foreach ($this->iterateSchedulePeriods($periodReader, $metadata) as $record) {
                yield $recordIndex++ => $record;
            }
        }
    }

    private function moveToNextSchedule(XMLReader $reader): bool
    {
        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::ELEMENT && $reader->localName === 'ГрафикДляСайта') {
                return true;
            }
        }

        return false;
    }

    private function readErrorMessage(XMLReader $reader): string
    {
        $errorMessage = '';
        $parameterError = '';

        while ($reader->read()) {
            if ($reader->nodeType !== XMLReader::ELEMENT) {
                continue;
            }

            if ($reader->localName === 'ОписаниеОшибки') {
                $errorMessage = $this->stringValue($this->elementReader->readCurrentElementValue($reader));
                continue;
            }

            if ($reader->localName === 'ОшибкаПараметров') {
                $parameterError = $this->stringValue($this->elementReader->readCurrentElementValue($reader));
            }
        }

        return $errorMessage !== '' ? $errorMessage : $parameterError;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function readScheduleMetadata(XMLReader $reader): ?array
    {
        $depth = $reader->depth;
        $source = [];

        if ($reader->isEmptyElement) {
            return null;
        }

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::END_ELEMENT && $reader->depth === $depth && $reader->localName === 'ГрафикДляСайта') {
                break;
            }

            if ($reader->nodeType !== XMLReader::ELEMENT || $reader->depth !== $depth + 1) {
                continue;
            }

            if ($reader->localName === 'ПериодыГрафика') {
                $this->skipCurrentElement($reader);
                continue;
            }

            $source[$reader->localName] = $this->elementReader->readCurrentElementValue($reader);
        }

        $clinicUid = $this->stringValue($source['Клиника'] ?? '');
        $employeeUid = $this->stringValue($source['СотрудникID'] ?? '');
        if ($clinicUid === '' || $employeeUid === '') {
            return null;
        }

        $specialtyName = $this->stringValue($source['Специализация'] ?? '');
        if ($specialtyName === '') {
            $specialtyName = 'Без основной специализации';
        }

        $durationFrom1C = $this->stringValue($source['ДлительностьПриема'] ?? '');

        return [
            'clinicUid' => $clinicUid,
            'employeeUid' => $employeeUid,
            'specialtyName' => $specialtyName,
            'specialtyUid' => $this->buildSpecialtyUid($specialtyName),
            'employeeName' => $this->stringValue($source['СотрудникФИО'] ?? ''),
            'durationFrom1C' => $durationFrom1C,
            'durationInSeconds' => $durationFrom1C !== '' ? $this->parseIsoDurationToSeconds($durationFrom1C) : self::DEFAULT_DURATION,
            'source' => $source,
        ];
    }

    /**
     * @param array<string, mixed> $metadata
     * @return Generator<int, array<string, mixed>, mixed, void>
     */
    private function iterateSchedulePeriods(XMLReader $reader, array $metadata): Generator
    {
        $depth = $reader->depth;

        if ($reader->isEmptyElement) {
            return;
        }

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::END_ELEMENT && $reader->depth === $depth && $reader->localName === 'ГрафикДляСайта') {
                break;
            }

            if ($reader->nodeType !== XMLReader::ELEMENT || $reader->depth !== $depth + 2) {
                continue;
            }

            $timeType = match ($reader->localName) {
                'СвободноеВремя' => 'free',
                'ЗанятоеВремя' => 'busy',
                default => null,
            };

            if ($timeType === null) {
                continue;
            }

            yield from $this->readTimeTypePeriods($reader, $metadata, $timeType);
        }
    }

    /**
     * @param array<string, mixed> $metadata
     * @return Generator<int, array<string, mixed>, mixed, void>
     */
    private function readTimeTypePeriods(XMLReader $reader, array $metadata, string $timeType): Generator
    {
        $depth = $reader->depth;

        if ($reader->isEmptyElement) {
            return;
        }

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::END_ELEMENT && $reader->depth === $depth) {
                break;
            }

            if ($reader->nodeType !== XMLReader::ELEMENT || $reader->localName !== 'ПериодГрафика') {
                continue;
            }

            $period = $this->elementReader->readCurrentElementValue($reader);
            if (is_array($period)) {
                yield $this->buildPeriodRecord($metadata, $timeType, $period);
            }
        }
    }

    private function skipCurrentElement(XMLReader $reader): void
    {
        $depth = $reader->depth;

        if ($reader->isEmptyElement) {
            return;
        }

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::END_ELEMENT && $reader->depth === $depth) {
                break;
            }
        }
    }

    /**
     * @param iterable<int, array<string, mixed>> $records
     * @return array<string, mixed>
     */
    private function aggregate(iterable $records): array
    {
        $schedule = [];

        foreach ($records as $record) {
            $employee =& $this->ensureEmployee($schedule, $record);

            if ($record['kind'] !== 'period') {
                unset($employee);
                continue;
            }

            $existing = $employee['timetable'];
            if ($record['timeType'] === 'free') {
                $employee['timetable'] = [
                    'free' => array_merge($existing['free'], $record['period']),
                    'busy' => $existing['busy'],
                    'freeFormatted' => array_merge($existing['freeFormatted'], $record['formattedSlots']),
                ];
            }

            if ($record['timeType'] === 'busy') {
                $employee['timetable'] = [
                    'free' => $existing['free'],
                    'busy' => array_merge($existing['busy'], $record['period']),
                    'freeFormatted' => $existing['freeFormatted'],
                ];
            }

            unset($employee);
        }

        return $schedule;
    }

    /**
     * @param array<mixed> $schedule
     * @param array<string, mixed> $record
     * @return array<string, mixed>
     */
    private function &ensureEmployee(array &$schedule, array $record): array
    {
        $clinicUid = $record['clinicUid'];
        $specialtyUid = $record['specialtyUid'];
        $employeeUid = $record['employeeUid'];

        if (!isset($schedule[$clinicUid][$specialtyUid][$employeeUid])) {
            $schedule[$clinicUid][$specialtyUid][$employeeUid] = $record['employee'];
        }

        return $schedule[$clinicUid][$specialtyUid][$employeeUid];
    }

    /**
     * @param array<string, mixed> $metadata
     * @return array<string, mixed>
     */
    private function buildEmployeeRecord(array $metadata): array
    {
        return [
            'kind' => 'employee',
            'clinicUid' => $metadata['clinicUid'],
            'specialtyUid' => $metadata['specialtyUid'],
            'employeeUid' => $metadata['employeeUid'],
            'employee' => $this->buildEmployee($metadata),
        ];
    }

    /**
     * @param array<string, mixed> $metadata
     * @param array<string, mixed> $period
     * @return array<string, mixed>
     */
    private function buildPeriodRecord(array $metadata, string $timeType, array $period): array
    {
        return [
            'kind' => 'period',
            'clinicUid' => $metadata['clinicUid'],
            'specialtyUid' => $metadata['specialtyUid'],
            'employeeUid' => $metadata['employeeUid'],
            'employee' => $this->buildEmployee($metadata),
            'timeType' => $timeType,
            'period' => $this->formatTimetable($period, 0, true),
            'formattedSlots' => $timeType === 'free' ? $this->formatTimetable($period, $metadata['durationInSeconds'], false) : [],
        ];
    }

    /**
     * @param array<string, mixed> $metadata
     * @return array<string, mixed>
     */
    private function buildEmployee(array $metadata): array
    {
        return $this->attachExtraFields([
            'specialtyName' => $metadata['specialtyName'],
            'employeeName' => $metadata['employeeName'],
            'durationFrom1C' => $metadata['durationFrom1C'],
            'durationInSeconds' => $metadata['durationInSeconds'],
            'timetable' => [
                'freeFormatted' => [],
                'busy' => [],
                'free' => [],
            ],
        ], $metadata['source'], self::KNOWN_KEYS);
    }

    private function buildSpecialtyUid(string $specialtyName): string
    {
        return preg_replace('/[^a-z0-9\s]/', '', strtolower(base64_encode($specialtyName))) ?? '';
    }

    private function parseIsoDurationToSeconds(string $value): int
    {
        $timestamp = strtotime($value);

        return $timestamp === false ? self::DEFAULT_DURATION : ((int) date('H', $timestamp) * 3600) + ((int) date('i', $timestamp) * 60);
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
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

    /**
     * @param array<string, mixed> $item
     * @return array<string, mixed>
     */
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
