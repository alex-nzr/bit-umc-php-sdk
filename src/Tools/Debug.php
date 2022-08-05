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
    public static function print(...$vars)
    {
        foreach ($vars as $var)
        {
            echo "<pre>";
            print_r($var);
            echo "</pre>";
        }
    }
}