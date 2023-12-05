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
    protected static mixed $instance = null;

    /**
     * @return static
     */
    public static function getInstance(): static
    {
        if (empty(static::$instance))
        {
            static::$instance = new static();
        }
        return static::$instance;
    }

    protected function __construct(){}

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