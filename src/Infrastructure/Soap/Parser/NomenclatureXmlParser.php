<?php

namespace ANZ\BitUmc\SDK\Infrastructure\Soap\Parser;

use XMLReader;

final class NomenclatureXmlParser extends AbstractSoapXmlParser
{
    private const KNOWN_KEYS = [
        'UID',
        'Наименование',
        'Вид',
        'Артикул',
        'Цена',
        'Продолжительность',
        'БазоваяЕдиницаИзмерения',
        'Родитель',
        'ЭтоПапка',
    ];

    public function parse(string $xml): array
    {
        $reader = $this->elementReader->createReader($xml);
        $items = [];
        $errorMessage = '';
        $resultValue = null;

        while ($reader->read()) {
            if ($reader->nodeType !== XMLReader::ELEMENT) {
                continue;
            }

            if ($reader->localName === 'ОписаниеОшибки') {
                $errorMessage = $this->stringValue($this->elementReader->readCurrentElementValue($reader));
                continue;
            }

            if ($reader->localName === 'Результат') {
                $resultValue = $this->stringValue($this->elementReader->readCurrentElementValue($reader));
                continue;
            }

            if ($reader->localName !== 'Каталог') {
                continue;
            }

            $item = $this->elementReader->readCurrentElementValue($reader);
            if (!is_array($item) || $this->isTruthy($item['ЭтоПапка'] ?? false)) {
                continue;
            }

            $uid = $this->stringValue($item['UID'] ?? '');
            if ($uid === '') {
                continue;
            }

            $items[$uid] = $this->attachExtraFields([
                'uid' => $uid,
                'name' => $this->stringValue($item['Наименование'] ?? ''),
                'typeOfItem' => $this->stringValue($item['Вид'] ?? ''),
                'artNumber' => $this->stringValue($item['Артикул'] ?? ''),
                'price' => preg_replace('/[^0-9]/', '', $this->stringValue($item['Цена'] ?? '')) ?? '',
                'duration' => $this->parseIsoDurationToSeconds($this->stringValue($item['Продолжительность'] ?? '')),
                'measureUnit' => $this->stringValue($item['БазоваяЕдиницаИзмерения'] ?? ''),
                'parent' => $this->stringValue($item['Родитель'] ?? ''),
            ], $item, self::KNOWN_KEYS);
            unset($item);
        }

        $reader->close();

        if ($resultValue !== null && !$this->isTruthy($resultValue)) {
            $this->failIfErrorMessage($errorMessage);
        }

        return $items;
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
