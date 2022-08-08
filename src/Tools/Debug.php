<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - Debug.php
 * 05.08.2022 21:31
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Tools;

/**
 * Class Debug
 * @package ANZ\BitUmc\SDK\Tools
 */
class Debug
{
    const PATH_TO_LOG_FILE = "/log/log.txt";

    /**
     * @param ...$vars
     */
    public static function print(...$vars): void
    {
        foreach ($vars as $var)
        {
            echo "<pre>";
            print_r($var);
            echo "</pre>";
        }
    }

    public static function printLog(...$vars): void
    {
        foreach ($vars as $var)
        {
            $log = date("d.m.Y H:i:s") . PHP_EOL . print_r($var, true);
            file_put_contents(
                $_SERVER['DOCUMENT_ROOT'] . static::PATH_TO_LOG_FILE,
                $log . PHP_EOL,
                FILE_APPEND
            );
        }
    }
}