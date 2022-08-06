<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - BuilderInterface.php
 * 06.08.2022 21:26
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Core\Contract;

/**
 * Interface BuilderInterface
 * @package ANZ\BitUmc\SDK\Core\Contract
 */
interface BuilderInterface
{
    public static function init(): BuilderInterface;
    public function build(): mixed;
}