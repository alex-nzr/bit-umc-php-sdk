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
    protected static ?object $instance = null;

    /**
     * @return object
     */
    public static function getInstance(): object
    {
        if (empty(static::$instance))
        {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @return void
     */
    final public function __clone()
    {
    }

    /**
     * @return void
     */
    final public function __wakeup()
    {
    }

    /**
     * @return void
     */
    final public function __sleep()
    {
    }
}