<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - IBuilder.php
 * 06.08.2022 21:26
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Core\Contract;

/**
 * Interface IBuilder
 * @package ANZ\BitUmc\SDK\Core\Contract
 */
interface IBuilder
{
    /**
     * @return \ANZ\BitUmc\SDK\Core\Contract\IBuilder
     */
    public static function init(): static;

    /**
     * @return mixed
     */
    public function build(): mixed;
}