<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');

use ANZ\BitUmc\SDK\Service\Builder\ClientBuilder;
use ANZ\BitUmc\SDK\Service\Factory\ServiceFactory;
use ANZ\BitUmc\SDK\Tools\Debug;

try {
    $client = ClientBuilder::init()
                ->setLogin('siteIntegration')
                ->setPassword('123456')
                ->setHttps(false)
                ->setAddress('localhost:3500')
                ->setBaseName('umc')
                ->setWsScope()
                ->build();

    $factory = new ServiceFactory($client);
    $reader  = $factory->getReader();
    $writer  = $factory->getWriter();

    //$res = $reader->getClinics();
    //$res = $reader->getEmployees();
    //$res = $reader->getNomenclature('f679444a-22b7-11df-8618-002618dcef2c');
    $res = $reader->getSchedule(14, 'f679444a-22b7-11df-8618-002618dcef2c', [
        '19cb6fa5-1578-11ed-9bee-5c3a455eb0d0', '99868528-0928-11dc-93d1-0004614ae652'
    ]);
    if ($res->isSuccess())
    {
        Debug::print('Success', $res->getData());
    }
    else
    {
        Debug::print('Error', $res->getErrorMessages());
    }
}
catch (Exception $e)
{
    echo $e->getMessage();
}