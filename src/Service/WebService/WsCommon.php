<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - BaseService.php
 * 04.08.2022 01:16
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Service\OneC;

use ANZ\BitUmc\SDK\Core\Contract\ApiClient;
use ANZ\BitUmc\SDK\Core\Contract\ServiceInterface;
use ANZ\BitUmc\SDK\Core\Contract\Result;

/**
 * Class BaseService
 * @package ANZ\BitUmc\SDK\Service\OneC
 */
abstract class BaseService implements ServiceInterface
{
    private ApiClient $client;

    /**
     * BaseService constructor.
     */
    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $method
     * @param array $params
     * @return \ANZ\BitUmc\SDK\Core\Contract\Result
     */
    public function getResponse(string $method, array $params = []): Result
    {
        return $this->client->send($method, $params);
    }
}