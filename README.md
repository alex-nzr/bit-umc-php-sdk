# SDK for 1C BIT.UMC

## Requirements
- PHP >= 8.1
- ext-soap
- ext-json
- ext-xmlreader

## Version 2 API
The SDK is built around a single public entry point: `ANZ\\BitUmc\\SDK\\BitUmcClient`.

### Principles
- Credentials and access data are passed at runtime.
- The SDK does not read `.env`, ini files or databases.
- Transport type is selected explicitly through `TransportType`.
- SOAP is implemented now.
- HTTP transport is reserved in the architecture for the next stage.
- SOAP XML responses are parsed through `XMLReader` to reduce peak memory usage on large payloads.
- Successful calls return normalized arrays.
- Failures throw typed exceptions from `ANZ\\BitUmc\\SDK\\Domain\\Exception`.

## Usage
```php
use ANZ\BitUmc\SDK\BitUmcClient;
use ANZ\BitUmc\SDK\Transport\Auth\BasicAuth;
use ANZ\BitUmc\SDK\Transport\ConnectionOptions;
use ANZ\BitUmc\SDK\Transport\Protocol;
use ANZ\BitUmc\SDK\Transport\TransportType;

$client = new BitUmcClient(
    TransportType::SOAP,
    new ConnectionOptions(
        protocol: Protocol::HTTP,
        host: '127.0.0.1:3500',
        baseName: 'umc',
        auth: new BasicAuth('1cUser', '1cUserPassword'),
        apiKey: null,
    )
);
```

## Available operations
```php
$client->getClinics();
$client->getEmployees();
$client->getNomenclature($clinicUid);
$client->getSchedule($scheduleQuery);
$client->getAppointmentStatus($appointmentUid);
$client->sendReserve($reserveRequest);
$client->sendWaitList($waitListRequest);
$client->sendAppointment($bookAppointmentRequest);
$client->deleteAppointment($appointmentUid);
```

## Request DTOs
- `ANZ\\BitUmc\\SDK\\Domain\\Request\\ScheduleQuery`
- `ANZ\\BitUmc\\SDK\\Domain\\Request\\ReserveRequest`
- `ANZ\\BitUmc\\SDK\\Domain\\Request\\WaitListRequest`
- `ANZ\\BitUmc\\SDK\\Domain\\Request\\BookAppointmentRequest`

See [examples/index.php](examples/index.php) for a complete example.

## Returned data

### `getClinics(): array`
```php
[
    'f679444a-22b7-11df-8618-002618dcef2c' => [
        'uid' => 'f679444a-22b7-11df-8618-002618dcef2c',
        'name' => 'Центральная клиника',
    ],
    '66abf7b4-2ff9-11df-8625-002618dcef2c' => [
        'uid' => '66abf7b4-2ff9-11df-8625-002618dcef2c',
        'name' => 'Третий центр',
    ],
]
```

### `getEmployees(): array`
```php
[
    '2eb1f97b-6a3c-11e9-936d-1856809fe650' => [
        'uid' => '2eb1f97b-6a3c-11e9-936d-1856809fe650',
        'name' => 'Юрий',
        'surname' => 'Безногов',
        'middleName' => 'Сергеевич',
        'fullName' => 'Безногов Юрий Сергеевич',
        'clinicUid' => 'f679444a-22b7-11df-8618-002618dcef2c',
        'photo' => 'base64_encoded_photo',
        'description' => 'Краткое описание из 1С',
        'rating' => '',
        'specialtyName' => 'Офтальмология',
        'specialtyUid' => '0j7rhngc0ldqu9gm0lzqvtc70l7qs9c40y8',
        'services' => [
            '5210c9dc-65a2-11e9-936d-1856809fe650' => [
                'uid' => '5210c9dc-65a2-11e9-936d-1856809fe650',
                'personalDuration' => 0,
            ],
        ],
    ],
]
```

### `getNomenclature(string $clinicUid): array`
```php
[
    'a0230570-3ef7-11de-8086-001485c0d477' => [
        'uid' => 'a0230570-3ef7-11de-8086-001485c0d477',
        'name' => 'Массаж век',
        'typeOfItem' => 'Услуга',
        'artNumber' => '',
        'price' => '160',
        'duration' => 1800,
        'measureUnit' => 'мин',
        'parent' => '5210c9bf-65a2-11e9-936d-1856809fe650',
    ],
    'a0230571-3ef7-11de-8086-001485c0d477' => [
        'uid' => 'a0230571-3ef7-11de-8086-001485c0d477',
        'name' => 'Гель для массажа',
        'typeOfItem' => 'Материал',
        'artNumber' => '101-55',
        'price' => '100',
        'duration' => 900,
        'measureUnit' => 'мл',
        'parent' => '00000000-0000-0000-0000-000000000000',
    ],
]
```

### `getSchedule(?ScheduleQuery $query = null): array`
```php
[
    'f679444a-22b7-11df-8618-002618dcef2c' => [
        '0j7rhngc0ldqu9gm0lzqvtc70l7qs9c40y8' => [
            'ac30e13a-3087-11dc-8594-005056c00008' => [
                'specialtyName' => 'Офтальмология',
                'employeeName' => 'Барбышева Евгения Петровна',
                'durationFrom1C' => '0001-01-01T00:15:00',
                'durationInSeconds' => 900,
                'timetable' => [
                    'freeFormatted' => [
                        '05-04-2026' => [
                            [
                                'typeOfTimeUid' => '624f2a40-5aa8-4f01-83f4-0f38535364bb',
                                'date' => '2026-04-05T00:00:00',
                                'timeBegin' => '2026-04-05T09:00:00',
                                'timeEnd' => '2026-04-05T09:15:00',
                                'formattedDate' => '05-04-2026',
                                'formattedTimeBegin' => '09:00',
                                'formattedTimeEnd' => '09:15',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
]
```

### `getAppointmentStatus(string $appointmentUid): array`
```php
[
    'statusId' => '6',
    'statusTitle' => 'Резерв времени',
]
```

### `sendReserve(ReserveRequest $request): array`
```php
[
    'uid' => '432a40a2-2ea3-11f1-9bf7-8fedb1ae9f50',
]
```

### `sendWaitList(WaitListRequest $request): array`
```php
[
    'uid' => '432a40ab-2ea3-11f1-9bf7-8fedb1ae9f50',
]
```

### `sendAppointment(BookAppointmentRequest $request): array`
```php
[
    'success' => true,
]
```

### `deleteAppointment(string $appointmentUid): array`
```php
[
    'success' => true,
]
```

## Error handling
```php
use ANZ\BitUmc\SDK\Domain\Exception\BitUmcException;

try {
    $clinics = $client->getClinics();
} catch (BitUmcException $e) {
    echo $e->getMessage();
}
```

## Tests

### What is covered
Current test suite covers these cases.

Unit and fixture-based tests:
- endpoint resolution
- request mapping for schedule, appointment status and appointment creation
- parsing of clinics
- parsing of employees and employee services
- parsing of nomenclature
- parsing of schedule
- parsing of common result payloads
- parsing of appointment status payloads
- SOAP response handling for:
  - `Ok`
  - `Error`
  - plain text error responses
  - XML responses with `ОписаниеОшибки`

Live integration tests:
- `getClinics()`
- `getEmployees()`
- search for `Центральная клиника`
- search for doctor `Барбышева`
- `getNomenclature()` for the central clinic with lookup of `Первичная консультация офтальмолога`
- `sendReserve()` -> `getAppointmentStatus()` -> `deleteAppointment()`
- `sendWaitList()`
- `sendAppointment()`
- negative transport cases:
  - wrong base name
  - wrong web-service name

### How unit tests work
Unit tests do not connect to 1C.
They work on local fixtures from `tests/Fixtures/soap`.
These fixtures are raw XML or raw text responses captured from a real 1C test instance.

This means:
- unit tests are deterministic and fast
- parser behaviour is checked against real responses, not synthetic examples
- you do not need any runtime credentials to run unit tests

Run unit tests:
```bash
vendor/bin/phpunit --testsuite unit
```

### How to refresh fixtures from a real 1C instance
Fixture capture script:
- [CaptureSoapFixtures.php](tests/Tools/CaptureSoapFixtures.php)

Before running it, set environment variables:
- `BIT_UMC_TEST_PROTOCOL` = `HTTP` or `HTTPS`
- `BIT_UMC_TEST_HOST`
- `BIT_UMC_TEST_BASE_NAME`
- `BIT_UMC_TEST_LOGIN`
- `BIT_UMC_TEST_PASSWORD`

Example PowerShell:
```powershell
$env:BIT_UMC_TEST_PROTOCOL='HTTP'
$env:BIT_UMC_TEST_HOST='example.com'
$env:BIT_UMC_TEST_BASE_NAME='umc'
$env:BIT_UMC_TEST_LOGIN='login'
$env:BIT_UMC_TEST_PASSWORD='password'
php tests/Tools/CaptureSoapFixtures.php
```

### How to run integration tests against a real 1C instance
Integration tests use live network calls and require env variables.

Supported env variables:
- `BIT_UMC_RUN_INTEGRATION_TESTS=1`
- `BIT_UMC_TEST_PROTOCOL=HTTP` or `HTTPS`
- `BIT_UMC_TEST_HOST`
- `BIT_UMC_TEST_BASE_NAME`
- `BIT_UMC_TEST_LOGIN`
- `BIT_UMC_TEST_PASSWORD`

Example PowerShell:
```powershell
$env:BIT_UMC_RUN_INTEGRATION_TESTS='1'
$env:BIT_UMC_TEST_PROTOCOL='HTTP'
$env:BIT_UMC_TEST_HOST='example.com'
$env:BIT_UMC_TEST_BASE_NAME='umc'
$env:BIT_UMC_TEST_LOGIN='login'
$env:BIT_UMC_TEST_PASSWORD='password'
vendor/bin/phpunit --testsuite integration
```

Important:
- integration tests assume that the target test base contains data similar to the fixtures used when they were written
- they currently oriented on clinic `Центральная клиника`, doctor `Барбышева` and service `Первичная консультация офтальмолога`
- on another base those exact entities may not exist, so tests that check for them may fail even if transport and parsing work correctly
- generic transport-negative tests can still be reused on any base

## Transport notes
`ConnectionOptions` already reserves the `apiKey` field for the future HTTP transport. SOAP ignores this field.
