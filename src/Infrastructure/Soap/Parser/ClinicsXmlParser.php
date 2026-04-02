<?php

namespace ANZ\BitUmc\SDK\Infrastructure\Soap\Parser;

final class ClinicsXmlParser extends AbstractSoapXmlParser
{
    public function parse(string $xml): array
    {
        $reader = $this->elementReader->createReader($xml);
        $clinics = [];
        $errorMessage = '';

        while ($reader->read()) {
            if ($reader->nodeType !== \XMLReader::ELEMENT) {
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

            $clinics[$uid] = [
                'uid' => $uid,
                'name' => $this->stringValue($item['Наименование'] ?? ''),
            ];
        }

        $reader->close();
        $this->failIfErrorMessage($errorMessage);

        return $clinics;
    }
}
