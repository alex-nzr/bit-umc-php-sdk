<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - Singleton.php
 * 04.08.2022 00:34
 * ==================================================
 */


namespace ANZ\BitUmc\SDK\Core\Trait;

/**
 * Trait Singleton
 * @package ANZ\BitUmc\SDK\Core\Trait
 */
trait Singleton
{
    private static mixed $instance = null;

    public static function getInstance(): mixed
    {
        if (static::$instance === null)
        {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function __construct()
    {
    }
    private function __clone()
    {
    }
    private function __wakeup()
    {
    }
}