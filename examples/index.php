<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');

use ANZ\BitUmc\SDK\Service\Builder\ClientBuilder;
use ANZ\BitUmc\SDK\Service\Builder\OrderBuilder;
use ANZ\BitUmc\SDK\Service\Factory\ServiceFactory;
use ANZ\BitUmc\SDK\Tools\Debug;

try {
    $client = ClientBuilder::init()
                ->setLogin('siteIntegration')
                ->setPassword('123456')
                ->setHttps(false)
                ->setAddress('localhost:3500')
                ->setBaseName('tempTestUmc')
                ->build();

    $factory = new ServiceFactory($client);
    $reader  = $factory->getReader();
    $writer  = $factory->getWriter();

    //$res = $reader->getClinics();
    //$res = $reader->getEmployees();
    //$res = $reader->getNomenclature('a78cbf94-24bd-11eb-baa7-1c1b0d51378c');
    /*$res = $reader->getSchedule(14, 'a78cbf94-24bd-11eb-baa7-1c1b0d51378c', [
        '0aeb21f1-cff1-4ff9-b3c1-721cf67f3968',
        'f3151797-457d-487c-bb5f-e6e27ce9e180'
    ]);*/
    //$res = $reader->getOrderStatus('ddc9234f-1fee-11ed-9bef-5e3a455eb0cf');

    //В качестве даты и времени записи передаётся объект \DateTime, созданный любым удобным способом
    $dateTimeBegin = \DateTime::createFromFormat("d.m.Y H:i:s", "14.09.2022 08:00:00");

    /*$waitList   = OrderBuilder::createWaitList()
        ->setSpecialtyName('Стоматология ПУ')
        ->setName('Иван')
        ->setLastName('Иванов')
        ->setSecondName('Иванович')
        ->setDateTimeBegin($dateTimeBegin)
        ->setPhone("+7 (915) 5415935")
        ->setEmail('example@gmail.com')
        ->setAddress('г. Москва, проспект Ленина 45')
        ->setClinicUid('a78cbf94-24bd-11eb-baa7-1c1b0d51378c')
        ->setComment('Comment text')
        ->build();

    $waitListRes = $writer->sendWaitList($waitList);*/

    /*$reserve   = OrderBuilder::createReserve()
        ->setClinicUid('a78cbf94-24bd-11eb-baa7-1c1b0d51378c')
        ->setSpecialtyName('Стоматология ПУ')
        ->setEmployeeUid('0aeb21f1-cff1-4ff9-b3c1-721cf67f3968')
        ->setDateTimeBegin($dateTimeBegin)
        ->build();
    $res = $writer->sendReserve($reserve);*/

    $order   = OrderBuilder::createOrder()
        ->setEmployeeUid('0aeb21f1-cff1-4ff9-b3c1-721cf67f3968')
        ->setName('Антон')
        ->setLastName('Пехота')
        ->setSecondName('Павлович')
        ->setDateTimeBegin($dateTimeBegin)
        ->setPhone("+79000803126")
        ->setEmail('ppp@gmail.com')
        ->setAddress('г. Москва, проспект Ленина 46')
        ->setClinicUid('a78cbf94-24bd-11eb-baa7-1c1b0d51378c')
        ->setReserveUid('049f00c5-3391-11ed-9bf2-5e3a455eb0cf')
        ->setComment('Comment text')

        ->setAppointmentDuration(2700)

        ->setServices([
            '964759cf-de9c-11e9-9383-50af73235947',
            '964759e2-de9c-11e9-9383-50af73235947',
            'bd439810-de9c-11e9-9383-50af73235947'
        ])

        ->setClientBirthday(\DateTime::createFromFormat("d.m.Y", "05.08.1962"))
        ->build();

    $res = $writer->sendOrder($order);

    if ($res->isSuccess())
    {
        Debug::print('Result success', $res->getData());
    }
    else
    {
        Debug::print('Result error', $res->getErrorMessages());
    }
}
catch (Exception $e)
{
    echo "Application error - " . $e->getMessage();
}