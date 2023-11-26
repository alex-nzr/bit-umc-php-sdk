<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - Common.php
 * 04.08.2022 01:16
 * ==================================================
 */
namespace ANZ\BitUmc\SDK\Service\OneC;

use ANZ\BitUmc\SDK\Core\Contract\Connection\IClient;
use ANZ\BitUmc\SDK\Core\Contract\Service\IService;
use ANZ\BitUmc\SDK\Core\Operation\Result;

/**
 * Class Common
 * @package ANZ\BitUmc\SDK\Service\OneC
 */
abstract class Common implements IService
{
    protected IClient $client;

    /**
     * Common constructor.
     */
    public function __construct(IClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $method
     * @param array $params
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function getResponse(string $method, array $params = []): Result
    {
        return $this->client->send($method, $params);
    }
}