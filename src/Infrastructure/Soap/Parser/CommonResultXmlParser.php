<?php

namespace ANZ\BitUmc\SDK\Infrastructure\Soap\Parser;

final class CommonResultXmlParser extends AbstractSoapXmlParser
{
    public function parse(string $xml): array
    {
        $reader = $this->elementReader->createReader($xml);
        $result = null;
        $errorMessage = '';
        $uid = '';

        while ($reader->read()) {
            if ($reader->nodeType !== \XMLReader::ELEMENT) {
                continue;
            }

            if ($reader->localName === 'Результат') {
                $result = $this->stringValue($this->elementReader->readCurrentElementValue($reader));
                continue;
            }

            if ($reader->localName === 'ОписаниеОшибки') {
                $errorMessage = $this->stringValue($this->elementReader->readCurrentElementValue($reader));
                continue;
            }

            if ($reader->localName === 'УИД') {
                $uid = $this->stringValue($this->elementReader->readCurrentElementValue($reader));
            }
        }

        $reader->close();

        if (!$this->isTruthy($result)) {
            $this->failIfErrorMessage($errorMessage !== '' ? $errorMessage : 'Remote service returned an unsuccessful result.');
        }

        return $uid !== '' ? ['uid' => $uid] : ['success' => true];
    }
}
