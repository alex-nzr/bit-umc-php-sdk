<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - ApiClient.php
 * 04.08.2022 02:06
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Core\Contract;

use ANZ\BitUmc\SDK\Core\Operation\Result;

/**
 * Interface ApiClientInterface
 * @package ANZ\BitUmc\SDK\Core\Contract
 */
interface ApiClient
{
    /**
     * @return string
     */
    public function getScope(): string;

    /**
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public function send(string $method, array $params): Result;
}