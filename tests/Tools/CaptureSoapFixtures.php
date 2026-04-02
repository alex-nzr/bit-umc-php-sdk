<?php

declare(strict_types=1);

use ANZ\BitUmc\SDK\BitUmcClient;
use ANZ\BitUmc\SDK\Domain\Request\BookAppointmentRequest;
use ANZ\BitUmc\SDK\Domain\Request\ReserveRequest;
use ANZ\BitUmc\SDK\Domain\Request\ScheduleQuery;
use ANZ\BitUmc\SDK\Domain\Request\WaitListRequest;
use ANZ\BitUmc\SDK\Infrastructure\Soap\SoapRequestMapper;
use ANZ\BitUmc\SDK\Infrastructure\Soap\SoapResponseParser;
use ANZ\BitUmc\SDK\Transport\Auth\BasicAuth;
use ANZ\BitUmc\SDK\Transport\ConnectionOptions;
use ANZ\BitUmc\SDK\Transport\Endpoint\EndpointResolver;
use ANZ\BitUmc\SDK\Transport\Protocol;
use ANZ\BitUmc\SDK\Transport\TransportType;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

function save_fixture(string $relativePath, string $content): void
{
    $fullPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $relativePath;
    $dir = dirname($fullPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents($fullPath, $content);
}

function call_raw(SoapClient $soapClient, string $method, object $payload): mixed
{
    return $soapClient->__soapCall($method, [$payload]);
}

function extract_return_string(mixed $response): string
{
    if (is_object($response) && property_exists($response, 'return')) {
        return (string) $response->return;
    }

    throw new RuntimeException('Unexpected SOAP response format.');
}

function find_by_field(array $items, string $field, string $value): ?array
{
    foreach ($items as $item) {
        if (is_array($item) && ($item[$field] ?? null) === $value) {
            return $item;
        }
    }

    return null;
}

function find_contains(array $items, string $field, string $needle): ?array
{
    foreach ($items as $item) {
        if (is_array($item) && is_string($item[$field] ?? null) && mb_stripos($item[$field], $needle) !== false) {
            return $item;
        }
    }

    return null;
}

function find_first_free_slot(array $schedule): ?array
{
    foreach ($schedule as $clinicUid => $specialties) {
        foreach ($specialties as $specialtyUid => $employees) {
            foreach ($employees as $employeeUid => $employeeData) {
                $specialtyName = $employeeData['specialtyName'] ?? '';
                foreach (($employeeData['timetable']['freeFormatted'] ?? []) as $date => $slots) {
                    foreach ($slots as $slot) {
                        return [
                            'clinicUid' => $clinicUid,
                            'employeeUid' => $employeeUid,
                            'specialtyName' => $specialtyName,
                            'slot' => $slot,
                        ];
                    }
                }
            }
        }
    }

    return null;
}

$host = getenv('BIT_UMC_TEST_HOST') ?: '';
$baseName = getenv('BIT_UMC_TEST_BASE_NAME') ?: '';
$login = getenv('BIT_UMC_TEST_LOGIN') ?: '';
$password = getenv('BIT_UMC_TEST_PASSWORD') ?: '';
$protocol = strtoupper(getenv('BIT_UMC_TEST_PROTOCOL') ?: 'HTTP');

if ($host === '' || $baseName === '' || $login === '' || $password === '') {
    throw new RuntimeException('BIT_UMC_TEST_HOST, BIT_UMC_TEST_BASE_NAME, BIT_UMC_TEST_LOGIN and BIT_UMC_TEST_PASSWORD must be set before fixture capture.');
}

$options = new ConnectionOptions(
    protocol: $protocol === 'HTTPS' ? Protocol::HTTPS : Protocol::HTTP,
    host: $host,
    baseName: $baseName,
    auth: new BasicAuth($login, $password),
    timeoutSeconds: 60,
);

$resolver = new EndpointResolver();
$soapClient = new SoapClient(
    $resolver->resolveWsdl($options),
    [
        'login' => $login,
        'password' => $password,
        'soap_version' => SOAP_1_2,
        'location' => $resolver->resolveSoapLocation($options),
        'cache_wsdl' => WSDL_CACHE_NONE,
        'exceptions' => true,
        'trace' => 1,
        'connection_timeout' => 60,
        'keep_alive' => false,
        'stream_context' => stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]),
    ]
);

$client = new BitUmcClient(TransportType::SOAP, $options);
$mapper = new SoapRequestMapper();
$responseParser = new SoapResponseParser();

$clinicsResponse = call_raw($soapClient, $mapper->getClinics()->method->value, $mapper->getClinics()->payload);
save_fixture('tests/Fixtures/soap/clinics/success.xml', extract_return_string($clinicsResponse));
$clinics = $client->getClinics();
$centralClinic = find_by_field($clinics, 'name', 'Центральная клиника');
if ($centralClinic === null) {
    throw new RuntimeException('Central clinic not found.');
}

$employeesResponse = call_raw($soapClient, $mapper->getEmployees()->method->value, $mapper->getEmployees()->payload);
save_fixture('tests/Fixtures/soap/employees/success.xml', extract_return_string($employeesResponse));
$employees = $client->getEmployees();
$barbysheva = find_contains($employees, 'fullName', 'Барбышева');
if ($barbysheva === null) {
    throw new RuntimeException('Doctor Барбышева not found.');
}

$nomenclatureOp = $mapper->getNomenclature($centralClinic['uid']);
$nomenclatureResponse = call_raw($soapClient, $nomenclatureOp->method->value, $nomenclatureOp->payload);
save_fixture('tests/Fixtures/soap/nomenclature/success.xml', extract_return_string($nomenclatureResponse));
$nomenclature = $client->getNomenclature($centralClinic['uid']);
$consultation = find_by_field($nomenclature, 'name', 'Первичная консультация офтальмолога');
if ($consultation === null) {
    throw new RuntimeException('Service "Первичная консультация офтальмолога" not found.');
}

$scheduleQuery = new ScheduleQuery(days: 14, clinicUid: $centralClinic['uid']);
$scheduleOp = $mapper->getSchedule($scheduleQuery);
$scheduleResponse = call_raw($soapClient, $scheduleOp->method->value, $scheduleOp->payload);
save_fixture('tests/Fixtures/soap/schedule/success.xml', extract_return_string($scheduleResponse));
$schedule = $client->getSchedule($scheduleQuery);
$slot = find_first_free_slot($schedule);
if ($slot === null) {
    throw new RuntimeException('No free slot found in schedule.');
}

$dateTimeBegin = new DateTimeImmutable($slot['slot']['timeBegin']);
$clientBirthday = new DateTimeImmutable('1985-05-20T00:00:00');
$uniqueSuffix = date('YmdHis');

$directAppointmentRequest = new BookAppointmentRequest(
    clinicUid: $slot['clinicUid'],
    employeeUid: $slot['employeeUid'],
    name: 'Тест',
    lastName: 'ПрямаяЗапись',
    secondName: 'Клиент',
    phone: '+79990000000',
    dateTimeBegin: $dateTimeBegin,
    specialtyName: $slot['specialtyName'],
    email: 'integration+' . $uniqueSuffix . '@example.com',
    address: 'Test address',
    comment: 'Automated integration test direct appointment',
    clientBirthday: $clientBirthday,
    appointmentDuration: 1800,
);
$appointmentOp = $mapper->sendAppointment($directAppointmentRequest);
$appointmentResponse = call_raw($soapClient, $appointmentOp->method->value, $appointmentOp->payload);
save_fixture('tests/Fixtures/soap/common/appointment-success.xml', extract_return_string($appointmentResponse));
$responseParser->parse($appointmentOp->method, $appointmentResponse);

$reserveRequest = new ReserveRequest(
    clinicUid: $slot['clinicUid'],
    employeeUid: $slot['employeeUid'],
    dateTimeBegin: $dateTimeBegin,
    specialtyName: $slot['specialtyName'],
);
$reserveOp = $mapper->sendReserve($reserveRequest);
$reserveResponse = call_raw($soapClient, $reserveOp->method->value, $reserveOp->payload);
save_fixture('tests/Fixtures/soap/common/success-with-uid.xml', extract_return_string($reserveResponse));
$reserveResult = $responseParser->parse($reserveOp->method, $reserveResponse);
$reserveUid = $reserveResult['uid'] ?? null;
if (!is_string($reserveUid) || $reserveUid === '') {
    throw new RuntimeException('Reserve UID was not returned.');
}

$statusOp = $mapper->getAppointmentStatus($reserveUid);
$statusResponse = call_raw($soapClient, $statusOp->method->value, $statusOp->payload);
save_fixture('tests/Fixtures/soap/status/success.xml', extract_return_string($statusResponse));

$reserveFlowAppointmentRequest = new BookAppointmentRequest(
    clinicUid: $slot['clinicUid'],
    employeeUid: $slot['employeeUid'],
    name: 'Тест',
    lastName: 'БроньЗапись',
    secondName: 'Клиент',
    phone: '+79990000002',
    dateTimeBegin: $dateTimeBegin,
    specialtyName: $slot['specialtyName'],
    email: 'reserve-flow+' . $uniqueSuffix . '@example.com',
    address: 'Reserve flow address',
    comment: 'Automated integration test reserve to appointment',
    appointmentUid: $reserveUid,
    appointmentDuration: 1800,
);
$reserveFlowAppointmentOp = $mapper->sendAppointment($reserveFlowAppointmentRequest);
$reserveFlowAppointmentResponse = call_raw($soapClient, $reserveFlowAppointmentOp->method->value, $reserveFlowAppointmentOp->payload);
save_fixture('tests/Fixtures/soap/common/error-description.xml', extract_return_string($reserveFlowAppointmentResponse));

$deleteOp = $mapper->deleteAppointment($reserveUid);
$deleteResponse = call_raw($soapClient, $deleteOp->method->value, $deleteOp->payload);
save_fixture('tests/Fixtures/soap/common/delete-success.xml', extract_return_string($deleteResponse));
$responseParser->parse($deleteOp->method, $deleteResponse);

$waitListRequest = new WaitListRequest(
    clinicUid: $centralClinic['uid'],
    name: 'Тест',
    lastName: 'Лист',
    secondName: 'Ожидания',
    phone: '+79990000001',
    dateTimeBegin: $dateTimeBegin,
    specialtyName: $slot['specialtyName'],
    email: 'waitlist+' . $uniqueSuffix . '@example.com',
    address: 'Wait list address',
    comment: 'Automated integration test wait list',
);
$waitListOp = $mapper->sendWaitList($waitListRequest);
$waitListResponse = call_raw($soapClient, $waitListOp->method->value, $waitListOp->payload);
save_fixture('tests/Fixtures/soap/common/wait-list-success.xml', extract_return_string($waitListResponse));
$responseParser->parse($waitListOp->method, $waitListResponse);

try {
    $badNomenclatureOp = $mapper->getNomenclature('00000000-0000-0000-0000-000000000001');
    $badNomenclatureResponse = call_raw($soapClient, $badNomenclatureOp->method->value, $badNomenclatureOp->payload);
    save_fixture('tests/Fixtures/soap/nomenclature/error-description.xml', extract_return_string($badNomenclatureResponse));
} catch (Throwable $exception) {
    save_fixture('tests/Fixtures/soap/nomenclature/error-description.xml', '');
}

try {
    $badStatusOp = $mapper->getAppointmentStatus('00000000-0000-0000-0000-000000000001');
    $badStatusResponse = call_raw($soapClient, $badStatusOp->method->value, $badStatusOp->payload);
    save_fixture('tests/Fixtures/soap/status/error-description.xml', extract_return_string($badStatusResponse));
} catch (Throwable $exception) {
    save_fixture('tests/Fixtures/soap/status/error-description.xml', '');
}

save_fixture('tests/Fixtures/soap/metadata.json', json_encode([
    'capturedAt' => date(DATE_ATOM),
    'centralClinic' => $centralClinic,
    'doctor' => $barbysheva,
    'service' => $consultation,
    'slot' => $slot,
    'reserveUid' => $reserveUid,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Fixtures captured successfully\n";
