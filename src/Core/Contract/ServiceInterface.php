<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - ServiceInterface.php
 * 05.08.2022 23:46
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Core\Contract;

/**
 * Interface ServiceInterface
 * @package ANZ\BitUmc\SDK\Core\Contract
 */
interface ServiceInterface
{
    public function getResponse(string $method, array $params = []): Result;
}