<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');

use ANZ\BitUmc\SDK\Builder;
use ANZ\BitUmc\SDK\Core\Dictionary\ClientScope;
use ANZ\BitUmc\SDK\Core\Dictionary\Protocol;
use ANZ\BitUmc\SDK\Debug\Logger;
use ANZ\BitUmc\SDK\Factory;

try
{
    $client = Builder\ExchangeClient::init()
                ->setLogin('siteIntegration')
                ->setPassword('123456')
                ->setPublicationProtocol(Protocol::HTTP)
                ->setPublicationAddress('localhost:3500')
                ->setBaseName('umc')
                ->setScope(ClientScope::WEB_SERVICE)
                ->build();

    $exchangeService = (new Factory\Exchange($client))->create();

    $clinicUid = 'f679444a-22b7-11df-8618-002618dcef2c';//'a78cbf94-24bd-11eb-baa7-1c1b0d51378c';
    $empUid1   = 'ac30e13a-3087-11dc-8594-005056c00008';//'0aeb21f1-cff1-4ff9-b3c1-721cf67f3968';
    $empUid2   = '99868528-0928-11dc-93d1-0004614ae652';//'f3151797-457d-487c-bb5f-e6e27ce9e180';
    $empUid3   = 'eab46ee9-94b1-11e3-87ec-002618dcef2c';
    $srvUid1   = 'a0230570-3ef7-11de-8086-001485c0d477';//'964759cf-de9c-11e9-9383-50af73235947';
    $srvUid2   = 'eb20edcf-3ee0-11de-b0dd-0050bf5d92cb';//'bd439810-de9c-11e9-9383-50af73235947';

    //$res = $exchangeService->getClinics();
    //$res = $exchangeService->getEmployees();
    //$res = $exchangeService->getNomenclature($clinicUid);

    //$res = $exchangeService->getSchedule(2, $clinicUid, [$empUid1, $empUid2, $empUid3], DateTime::createFromFormat("d.m.Y H:i:s", "21.06.2023 09:00:00"));
    //$res = $exchangeService->getOrderStatus('39d9b2f9-35db-11ed-9bf2-5e3a455eb0cf');

    //В качестве даты и времени записи передаётся объект \DateTime, созданный любым удобным способом
    $dateTimeBegin = DateTime::createFromFormat("d.m.Y H:i:s", "03.12.2023 14:00:00");

    $waitList = Builder\Order::createWaitList()
        ->setSpecialtyName('Стоматология')
        ->setName('Иван')
        ->setLastName('Иванов')
        ->setSecondName('Иванович')
        ->setDateTimeBegin($dateTimeBegin)
        ->setPhone("+7 (915) 5415935")
        ->setEmail('example@gmail.com')
        ->setAddress('г. Москва, проспект Ленина 45')
        ->setClinicUid($clinicUid)
        ->setComment('Comment text')
        ->build();

    //$res = $exchangeService->sendWaitList($waitList);

    $reserve = Builder\Order::createReserve()
        ->setClinicUid($clinicUid)
        ->setSpecialtyName('Стоматология')
        ->setEmployeeUid($empUid1)
        ->setDateTimeBegin($dateTimeBegin)
        ->build();
    //$res = $exchangeService->sendReserve($reserve);

    //$orderUid = $res->getData()['uid'];

    $order = Builder\Order::createOrder()
        ->setEmployeeUid($empUid1)
        ->setName('Антон')
        ->setLastName('Печкин')
        ->setSecondName('Павлович')
        ->setDateTimeBegin($dateTimeBegin)
        ->setPhone("+79000803126")
        ->setEmail('ppp@gmail.com')
        ->setAddress('г. Москва, проспект Ленина 46')
        ->setClinicUid($clinicUid)
        ->setOrderUid('')//если не устанавливать или передать пустую строку, то создастся новая заявка, иначе будет изменена старая
        ->setComment('Comment text')
        ->setAppointmentDuration(2700) // Не учитывается если указаны услуги (setServices). Также есть "особенности" апи УМЦ - длительность считается по услугам, только если для услуги указана индивидуальная длительность для конкретного врача. Длительность из карточки услуги не учитывается.
        ->setServices([$srvUid1, $srvUid2])
        ->setClientBirthday(DateTime::createFromFormat("d.m.Y", "05.08.1962"))
        ->build();

    $res = $exchangeService->sendOrder($order);

    //$res = $exchangeService->deleteOrder('8912c555-907a-11ee-9c3f-5e3a455eb0cf');

    if ($res->isSuccess())
    {
        Logger::print('Result success', $res->getData());
    }
    else
    {
        Logger::print('Result error', $res->getErrorMessages());
    }
}
catch (Exception $e)
{
    echo "Application error - " . $e->getMessage();
}