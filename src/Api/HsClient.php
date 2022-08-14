<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - HsClient.php
 * 06.08.2022 23:19
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Api;

use ANZ\BitUmc\SDK\Core\Contract\ApiClient;
use ANZ\BitUmc\SDK\Core\Operation\Result;

/**
 * Class HsClient
 * @package ANZ\BitUmc\SDK\Api
 */
class HsClient implements ApiClient
{

    /**
     * @param string $method
     * @param array $params
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function send(string $method, array $params): Result
    {
        return new Result;
    }

    /**
     * @return bool
     */
    public function isHsScope(): bool
    {
        return true;
    }
}