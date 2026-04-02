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

final class SoapResponseParser
{
    public function parse(SoapMethod $method, mixed $response): array
    {
        if (!is_object($response) || !property_exists($response, 'return')) {
            throw new ResponseParseException('Unexpected response format returned from 1C.');
        }

        if ($response->return === 'Ok') {
            return ['success' => true];
        }

        if ($response->return === 'Error') {
            throw new RemoteServiceException(sprintf('1C returned an unknown error to the request %s.', $method->value));
        }

        if (!is_string($response->return) || trim($response->return) === '') {
            throw new ResponseParseException('Unexpected SOAP payload type returned from 1C.');
        }

        if (!str_starts_with(ltrim($response->return), '<')) {
            throw new RemoteServiceException(trim($response->return));
        }

        return match ($method) {
            SoapMethod::GET_CLINICS => (new ClinicsXmlParser())->parse($response->return),
            SoapMethod::GET_EMPLOYEES => (new EmployeesXmlParser())->parse($response->return),
            SoapMethod::GET_NOMENCLATURE => (new NomenclatureXmlParser())->parse($response->return),
            SoapMethod::GET_SCHEDULE => (new ScheduleXmlParser())->parse($response->return),
            SoapMethod::CREATE_RESERVE,
            SoapMethod::CREATE_APPOINTMENT,
            SoapMethod::CREATE_WAIT_LIST,
            SoapMethod::DELETE_APPOINTMENT => (new CommonResultXmlParser())->parse($response->return),
            SoapMethod::GET_APPOINTMENT_STATUS => (new AppointmentStatusXmlParser())->parse($response->return),
        };
    }
}
