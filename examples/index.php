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
                ->setBaseName('umc')
                ->build();

    $factory = new ServiceFactory($client);
    $reader  = $factory->getReader();
    $writer  = $factory->getWriter();

    //$res = $reader->getClinics();
    //$res = $reader->getEmployees();
    //$res = $reader->getNomenclature('f679444a-22b7-11df-8618-002618dcef2c');
    /*$res = $reader->getSchedule(14, 'f679444a-22b7-11df-8618-002618dcef2c', [
        '19cb6fa5-1578-11ed-9bee-5c3a455eb0d0', '99868528-0928-11dc-93d1-0004614ae652',
        '2eb1f97b-6a3c-11e9-936d-1856809fe650'
    ]);*/
    //$res = $reader->getOrderStatus('1a5abb39-1ef0-11ed-9bef-5e3a455eb0cf');

    //В качестве даты передаётся объект \DateTime, созданный любым удобным способом
    $date      = new \DateTime('2022-08-19T00:00:00');
    $timeBegin = \DateTime::createFromFormat("d.m.Y H:i:s", "19.08.2022 09:00:00");

    /*$reserve   = OrderBuilder::createReserve()
        ->setClinicUid('f679444a-22b7-11df-8618-002618dcef2c')
        ->setSpecialtyName('Терапия')
        ->setEmployeeUid('19cb6fa5-1578-11ed-9bee-5c3a455eb0d0')
        ->setDate($date)
        ->setTimeBegin($timeBegin)
        ->build();
    $res = $writer->sendReserve($reserve);*/

    $waitList   = OrderBuilder::createWaitList()
        ->setSpecialtyName('Терапия')
        ->setName('Иван')
        ->setLastName('Иванов')
        ->setSecondName('Иванович')
        ->setDate($date)
        ->setTimeBegin($timeBegin)
        ->setPhone("+79000803125")
        ->setEmail('example@gmail.com')
        ->setAddress('г. Москва, проспект Ленина 45')
        ->setClinicUid('f679444a-22b7-11df-8618-002618dcef2c')
        ->setComment('Comment text')
        ->build();

    $res = $writer->sendWaitList($waitList);
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