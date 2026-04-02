<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');

use ANZ\BitUmc\SDK\BitUmcClient;
use ANZ\BitUmc\SDK\Debug\Logger;
use ANZ\BitUmc\SDK\Domain\Request\BookAppointmentRequest;
use ANZ\BitUmc\SDK\Domain\Request\ReserveRequest;
use ANZ\BitUmc\SDK\Domain\Request\ScheduleQuery;
use ANZ\BitUmc\SDK\Domain\Request\WaitListRequest;
use ANZ\BitUmc\SDK\Transport\Auth\BasicAuth;
use ANZ\BitUmc\SDK\Transport\ConnectionOptions;
use ANZ\BitUmc\SDK\Transport\Protocol;
use ANZ\BitUmc\SDK\Transport\TransportType;

try {
    // Runtime credentials and transport are passed explicitly from the host application.
    $client = new BitUmcClient(
        TransportType::SOAP,
        new ConnectionOptions(
            protocol: Protocol::HTTP,
            host: 'localhost:3500',
            baseName: 'umc',
            auth: new BasicAuth('siteIntegration', '123456'),
            apiKey: null,
        )
    );

    $clinicUid = 'f679444a-22b7-11df-8618-002618dcef2c';
    $employeeUid1 = 'ac30e13a-3087-11dc-8594-005056c00008';
    $employeeUid2 = '99868528-0928-11dc-93d1-0004614ae652';
    $employeeUid3 = 'eab46ee9-94b1-11e3-87ec-002618dcef2c';
    $serviceUid1 = 'a0230570-3ef7-11de-8086-001485c0d477';
    $serviceUid2 = 'eb20edcf-3ee0-11de-b0dd-0050bf5d92cb';

    $dateTimeBegin = DateTime::createFromFormat('d.m.Y H:i:s', '03.12.2026 14:00:00');
    $clientBirthday = DateTime::createFromFormat('d.m.Y', '05.08.1962');

    /*
     * 1. Direct requests with no DTOs
     */

    //$clinics = $client->getClinics();
    //Logger::print('Clinics', $clinics);

    //$employees = $client->getEmployees();
    //Logger::print('Employees', $employees);

    //$nomenclature = $client->getNomenclature($clinicUid);
    //Logger::print('Nomenclature', $nomenclature);

    //$appointmentStatus = $client->getAppointmentStatus('39d9b2f9-35db-11ed-9bf2-5e3a455eb0cf');
    //Logger::print('Appointment status', $appointmentStatus);

    //$deleteResult = $client->deleteAppointment('8912c555-907a-11ee-9c3f-5e3a455eb0cf');
    //Logger::print('Delete appointment result', $deleteResult);

    /*
     * 2. Schedule query
     */

    $scheduleQuery = new ScheduleQuery(
        days: 2,
        clinicUid: $clinicUid,
        employeeUids: [$employeeUid1, $employeeUid2, $employeeUid3],
        startDate: DateTime::createFromFormat('d.m.Y H:i:s', '21.06.2026 09:00:00'),
    );

    //$schedule = $client->getSchedule($scheduleQuery);
    //Logger::print('Schedule', $schedule);

    /*
     * 3. Wait list request
     */

    $waitListRequest = new WaitListRequest(
        clinicUid: $clinicUid,
        name: 'Иван',
        lastName: 'Иванов',
        secondName: 'Иванович',
        phone: '+7 (915) 541-59-35',
        dateTimeBegin: $dateTimeBegin,
        specialtyName: 'Стоматология',
        email: 'example@gmail.com',
        address: 'г. Москва, проспект Ленина 45',
        comment: 'Comment text',
    );

    //$waitListResult = $client->sendWaitList($waitListRequest);
    //Logger::print('Wait list result', $waitListResult);

    /*
     * 4. Reserve request
     */

    $reserveRequest = new ReserveRequest(
        clinicUid: $clinicUid,
        employeeUid: $employeeUid1,
        dateTimeBegin: $dateTimeBegin,
        specialtyName: 'Стоматология',
    );

    //$reserveResult = $client->sendReserve($reserveRequest);
    //Logger::print('Reserve result', $reserveResult);

    /*
     * 5. Book appointment request
     */

    $bookAppointmentRequest = new BookAppointmentRequest(
        clinicUid: $clinicUid,
        employeeUid: $employeeUid1,
        name: 'Антон',
        lastName: 'Печкин',
        secondName: 'Павлович',
        phone: '+7 900 080-31-26',
        dateTimeBegin: $dateTimeBegin,
        specialtyName: 'Стоматология',
        email: 'ppp@gmail.com',
        address: 'г. Москва, проспект Ленина 46',
        comment: 'Comment text',
        appointmentUid: '',
        clientBirthday: $clientBirthday,
        appointmentDuration: 2700,
        services: [$serviceUid1, $serviceUid2],
    );

    $appointmentResult = $client->sendAppointment($bookAppointmentRequest);
    Logger::print('Appointment result', $appointmentResult);
} catch (Throwable $e) {
    Logger::print('Application error', [
        'class' => $e::class,
        'message' => $e->getMessage(),
    ]);
}
