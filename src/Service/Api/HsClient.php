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

namespace ANZ\BitUmc\SDK\Service\Api;

use ANZ\BitUmc\SDK\Config\Constants;
use ANZ\BitUmc\SDK\Core\Contract\ApiClient;
use ANZ\BitUmc\SDK\Core\Operation\Result;
use JetBrains\PhpStorm\Pure;

class HsClient implements ApiClient
{

    /**
     * @param string $method
     * @param array $params
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    #[Pure]
    public function send(string $method, array $params): Result
    {
        return new Result;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return Constants::HS_SCOPE;
    }
}