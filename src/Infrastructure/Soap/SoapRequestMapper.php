<?php

namespace ANZ\BitUmc\SDK\Infrastructure\Soap;

use ANZ\BitUmc\SDK\Domain\Request\BookAppointmentRequest;
use ANZ\BitUmc\SDK\Domain\Request\ReserveRequest;
use ANZ\BitUmc\SDK\Domain\Request\ScheduleQuery;
use ANZ\BitUmc\SDK\Domain\Request\WaitListRequest;
use ANZ\BitUmc\SDK\Tools\DateFormatter;
use ANZ\BitUmc\SDK\Tools\PhoneFormatter;
use DateTimeImmutable;
use DateTimeInterface;
use stdClass;

final class SoapRequestMapper
{
    public function getClinics(): SoapOperation
    {
        return new SoapOperation(SoapMethod::GET_CLINICS, new stdClass());
    }

    public function getEmployees(): SoapOperation
    {
        return new SoapOperation(SoapMethod::GET_EMPLOYEES, new stdClass());
    }

    public function getNomenclature(string $clinicUid): SoapOperation
    {
        $payload = new stdClass();
        $payload->Clinic = $clinicUid;

        return new SoapOperation(SoapMethod::GET_NOMENCLATURE, $payload);
    }

    public function getSchedule(ScheduleQuery $query): SoapOperation
    {
        $payload = new stdClass();
        $startDate = $query->startDate ?? new DateTimeImmutable('tomorrow');
        $payload->StartDate = $this->formatDateTime($startDate);
        $finishDate = DateTimeImmutable::createFromInterface($startDate)->modify('+' . $query->days . ' days');
        $payload->FinishDate = $this->formatDateTime($finishDate);
        $payload->Params = [];

        if ($query->clinicUid !== '') {
            $payload->Params[] = new SoapParameter('Clinic', $query->clinicUid);
        }

        $employeeUids = array_values(array_filter($query->employeeUids, static fn (mixed $value): bool => is_string($value) && $value !== ''));
        if ($employeeUids !== []) {
            $payload->Params[] = new SoapParameter('Employees', implode(';', $employeeUids));
        }

        return new SoapOperation(SoapMethod::GET_SCHEDULE, $payload);
    }

    public function getAppointmentStatus(string $appointmentUid): SoapOperation
    {
        $payload = new stdClass();
        $payload->GUID = $appointmentUid;

        return new SoapOperation(SoapMethod::GET_APPOINTMENT_STATUS, $payload);
    }

    public function sendReserve(ReserveRequest $request): SoapOperation
    {
        $payload = new stdClass();
        $payload->Specialization = $request->specialtyName;
        $payload->Date = $this->formatDateTime($request->dateTimeBegin);
        $payload->TimeBegin = $this->formatDateTime($request->dateTimeBegin);
        $payload->EmployeeID = $request->employeeUid;
        $payload->Clinic = $request->clinicUid;

        return new SoapOperation(SoapMethod::CREATE_RESERVE, $payload);
    }

    public function sendWaitList(WaitListRequest $request): SoapOperation
    {
        $payload = $this->mapBaseAppointment($request);
        $payload->Specialization = $request->specialtyName;

        return new SoapOperation(SoapMethod::CREATE_WAIT_LIST, $payload);
    }

    public function sendAppointment(BookAppointmentRequest $request): SoapOperation
    {
        $payload = $this->mapBaseAppointment($request);
        $payload->EmployeeID = $request->employeeUid;
        $payload->GUID = $request->appointmentUid;
        $payload->Params = [
            new SoapParameter('Birthday', $request->clientBirthday instanceof DateTimeInterface ? $this->formatDateTime($request->clientBirthday) : ''),
            new SoapParameter('Duration', $request->appointmentDuration !== null ? DateFormatter::calculateDurationFromSeconds($request->appointmentDuration) : ''),
        ];

        if ($request->services !== []) {
            $payload->Params[] = new SoapParameter('Services', implode(';', $request->services));
            $payload->Params[] = new SoapParameter('DurationType', 'ServiceDuration');
        }

        return new SoapOperation(SoapMethod::CREATE_APPOINTMENT, $payload);
    }

    public function deleteAppointment(string $appointmentUid): SoapOperation
    {
        $payload = new stdClass();
        $payload->GUID = $appointmentUid;

        return new SoapOperation(SoapMethod::DELETE_APPOINTMENT, $payload);
    }

    private function mapBaseAppointment(WaitListRequest|BookAppointmentRequest $request): stdClass
    {
        $payload = new stdClass();
        $payload->PatientSurname = $request->lastName;
        $payload->PatientName = $request->name;
        $payload->PatientFatherName = $request->secondName;
        $payload->Date = $this->formatDateTime($request->dateTimeBegin);
        $payload->TimeBegin = $this->formatDateTime($request->dateTimeBegin);
        $payload->Phone = PhoneFormatter::formatPhone($request->phone);
        $payload->Email = $request->email;
        $payload->Address = $request->address;
        $payload->Clinic = $request->clinicUid;
        $payload->Comment = $request->comment;
        $payload->Params = [];

        return $payload;
    }

    private function formatDateTime(DateTimeInterface $dateTime): string
    {
        return $dateTime->format('Y-m-d\\TH:i:s');
    }
}
