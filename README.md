# SDK для 1С-БИТ.УМЦ

## Требования
- PHP >= 8.1
- ext-soap
- ext-json
- ext-xmlreader

## API версии 2
SDK построен вокруг единой публичной точки входа: `ANZ\\BitUmc\\SDK\\BitUmcClient`.

### Принципы
- Учётные данные передаются во время выполнения.
- SDK не читает `.env`, ini-файлы и базы данных.
- Тип транспорта явно выбирается через `TransportType`.
- Сейчас реализован SOAP; HTTP-транспорт зарезервирован для следующего этапа.
- XML-ответы SOAP разбираются через `XMLReader`, чтобы уменьшить пиковое потребление памяти.
- Успешные вызовы возвращают нормализованные массивы.
- Ошибки выбрасывают типизированные исключения из `ANZ\\BitUmc\\SDK\\Domain\\Exception`.

## Использование
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

## Доступные операции
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

## Большие коллекции

Методы `getClinics()`, `getEmployees()` и `getNomenclature()` для обратной совместимости продолжают возвращать полные массивы. Приложения, которым не нужно хранить всю большую коллекцию, могут использовать соответствующий итератор:

```php
foreach ($client->iterateEmployees() as $employeeUid => $employee) {
    $repository->save($employeeUid, $employee);
}
```

Доступны `iterateClinics()`, `iterateEmployees()` и `iterateNomenclature(string $clinicUid)`. Они возвращают те же нормализованные элементы и ключи UID, но не накапливают результирующий массив внутри SDK. Сам SOAP-ответ пока всё ещё получается `SoapClient` как строка; потоковый транспорт будет отдельной оптимизацией. Исключения итераторов возникают при начале обхода.

## DTO запросов
- `ANZ\\BitUmc\\SDK\\Domain\\Request\\ScheduleQuery`
- `ANZ\\BitUmc\\SDK\\Domain\\Request\\ReserveRequest`
- `ANZ\\BitUmc\\SDK\\Domain\\Request\\WaitListRequest`
- `ANZ\\BitUmc\\SDK\\Domain\\Request\\BookAppointmentRequest`

Полный пример приведён в [examples/index.php](examples/index.php).

## Возвращаемые данные

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

## Обработка ошибок
```php
use ANZ\BitUmc\SDK\Domain\Exception\BitUmcException;

try {
    $clinics = $client->getClinics();
} catch (BitUmcException $e) {
    echo $e->getMessage();
}
```

## Тесты

### Что покрыто
Текущий набор тестов покрывает следующие случаи.

Unit-тесты с fixtures:
- разрешение endpoint;
- маппинг запросов расписания, статуса и создания записи;
- разбор клиник, сотрудников, услуг, расписания и статусов;
- обработка SOAP-ответов:
  - `Ok`
  - `Error`
  - plain text error responses
  - XML responses with `ОписаниеОшибки`

Интеграционные тесты с реальной 1С:
- `getClinics()`
- `getEmployees()`
- поиск `Центральная клиника`;
- поиск врача `Барбышева`;
- `getNomenclature()` для центральной клиники с поиском `Первичная консультация офтальмолога`;
- `sendReserve()` -> `getAppointmentStatus()` -> `deleteAppointment()`
- `sendWaitList()`
- `sendAppointment()`
- негативные сценарии транспорта: неверное имя базы и web-сервиса.

### Как работают unit-тесты
Unit-тесты не подключаются к 1С. Они используют локальные fixtures из `tests/Fixtures/soap`: необработанные XML и текстовые ответы, полученные из тестового экземпляра 1С.

Это означает, что тесты детерминированы и быстры, поведение парсера проверяется на реальных ответах, а учётные данные не требуются.

Запуск unit-тестов:
```bash
vendor/bin/phpunit --testsuite unit
```

### Как обновить fixtures из реальной 1С
Скрипт получения fixtures:
- [CaptureSoapFixtures.php](tests/Tools/CaptureSoapFixtures.php)

Перед запуском задайте переменные окружения:
- `BIT_UMC_TEST_PROTOCOL` = `HTTP` or `HTTPS`
- `BIT_UMC_TEST_HOST`
- `BIT_UMC_TEST_BASE_NAME`
- `BIT_UMC_TEST_LOGIN`
- `BIT_UMC_TEST_PASSWORD`

Пример для PowerShell:
```powershell
$env:BIT_UMC_TEST_PROTOCOL='HTTP'
$env:BIT_UMC_TEST_HOST='example.com'
$env:BIT_UMC_TEST_BASE_NAME='umc'
$env:BIT_UMC_TEST_LOGIN='login'
$env:BIT_UMC_TEST_PASSWORD='password'
php tests/Tools/CaptureSoapFixtures.php
```

### Как запустить интеграционные тесты против реальной 1С
Интеграционные тесты выполняют сетевые вызовы и требуют переменных окружения.

Поддерживаемые переменные:
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

Важно:
- интеграционные тесты предполагают, что в целевой базе есть данные, похожие на использованные fixtures;
- сейчас они ориентированы на клинику `Центральная клиника`, врача `Барбышева` и услугу `Первичная консультация офтальмолога`;
- в другой базе эти сущности могут отсутствовать, поэтому отдельные проверки могут завершиться ошибкой при исправных транспорте и парсинге;
- общие негативные проверки транспорта применимы к любой базе.

## Примечание о транспорте
В `ConnectionOptions` поле `apiKey` уже зарезервировано для будущего HTTP-транспорта. SOAP его игнорирует.
