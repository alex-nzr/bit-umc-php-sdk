<?php

namespace ANZ\BitUmc\SDK\Infrastructure\Soap\Parser;

use XMLReader;

final class ClinicsXmlParser extends AbstractSoapXmlParser
{
    private const KNOWN_KEYS = [
        'УИД',
        'Наименование',
    ];

    public function parse(string $xml): array
    {
        return iterator_to_array($this->iterate($xml));
    }

    public function iterate(string $xml): \Generator
    {
        $reader = $this->elementReader->createReader($xml);

        yield from $this->iterateReader($reader);
    }

    public function parseFile(string $path): array
    {
        return iterator_to_array($this->iterateFile($path));
    }

    public function iterateFile(string $path): \Generator
    {
        $reader = $this->elementReader->createReaderFromFile($path);

        yield from $this->iterateReader($reader);
    }

    private function iterateReader(XMLReader $reader): \Generator
    {
        $errorMessage = '';

        try
        {
            while ($reader->read())
            {
                if ($reader->nodeType !== XMLReader::ELEMENT) {
                    continue;
                }

                if ($reader->localName === 'ОписаниеОшибки') {
                    $errorMessage = $this->stringValue($this->elementReader->readCurrentElementValue($reader));
                    continue;
                }

                if ($reader->localName !== 'Клиника') {
                    continue;
                }

                $item = $this->elementReader->readCurrentElementValue($reader);
                if (!is_array($item)) {
                    continue;
                }

                $uid = $this->stringValue($item['УИД'] ?? '');
                if ($uid === '') {
                    continue;
                }

                yield $uid => $this->attachExtraFields([
                    'uid' => $uid,
                    'name' => $this->stringValue($item['Наименование'] ?? ''),
                ], $item, self::KNOWN_KEYS);
            }
        }
        finally
        {
            $reader->close();
        }
        $this->failIfErrorMessage($errorMessage);
    }
}
