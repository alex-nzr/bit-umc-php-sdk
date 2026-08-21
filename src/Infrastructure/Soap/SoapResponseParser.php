<?php

namespace ANZ\BitUmc\SDK\Infrastructure\Soap;

use ANZ\BitUmc\SDK\Domain\Exception\RemoteServiceException;
use ANZ\BitUmc\SDK\Domain\Exception\ResponseParseException;
use ANZ\BitUmc\SDK\Infrastructure\Soap\Parser\AppointmentStatusXmlParser;
use ANZ\BitUmc\SDK\Infrastructure\Soap\Parser\ClinicsXmlParser;
use ANZ\BitUmc\SDK\Infrastructure\Soap\Parser\CommonResultXmlParser;
use ANZ\BitUmc\SDK\Infrastructure\Soap\Parser\EmployeesXmlParser;
use ANZ\BitUmc\SDK\Infrastructure\Soap\Parser\NomenclatureXmlParser;
use ANZ\BitUmc\SDK\Infrastructure\Soap\Parser\ScheduleXmlParser;
use ANZ\BitUmc\SDK\Infrastructure\Soap\Transport\Stream\SoapPayload;

final class SoapResponseParser
{
    /**
     * @return \Generator<mixed, array<string, mixed>, mixed, void>
     */
    public function iterate(SoapMethod $method, mixed $response): \Generator
    {
        $payload = $this->getXmlPayload($method, $response);

        yield from match ($method) {
            SoapMethod::GET_CLINICS => (new ClinicsXmlParser())->iterate($payload),
            SoapMethod::GET_EMPLOYEES => (new EmployeesXmlParser())->iterate($payload),
            SoapMethod::GET_NOMENCLATURE => (new NomenclatureXmlParser())->iterate($payload),
            SoapMethod::GET_SCHEDULE => (new ScheduleXmlParser())->iterate($payload),
            default => throw new ResponseParseException(sprintf('SOAP method %s does not return an iterable collection.', $method->value)),
        };
    }

    /**
     * @return \Generator<mixed, array<string, mixed>, mixed, void>
     */
    public function iteratePayload(SoapMethod $method, SoapPayload $payload): \Generator
    {
        yield from match ($method) {
            SoapMethod::GET_CLINICS => (new ClinicsXmlParser())->iterateFile($payload->getPath()),
            SoapMethod::GET_EMPLOYEES => (new EmployeesXmlParser())->iterateFile($payload->getPath()),
            SoapMethod::GET_NOMENCLATURE => (new NomenclatureXmlParser())->iterateFile($payload->getPath()),
            SoapMethod::GET_SCHEDULE => (new ScheduleXmlParser())->iterateFile($payload->getPath()),
            default => throw new ResponseParseException(sprintf('SOAP method %s does not return an iterable collection.', $method->value)),
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function parsePayload(SoapMethod $method, SoapPayload $payload): array
    {
        return match ($method) {
            SoapMethod::GET_CLINICS => (new ClinicsXmlParser())->parseFile($payload->getPath()),
            SoapMethod::GET_EMPLOYEES => (new EmployeesXmlParser())->parseFile($payload->getPath()),
            SoapMethod::GET_NOMENCLATURE => (new NomenclatureXmlParser())->parseFile($payload->getPath()),
            SoapMethod::GET_SCHEDULE => (new ScheduleXmlParser())->parseFile($payload->getPath()),
            default => $this->parse($method, (object) ['return' => $payload->read()]),
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function parse(SoapMethod $method, mixed $response): array
    {
        if (!is_object($response) || !property_exists($response, 'return')) {
            throw new ResponseParseException('Unexpected response format returned from 1C.');
        }

        if (!is_string($response->return)) {
            throw new ResponseParseException('Unexpected SOAP payload type returned from 1C.');
        }

        $payload = trim($response->return);

        if ($payload === 'Ok') {
            return ['success' => true];
        }

        if ($payload === 'Error') {
            throw new RemoteServiceException(sprintf('1C returned an unknown error to the request %s.', $method->value));
        }

        if ($payload === '') {
            throw new ResponseParseException('Unexpected SOAP payload type returned from 1C.');
        }

        if (!str_starts_with($payload, '<')) {
            throw new RemoteServiceException($payload);
        }

        return match ($method) {
            SoapMethod::GET_CLINICS => (new ClinicsXmlParser())->parse($payload),
            SoapMethod::GET_EMPLOYEES => (new EmployeesXmlParser())->parse($payload),
            SoapMethod::GET_NOMENCLATURE => (new NomenclatureXmlParser())->parse($payload),
            SoapMethod::GET_SCHEDULE => (new ScheduleXmlParser())->parse($payload),
            SoapMethod::CREATE_RESERVE,
            SoapMethod::CREATE_APPOINTMENT,
            SoapMethod::CREATE_WAIT_LIST,
            SoapMethod::DELETE_APPOINTMENT => (new CommonResultXmlParser())->parse($payload),
            SoapMethod::GET_APPOINTMENT_STATUS => (new AppointmentStatusXmlParser())->parse($payload),
        };
    }

    private function getXmlPayload(SoapMethod $method, mixed $response): string
    {
        if (!is_object($response) || !property_exists($response, 'return') || !is_string($response->return)) {
            throw new ResponseParseException('Unexpected SOAP payload type returned from 1C.');
        }

        $payload = $response->return;
        $payloadLength = strlen($payload);
        for ($index = 0; $index < $payloadLength; $index++) {
            $character = $payload[$index];
            if (!ctype_space($character)) {
                if ($character === '<') {
                    return $index === 0 ? $payload : substr($payload, $index);
                }

                break;
            }
        }

        throw new ResponseParseException(sprintf('SOAP method %s did not return an XML collection payload.', $method->value));
    }
}
