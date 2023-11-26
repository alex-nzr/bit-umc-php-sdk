<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');

use ANZ\BitUmc\SDK\Core\Enumeration\ClientScope;
use ANZ\BitUmc\SDK\Core\Enumeration\Protocol;
use ANZ\BitUmc\SDK\Service\Builder\ClientBuilder;
use ANZ\BitUmc\SDK\Service\Builder\OrderBuilder;
use ANZ\BitUmc\SDK\Service\Factory\ServiceFactory;

try {
    $client = ClientBuilder::init()
                ->setLogin('siteIntegration')
                ->setPassword('123456')
                ->setPublicationProtocol(Protocol::HTTP)
                ->setPublicationAddress('1c.nivlako.keenetic.pro')
                ->setBaseName('umc')
                ->setScope(ClientScope::WEB_SERVICE)
                ->build();

    $factory = ServiceFactory::initByClient($client);
    $reader  = $factory->getReader();
    $writer  = $factory->getWriter();

    $clinicUid = 'f679444a-22b7-11df-8618-002618dcef2c';//'a78cbf94-24bd-11eb-baa7-1c1b0d51378c';
    $empUid1   = 'ac30e13a-3087-11dc-8594-005056c00008';//'0aeb21f1-cff1-4ff9-b3c1-721cf67f3968';
    $empUid2   = '99868528-0928-11dc-93d1-0004614ae652';//'f3151797-457d-487c-bb5f-e6e27ce9e180';
    $srvUid1   = '22d1b486-b34b-11de-8171-001583078ee5';//'964759cf-de9c-11e9-9383-50af73235947';
    $srvUid2   = '22d1b484-b34b-11de-8171-001583078ee5';//'bd439810-de9c-11e9-9383-50af73235947';

    $res = $reader->getClinics();
    //$res = $reader->getEmployees();
    //$res = $reader->getNomenclature($clinicUid);
    //$res = $reader->getSchedule(1, $clinicUid, [ $empUid1, $empUid2 ]);
    //$res = $reader->getOrderStatus('39d9b2f9-35db-11ed-9bf2-5e3a455eb0cf');

    //В качестве даты и времени записи передаётся объект \DateTime, созданный любым удобным способом
    $dateTimeBegin = \DateTime::createFromFormat("d.m.Y H:i:s", "21.09.2022 14:00:00");

    /*$waitList   = OrderBuilder::createWaitList()
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

    $res = $writer->sendWaitList($waitList);*/

    /*$reserve   = OrderBuilder::createReserve()
        ->setClinicUid($clinicUid)
        ->setSpecialtyName('Стоматология')
        ->setEmployeeUid($empUid1)
        ->setDateTimeBegin($dateTimeBegin)
        ->build();
    $res = $writer->sendReserve($reserve);

    $orderUid = $res->getData()['uid'];*/

    /*$order   = OrderBuilder::createOrder()
        ->setEmployeeUid($empUid1)
        ->setName('Антон')
        ->setLastName('Печкин')
        ->setSecondName('Павлович')
        ->setDateTimeBegin($dateTimeBegin)
        ->setPhone("+79000803126")
        ->setEmail('ppp@gmail.com')
        ->setAddress('г. Москва, проспект Ленина 46')
        ->setClinicUid($clinicUid)
        ->setOrderUid('39d9b2f9-35db-11ed-9bf2-5e3a455eb0cf')//если не устанавливать или передать пустую строку, то создастся новая заявка, иначе будет изменена старая
        ->setComment('Comment text')
        ->setAppointmentDuration(2700) // не учитывается если указаны услуги (setServices)
        ->setServices([$srvUid1, $srvUid2])
        ->setClientBirthday(\DateTime::createFromFormat("d.m.Y", "05.08.1962"))
        ->build();

    $res = $writer->sendOrder($order);*/

    //$res = $writer->deleteOrder('54cd6a8e-3912-11ed-9bf2-5e3a455eb0cf');

    if ($res->isSuccess())
    {
        \ANZ\BitUmc\SDK\Debug\Logger::print('Result success', $res->getData());
    }
    else
    {
        \ANZ\BitUmc\SDK\Debug\Logger::print('Result error', $res->getErrorMessages());
    }
}
catch (Exception $e)
{
    echo "Application error - " . $e->getMessage();
}