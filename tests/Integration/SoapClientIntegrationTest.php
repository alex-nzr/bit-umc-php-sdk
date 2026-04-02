<?php

declare(strict_types=1);

namespace ANZ\BitUmc\SDK\Tests\Integration;

use ANZ\BitUmc\SDK\BitUmcClient;
use ANZ\BitUmc\SDK\Domain\Exception\TransportException;
use ANZ\BitUmc\SDK\Domain\Request\BookAppointmentRequest;
use ANZ\BitUmc\SDK\Domain\Request\ReserveRequest;
use ANZ\BitUmc\SDK\Domain\Request\ScheduleQuery;
use ANZ\BitUmc\SDK\Domain\Request\WaitListRequest;
use ANZ\BitUmc\SDK\Infrastructure\Soap\SoapRequestMapper;
use ANZ\BitUmc\SDK\Infrastructure\Soap\SoapTransport;
use ANZ\BitUmc\SDK\Transport\Auth\BasicAuth;
use ANZ\BitUmc\SDK\Transport\ConnectionOptions;
use ANZ\BitUmc\SDK\Transport\Endpoint\EndpointResolver;
use ANZ\BitUmc\SDK\Transport\Protocol;
use ANZ\BitUmc\SDK\Transport\TransportType;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class SoapClientIntegrationTest extends TestCase
{
    private ?BitUmcClient $client = null;
    private array $clinics = [];
    private array $employees = [];

    protected function setUp(): void
    {
        if (getenv('BIT_UMC_RUN_INTEGRATION_TESTS') !== '1') {
            $this->markTestSkipped('Integration tests are disabled. Set BIT_UMC_RUN_INTEGRATION_TESTS=1 to enable them.');
        }

        $host = (string) getenv('BIT_UMC_TEST_HOST');
        $baseName = (string) getenv('BIT_UMC_TEST_BASE_NAME');
        $login = (string) getenv('BIT_UMC_TEST_LOGIN');
        $password = (string) getenv('BIT_UMC_TEST_PASSWORD');
        $protocol = strtoupper((string) getenv('BIT_UMC_TEST_PROTOCOL'));

        if ($host === '' || $baseName === '' || $login === '' || $password === '') {
            $this->markTestSkipped('Integration credentials are not configured.');
        }

        $this->client = $this->createClientWithRetry(
            $host,
            $baseName,
            $login,
            $password,
            $protocol === 'HTTPS' ? Protocol::HTTPS : Protocol::HTTP
        );

        $this->clinics = $this->executeWithRetry(fn (): array => $this->client->getClinics(), 3);
        $this->employees = $this->executeWithRetry(fn (): array => $this->client->getEmployees(), 3);
    }

    public function testGetClinicsReturnsData(): void
    {
        self::assertIsArray($this->clinics);
        self::assertNotEmpty($this->clinics);
    }

    public function testGetEmployeesReturnsData(): void
    {
        self::assertIsArray($this->employees);
        self::assertNotEmpty($this->employees);
    }

    public function testFindsCentralClinic(): void
    {
        $clinic = $this->findFirstByField($this->clinics, 'name', 'Центральная клиника');

        self::assertNotNull($clinic);
        self::assertSame('Центральная клиника', $clinic['name']);
    }

    public function testFindsBarbyshevaDoctor(): void
    {
        $employee = $this->findFirstContains($this->employees, 'fullName', 'Барбышева');

        self::assertNotNull($employee);
        self::assertStringContainsString('Барбышева', $employee['fullName']);
    }

    public function testGetsNomenclatureForCentralClinicAndFindsPrimaryOphthalmologistConsultation(): void
    {
        $clinic = $this->findFirstByField($this->clinics, 'name', 'Центральная клиника');
        self::assertNotNull($clinic, 'Central clinic was not found in the live 1C response.');

        $nomenclature = $this->client?->getNomenclature($clinic['uid']);
        self::assertIsArray($nomenclature);
        self::assertNotEmpty($nomenclature);

        $service = $this->findFirstByField($nomenclature, 'name', 'Первичная консультация офтальмолога');

        self::assertNotNull($service, 'Expected service was not found in central clinic nomenclature.');
        self::assertSame('Первичная консультация офтальмолога', $service['name']);
    }

    public function testReserveStatusAndDeleteFlow(): void
    {
        $slot = $this->findFirstFreeSlot();
        self::assertNotNull($slot, 'No free slot was found for reserve flow.');

        $reserveResult = $this->executeWithRetry(fn (): array => $this->client?->sendReserve(new ReserveRequest(
            clinicUid: $slot['clinicUid'],
            employeeUid: $slot['employeeUid'],
            dateTimeBegin: new DateTimeImmutable($slot['slot']['timeBegin']),
            specialtyName: $slot['specialtyName'],
        )));

        self::assertIsArray($reserveResult);
        self::assertArrayHasKey('uid', $reserveResult);

        $statusResult = $this->executeWithRetry(fn (): array => $this->client?->getAppointmentStatus($reserveResult['uid']));
        self::assertIsArray($statusResult);
        self::assertArrayHasKey('statusId', $statusResult);

        $deleteResult = $this->executeWithRetry(fn (): array => $this->client?->deleteAppointment($reserveResult['uid']));
        self::assertIsArray($deleteResult);
        self::assertSame(['success' => true], $deleteResult);
    }

    public function testSendWaitListReturnsSuccess(): void
    {
        $slot = $this->findFirstFreeSlot();
        self::assertNotNull($slot, 'No free slot was found for wait list flow.');

        $result = $this->executeWithRetry(fn (): array => $this->client?->sendWaitList(new WaitListRequest(
            clinicUid: $slot['clinicUid'],
            name: 'Integration',
            lastName: 'WaitList',
            secondName: 'Client',
            phone: '+79990001000',
            dateTimeBegin: new DateTimeImmutable($slot['slot']['timeBegin']),
            specialtyName: $slot['specialtyName'],
            email: 'waitlist.integration@example.com',
            address: 'Integration address',
            comment: 'Integration test wait list',
        )));

        self::assertIsArray($result);
        self::assertArrayHasKey('uid', $result);
    }

    public function testSendAppointmentReturnsSuccess(): void
    {
        $slot = $this->findFirstFreeSlot();
        self::assertNotNull($slot, 'No free slot was found for direct appointment flow.');

        $result = $this->executeWithRetry(fn (): array => $this->client?->sendAppointment(new BookAppointmentRequest(
            clinicUid: $slot['clinicUid'],
            employeeUid: $slot['employeeUid'],
            name: 'Integration',
            lastName: 'Appointment',
            secondName: 'Client',
            phone: '+79990002000',
            dateTimeBegin: new DateTimeImmutable($slot['slot']['timeBegin']),
            specialtyName: $slot['specialtyName'],
            email: 'appointment.integration@example.com',
            address: 'Integration address',
            comment: 'Integration test appointment',
            appointmentDuration: 1800,
        )));

        self::assertSame(['success' => true], $result);
    }

    public function testWrongBaseNameRaisesTransportException(): void
    {
        $host = (string) getenv('BIT_UMC_TEST_HOST');
        $login = (string) getenv('BIT_UMC_TEST_LOGIN');
        $password = (string) getenv('BIT_UMC_TEST_PASSWORD');
        $protocol = strtoupper((string) getenv('BIT_UMC_TEST_PROTOCOL'));

        $this->expectException(TransportException::class);

        $client = new BitUmcClient(
            TransportType::SOAP,
            new ConnectionOptions(
                protocol: $protocol === 'HTTPS' ? Protocol::HTTPS : Protocol::HTTP,
                host: $host,
                baseName: 'missing_base_for_transport_test',
                auth: new BasicAuth($login, $password),
                timeoutSeconds: 30,
            )
        );

        $client->getClinics();
    }

    public function testWrongServiceNameRaisesTransportException(): void
    {
        $host = (string) getenv('BIT_UMC_TEST_HOST');
        $baseName = (string) getenv('BIT_UMC_TEST_BASE_NAME');
        $login = (string) getenv('BIT_UMC_TEST_LOGIN');
        $password = (string) getenv('BIT_UMC_TEST_PASSWORD');
        $protocol = strtoupper((string) getenv('BIT_UMC_TEST_PROTOCOL'));

        $this->expectException(TransportException::class);

        $transport = new SoapTransport(
            new ConnectionOptions(
                protocol: $protocol === 'HTTPS' ? Protocol::HTTPS : Protocol::HTTP,
                host: $host,
                baseName: $baseName,
                auth: new BasicAuth($login, $password),
                timeoutSeconds: 30,
            ),
            new EndpointResolver('ws/IntegrationMissing')
        );

        $transport->execute((new SoapRequestMapper())->getClinics());
    }

    private function findFirstByField(array $items, string $field, string $expectedValue): ?array
    {
        foreach ($items as $item) {
            if (is_array($item) && ($item[$field] ?? null) === $expectedValue) {
                return $item;
            }
        }

        return null;
    }

    private function findFirstContains(array $items, string $field, string $needle): ?array
    {
        foreach ($items as $item) {
            if (is_array($item) && is_string($item[$field] ?? null) && mb_stripos($item[$field], $needle) !== false) {
                return $item;
            }
        }

        return null;
    }

    private function findFirstFreeSlot(): ?array
    {
        $centralClinic = $this->findFirstByField($this->clinics, 'name', 'Центральная клиника');
        if ($centralClinic === null) {
            return null;
        }

        $schedule = $this->executeWithRetry(fn (): array => $this->client?->getSchedule(new ScheduleQuery(days: 14, clinicUid: $centralClinic['uid'])));
        if (!is_array($schedule)) {
            return null;
        }

        foreach ($schedule as $clinicUid => $specialties) {
            foreach ($specialties as $employees) {
                foreach ($employees as $employeeUid => $employeeData) {
                    foreach (($employeeData['timetable']['freeFormatted'] ?? []) as $slots) {
                        foreach ($slots as $slot) {
                            if (is_array($slot) && isset($slot['timeBegin'])) {
                                return [
                                    'clinicUid' => $clinicUid,
                                    'employeeUid' => $employeeUid,
                                    'specialtyName' => $employeeData['specialtyName'] ?? '',
                                    'slot' => $slot,
                                ];
                            }
                        }
                    }
                }
            }
        }

        return null;
    }

    private function executeWithRetry(callable $callback, int $attempts = 2): array
    {
        $lastException = null;

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                return $callback();
            } catch (TransportException $exception) {
                $lastException = $exception;
                $isTransient = $this->isTransientTransportFailure($exception);
                if (!$isTransient || $attempt === $attempts) {
                    throw $exception;
                }

                usleep(500000);
            }
        }

        throw $lastException ?? new TransportException('Unexpected retry failure.');
    }

    private function createClientWithRetry(string $host, string $baseName, string $login, string $password, Protocol $protocol, int $attempts = 3): BitUmcClient
    {
        $lastException = null;

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                return new BitUmcClient(
                    TransportType::SOAP,
                    new ConnectionOptions(
                        protocol: $protocol,
                        host: $host,
                        baseName: $baseName,
                        auth: new BasicAuth($login, $password),
                        timeoutSeconds: 60,
                    )
                );
            } catch (TransportException $exception) {
                $lastException = $exception;
                if (!$this->isTransientTransportFailure($exception) || $attempt === $attempts) {
                    throw $exception;
                }

                usleep(500000);
            }
        }

        throw $lastException ?? new TransportException('Unexpected client initialization retry failure.');
    }

    private function isTransientTransportFailure(TransportException $exception): bool
    {
        $message = $exception->getMessage();

        return str_contains($message, 'Could not connect to host')
            || str_contains($message, 'looks like we got no XML document')
            || str_contains($message, 'Error Fetching http headers')
            || str_contains($message, 'Premature end of data')
            || str_contains($message, 'failed to load external entity');
    }
}
