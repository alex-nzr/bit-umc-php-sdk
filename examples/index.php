<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');

use ANZ\BitUmc\SDK\Service\Api\UmcClient;

try {
    $client = UmcClient::create()
        ->setLogin('siteIntegration')
        ->setPassword('123456')
        ->setHttps(false)
        ->setAddress('localhost:3500')
        ->setBaseName('umc_corp')
        ->init();

    echo "<pre>";
    print_r(json_decode($client->getClinics()->getContent(), true));
    echo "</pre>";
}
catch (Exception $e)
{
    echo $e->getMessage();
}