<?php

namespace ANZ\BitUmc\SDK;

use ANZ\BitUmc\SDK\Domain\Exception\UnsupportedTransportException;
use ANZ\BitUmc\SDK\Domain\Request\BookAppointmentRequest;
use ANZ\BitUmc\SDK\Domain\Request\ReserveRequest;
use ANZ\BitUmc\SDK\Domain\Request\ScheduleQuery;
use ANZ\BitUmc\SDK\Domain\Request\WaitListRequest;
use ANZ\BitUmc\SDK\Infrastructure\Soap\SoapOperation;
use ANZ\BitUmc\SDK\Infrastructure\Soap\SoapMethod;
use ANZ\BitUmc\SDK\Infrastructure\Soap\SoapRequestMapper;
use ANZ\BitUmc\SDK\Infrastructure\Soap\SoapResponseParser;
use ANZ\BitUmc\SDK\Infrastructure\Soap\Transport\SoapTransport;
use ANZ\BitUmc\SDK\Infrastructure\Soap\Transport\StreamingSoapTransport;
use ANZ\BitUmc\SDK\Transport\ConnectionOptions;
use ANZ\BitUmc\SDK\Transport\TransportType;

final class BitUmcClient
{
    private readonly SoapTransport $soapTransport;
    private readonly StreamingSoapTransport $streamingSoapTransport;
    private readonly SoapRequestMapper $soapRequestMapper;
    private readonly SoapResponseParser $soapResponseParser;

    /**
     * @throws \SoapFault
     */
    public function __construct(
        private readonly TransportType $transportType,
        private readonly ConnectionOptions $options,
    ) {
        if ($this->transportType !== TransportType::SOAP) {
            throw new UnsupportedTransportException('HTTP transport is reserved in the new architecture but is not implemented yet.');
        }

        $this->soapTransport = new SoapTransport($this->options);
        $this->streamingSoapTransport = new StreamingSoapTransport($this->options);
        $this->soapRequestMapper = new SoapRequestMapper();
        $this->soapResponseParser = new SoapResponseParser();
    }

    public function getClinics(): array
    {
        return $this->dispatch($this->soapRequestMapper->getClinics());
    }

    public function iterateClinics(): \Generator
    {
        yield from $this->dispatchIterable($this->soapRequestMapper->getClinics());
    }

    public function getEmployees(): array
    {
        return $this->dispatch($this->soapRequestMapper->getEmployees());
    }

    public function iterateEmployees(): \Generator
    {
        yield from $this->dispatchIterable($this->soapRequestMapper->getEmployees());
    }

    public function getNomenclature(string $clinicUid): array
    {
        return $this->dispatch($this->soapRequestMapper->getNomenclature($clinicUid));
    }

    public function iterateNomenclature(string $clinicUid): \Generator
    {
        yield from $this->dispatchIterable($this->soapRequestMapper->getNomenclature($clinicUid));
    }

    public function getSchedule(?ScheduleQuery $query = null): array
    {
        return $this->dispatch($this->soapRequestMapper->getSchedule($query ?? new ScheduleQuery()));
    }

    public function iterateSchedule(?ScheduleQuery $query = null): \Generator
    {
        yield from $this->dispatchIterable($this->soapRequestMapper->getSchedule($query ?? new ScheduleQuery()));
    }

    public function getAppointmentStatus(string $appointmentUid): array
    {
        return $this->dispatch($this->soapRequestMapper->getAppointmentStatus($appointmentUid));
    }

    public function sendReserve(ReserveRequest $request): array
    {
        return $this->dispatch($this->soapRequestMapper->sendReserve($request));
    }

    public function sendWaitList(WaitListRequest $request): array
    {
        return $this->dispatch($this->soapRequestMapper->sendWaitList($request));
    }

    public function sendAppointment(BookAppointmentRequest $request): array
    {
        return $this->dispatch($this->soapRequestMapper->sendAppointment($request));
    }

    public function deleteAppointment(string $appointmentUid): array
    {
        return $this->dispatch($this->soapRequestMapper->deleteAppointment($appointmentUid));
    }

    private function dispatch(SoapOperation $operation): array
    {
        if (in_array($operation->method, [SoapMethod::GET_CLINICS, SoapMethod::GET_EMPLOYEES, SoapMethod::GET_NOMENCLATURE, SoapMethod::GET_SCHEDULE], true)) {
            return $this->soapResponseParser->parsePayload($operation->method, $this->streamingSoapTransport->execute($operation));
        }

        $response = $this->soapTransport->execute($operation);

        return $this->soapResponseParser->parse($operation->method, $response);
    }

    private function dispatchIterable(SoapOperation $operation): \Generator
    {
        yield from $this->soapResponseParser->iteratePayload($operation->method, $this->streamingSoapTransport->execute($operation));
    }
}
