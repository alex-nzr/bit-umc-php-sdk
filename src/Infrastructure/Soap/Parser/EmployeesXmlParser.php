<?php

namespace ANZ\BitUmc\SDK\Infrastructure\Soap\Parser;

final class EmployeesXmlParser extends AbstractSoapXmlParser
{
    private const KNOWN_KEYS = [
        'UID',
        'Имя',
        'Фамилия',
        'Отчество',
        'Специализация',
        'Организация',
        'Фото',
        'КраткоеОписание',
        'СреднийРейтинг',
        'ОсновныеУслуги',
    ];

    private const SERVICE_KNOWN_KEYS = [
        'UID',
        'Продолжительность',
    ];

    public function parse(string $xml): array
    {
        $reader = $this->elementReader->createReader($xml);
        $employees = [];
        $errorMessage = '';

        while ($reader->read()) {
            if ($reader->nodeType !== \XMLReader::ELEMENT) {
                continue;
            }

            if ($reader->localName === 'ОписаниеОшибки') {
                $errorMessage = $this->stringValue($this->elementReader->readCurrentElementValue($reader));
                continue;
            }

            if ($reader->localName !== 'Сотрудник') {
                continue;
            }

            $item = $this->elementReader->readCurrentElementValue($reader);
            if (!is_array($item)) {
                continue;
            }

            $uid = $this->stringValue($item['UID'] ?? '');
            if ($uid === '') {
                continue;
            }

            $specialtyName = $this->stringValue($item['Специализация'] ?? '');
            if ($specialtyName === '') {
                $specialtyName = 'Без основной специализации';
            }

            $employee = [
                'uid' => $uid,
                'name' => $this->stringValue($item['Имя'] ?? ''),
                'surname' => $this->stringValue($item['Фамилия'] ?? ''),
                'middleName' => $this->stringValue($item['Отчество'] ?? ''),
                'clinicUid' => $this->normalizeClinicUid($item['Организация'] ?? ''),
                'photo' => $this->stringValue($item['Фото'] ?? ''),
                'description' => $this->stringValue($item['КраткоеОписание'] ?? ''),
                'rating' => $this->stringValue($item['СреднийРейтинг'] ?? ''),
                'specialtyName' => $specialtyName,
                'specialtyUid' => $this->buildSpecialtyUid($specialtyName),
                'services' => [],
            ];
            $employee['fullName'] = trim($employee['surname'] . ' ' . $employee['name'] . ' ' . $employee['middleName']);

            $services = $item['ОсновныеУслуги']['ОсновнаяУслуга'] ?? [];
            foreach ($this->listify($services) as $service) {
                if (!is_array($service)) {
                    continue;
                }

                $serviceUid = $this->stringValue($service['UID'] ?? '');
                if ($serviceUid === '') {
                    continue;
                }

                $employee['services'][$serviceUid] = $this->attachExtraFields([
                    'uid' => $serviceUid,
                    'personalDuration' => $this->parseIsoDurationToSeconds($this->stringValue($service['Продолжительность'] ?? '')),
                ], $service, self::SERVICE_KNOWN_KEYS);
            }

            $employees[$uid] = $this->attachExtraFields($employee, $item, self::KNOWN_KEYS);
            unset($item, $employee);
        }

        $reader->close();
        $this->failIfErrorMessage($errorMessage);

        return $employees;
    }

    private function normalizeClinicUid(mixed $value): string
    {
        $uid = $this->stringValue($value);

        return $uid === '00000000-0000-0000-0000-000000000000' ? '' : $uid;
    }

    private function buildSpecialtyUid(string $specialtyName): string
    {
        return preg_replace('/[^a-z0-9\s]/', '', strtolower(base64_encode($specialtyName))) ?? '';
    }

    private function parseIsoDurationToSeconds(string $value): int
    {
        if ($value === '') {
            return 0;
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return 0;
        }

        return ((int) date('H', $timestamp) * 3600) + ((int) date('i', $timestamp) * 60);
    }
}
