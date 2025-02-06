<?php
/*
 * ==================================================
 * This file is part of project bit-umc-php-sdk
 * 04.08.2022
 * ==================================================
*/
namespace ANZ\BitUmc\SDK\Core\Trait;

trait Singleton
{
    protected static array $instances = [];

    public static function getInstance()
    {
        if (!key_exists(static::class, static::$instances) || !(static::$instances[static::class] instanceof static))
        {
            static::$instances[static::class] = new static();
        }
        return static::$instances[static::class];
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