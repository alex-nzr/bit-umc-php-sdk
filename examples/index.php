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

    $res = $reader->getEmployees();
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