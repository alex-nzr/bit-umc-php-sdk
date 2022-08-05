<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - Container.php
 * 04.08.2022 01:28
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Service;

use ANZ\BitUmc\SDK\Core\Contract\ApiClientInterface;
use ANZ\BitUmc\SDK\Core\DI\ServiceLocator;
use ANZ\BitUmc\SDK\Core\Trait\Singleton;
use ANZ\BitUmc\SDK\Service\Api\UmcClient;
use ANZ\BitUmc\SDK\Service\OneC\Reader;
use ANZ\BitUmc\SDK\Service\OneC\Writer;

/**
 * Class Container
 * @package ANZ\BitUmc\SDK\Service
 * @method static Container getInstance()
 */
class Container
{
     use Singleton;

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    private function __construct()
     {
         ServiceLocator::getInstance()->add('umc.sdk.writer', Writer::class);
         ServiceLocator::getInstance()->add('umc.sdk.reader', Reader::class);
     }

    /**
     * @param string $wsUrl
     * @param string $login
     * @param string $password
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClientInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
     public function getApiClient(string $wsUrl, string $login, string $password): ApiClientInterface
     {
         //TODO переделать - клиент будет создаваться конструктором и под капотом юзать контейнер сервисов
         //TODO сервис-локатор переделать на фабрику
         if (!ServiceLocator::getInstance()->has('umc.sdk.client'))
         {
             ServiceLocator::getInstance()->add('umc.sdk.client', UmcClient::class, [
                 $wsUrl,
                 $login,
                 $password
             ]);
         }

         return ServiceLocator::getInstance()->get('umc.sdk.client');
     }
}