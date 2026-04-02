<?php

declare(strict_types=1);

namespace ANZ\BitUmc\SDK\Tests\Unit\Infrastructure\Soap;

use ANZ\BitUmc\SDK\Domain\Request\BookAppointmentRequest;
use ANZ\BitUmc\SDK\Domain\Request\ScheduleQuery;
use ANZ\BitUmc\SDK\Infrastructure\Soap\SoapMethod;
use ANZ\BitUmc\SDK\Infrastructure\Soap\SoapRequestMapper;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class SoapRequestMapperTest extends TestCase
{
    public function testMapsAppointmentStatusRequest(): void
    {
        $mapper = new SoapRequestMapper();
        $operation = $mapper->getAppointmentStatus('appointment-guid');

        self::assertSame(SoapMethod::GET_APPOINTMENT_STATUS, $operation->method);
        self::assertSame('appointment-guid', $operation->payload->GUID);
    }

    public function testMapsScheduleQueryWithClinicAndEmployees(): void
    {
        $mapper = new SoapRequestMapper();
        $operation = $mapper->getSchedule(new ScheduleQuery(
            days: 2,
            clinicUid: 'clinic-guid',
            employeeUids: ['emp-1', 'emp-2'],
            startDate: new DateTimeImmutable('2026-06-21 09:00:00'),
        ));

        self::assertSame(SoapMethod::GET_SCHEDULE, $operation->method);
        self::assertSame('2026-06-21T09:00:00', $operation->payload->StartDate);
        self::assertSame('2026-06-23T09:00:00', $operation->payload->FinishDate);
        self::assertCount(2, $operation->payload->Params);
    }

    public function testMapsAppointmentPayload(): void
    {
        $mapper = new SoapRequestMapper();
        $operation = $mapper->sendAppointment(new BookAppointmentRequest(
            clinicUid: 'clinic-guid',
            employeeUid: 'employee-guid',
            name: 'Anton',
            lastName: 'Petrov',
            secondName: 'Ivanovich',
            phone: '+7 (900) 000-00-00',
            dateTimeBegin: new DateTimeImmutable('2026-12-03 14:00:00'),
            specialtyName: 'Стоматология',
            email: 'anton@example.com',
            address: 'Address',
            comment: 'Comment',
            appointmentUid: 'appointment-guid',
            clientBirthday: new DateTimeImmutable('1962-08-05 00:00:00'),
            appointmentDuration: 2700,
            services: ['srv-1', 'srv-2'],
        ));

        self::assertSame(SoapMethod::CREATE_APPOINTMENT, $operation->method);
        self::assertSame('appointment-guid', $operation->payload->GUID);
        self::assertSame('employee-guid', $operation->payload->EmployeeID);
        self::assertCount(4, $operation->payload->Params);
    }
}
