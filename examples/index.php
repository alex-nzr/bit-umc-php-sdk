<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');

use ANZ\BitUmc\SDK\Service\Api\UmcClient;
use ANZ\BitUmc\SDK\Service\Factory\WsFactory;
use ANZ\BitUmc\SDK\Tools\Debug;

try {
    $client = UmcClient::create()
        ->setLogin('siteIntegration')
        ->setPassword('123456')
        ->setHttps(false)
        ->setAddress('localhost:3500')
        ->setBaseName('umc_corp')
        ->init();

    $factory = new WsFactory($client);
    $reader  = $factory->getReader();

    Debug::print($reader->getClinics()->getData());
}
catch (Exception $e)
{
    echo $e->getMessage();
}