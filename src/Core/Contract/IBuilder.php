<?php
/*
 * ==================================================
 * This file is part of project bit-umc-php-sdk
 * 06.08.2022
 * ==================================================
*/
namespace ANZ\BitUmc\SDK\Core\Contract;

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