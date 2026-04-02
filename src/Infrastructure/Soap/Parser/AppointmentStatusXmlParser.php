<?php

namespace ANZ\BitUmc\SDK\Infrastructure\Soap\Parser;

use ANZ\BitUmc\SDK\Domain\Exception\RemoteServiceException;

final class AppointmentStatusXmlParser extends AbstractSoapXmlParser
{
    public function parse(string $xml): array
    {
        $reader = $this->elementReader->createReader($xml);
        $statusId = '';
        $statusTitle = '';
        $errorMessage = '';

        while ($reader->read()) {
            if ($reader->nodeType !== \XMLReader::ELEMENT) {
                continue;
            }

            if ($reader->localName === 'Результат') {
                $statusId = $this->stringValue($this->elementReader->readCurrentElementValue($reader));
                continue;
            }

            if ($reader->localName === 'ОписаниеРезультата') {
                $statusTitle = $this->stringValue($this->elementReader->readCurrentElementValue($reader));
                continue;
            }

            if ($reader->localName === 'ОписаниеОшибки') {
                $errorMessage = $this->stringValue($this->elementReader->readCurrentElementValue($reader));
            }
        }

        $reader->close();

        if ((int) $statusId <= 0) {
            throw new RemoteServiceException(trim($statusId . ' - ' . $errorMessage));
        }

        if ((int) $statusId === 9) {
            $statusTitle = 'Забронирована';
        }

        return [
            'statusId' => $statusId,
            'statusTitle' => $statusTitle,
        ];
    }
}
