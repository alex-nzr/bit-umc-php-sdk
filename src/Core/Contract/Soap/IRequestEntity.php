<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - IRequestEntity.php
 * 29.11.2023 01:22
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Core\Contract\Soap;

/**
 * @interface IRequestEntity
 * @package ANZ\BitUmc\SDK\Core\Contract\Soap
 */
interface IRequestEntity
{
    public function getRequestMethod(): string;
}