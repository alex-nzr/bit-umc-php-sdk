<?php

declare(strict_types=1);

namespace ANZ\BitUmc\SDK\Tests\Integration;

use ANZ\BitUmc\SDK\BitUmcClient;
use ANZ\BitUmc\SDK\Domain\Exception\TransportException;
use ANZ\BitUmc\SDK\Domain\Request\ScheduleQuery;
use ANZ\BitUmc\SDK\Transport\Auth\BasicAuth;
use ANZ\BitUmc\SDK\Transport\ConnectionOptions;
use ANZ\BitUmc\SDK\Transport\Protocol;
use ANZ\BitUmc\SDK\Transport\TransportType;
use PHPUnit\Framework\TestCase;

final class StreamingSoapClientIntegrationTest extends TestCase
{
    private ?BitUmcClient $client = null;

    protected function setUp(): void
    {
        if (getenv('BIT_UMC_RUN_INTEGRATION_TESTS') !== '1') {
            $this->markTestSkipped('Integration tests are disabled. Set BIT_UMC_RUN_INTEGRATION_TESTS=1 to enable them.');
        }

        $host = (string) getenv('BIT_UMC_TEST_HOST');
        $port = (string) getenv('BIT_UMC_TEST_PORT');
        $baseName = (string) getenv('BIT_UMC_TEST_BASE_NAME');
        $login = (string) getenv('BIT_UMC_TEST_LOGIN');
        $password = (string) getenv('BIT_UMC_TEST_PASSWORD');
        $protocol = strtoupper((string) getenv('BIT_UMC_TEST_PROTOCOL'));

        if ($host === '' || $baseName === '' || $login === '' || $password === '') {
            $this->markTestSkipped('Integration credentials are not configured.');
        }

        $this->client = new BitUmcClient(
            TransportType::SOAP,
            new ConnectionOptions(
                protocol: $protocol === 'HTTPS' ? Protocol::HTTPS : Protocol::HTTP,
                host: $port === '' ? $host : $host . ':' . $port,
                baseName: $baseName,
                auth: new BasicAuth($login, $password),
                timeoutSeconds: 60,
            )
        );
    }

    public function testStreamingReadMethodsReturnLiveData(): void
    {
        $client = $this->client ?? throw new TransportException('Streaming integration client was not initialized.');
        $clinics = [];

        foreach ($client->iterateClinics() as $item) {
            self::assertIsArray($item);
            self::assertNotSame('', $item['uid'] ?? '');
            $clinics[] = $item;
        }

        self::assertNotSame([], $clinics, 'Streaming clinics response is empty.');

        $employeeFound = false;
        foreach ($client->iterateEmployees() as $item) {
            self::assertIsArray($item);
            self::assertNotSame('', $item['uid'] ?? '');
            $employeeFound = true;
            break;
        }

        self::assertTrue($employeeFound, 'Streaming employees response is empty.');

        $centralClinic = $this->findFirstByField($clinics, 'name', 'Центральная клиника');
        self::assertNotNull($centralClinic, 'Central clinic was not found in the live 1C response.');

        $nomenclatureFound = false;
        foreach ($client->iterateNomenclature($centralClinic['uid']) as $item) {
            self::assertIsArray($item);
            self::assertNotSame('', $item['uid'] ?? '');
            $nomenclatureFound = true;
            break;
        }

        self::assertTrue($nomenclatureFound, 'Streaming nomenclature response is empty.');

        $scheduleFound = false;
        foreach ($client->iterateSchedule(new ScheduleQuery(days: 14, clinicUid: $centralClinic['uid'])) as $record) {
            self::assertIsArray($record);
            self::assertContains($record['kind'] ?? null, ['employee', 'period']);
            $scheduleFound = true;
            break;
        }

        self::assertTrue($scheduleFound, 'Streaming schedule response is empty.');
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
}
