<?php
/*
 * ==================================================
 * This file is part of project bit-umc-php-sdk
 * 26.11.2023
 * ==================================================
*/
namespace ANZ\BitUmc\SDK\Core\Contract;

use ANZ\BitUmc\SDK\Core\Contract\Connection\IClient;
use ANZ\BitUmc\SDK\Core\Contract\Service\IExchangeService;

interface IFactory
{
    /**
     * @return mixed
     */
    public function create(): mixed;
}