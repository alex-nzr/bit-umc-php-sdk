<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - Logger.php
 * 26.11.2023 21:44
 * ==================================================
 */
namespace ANZ\BitUmc\SDK\Debug;

use Psr\Log\AbstractLogger;
use Stringable;

/**
 * @class Logger
 * @package ANZ\BitUmc\SDK\Debug
 */
class Logger extends AbstractLogger
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

    /**
     * @param ...$vars
     * @return void
     */
    public static function printToFile(...$vars): void
    {
        $logger = new static();
        foreach ($vars as $var)
        {
            $logger->debug(print_r($var, true));
        }
    }

    /**
     * @param $level
     * @param \Stringable|string $message
     * @param array $context
     * @return void
     */
    public function log($level, Stringable|string $message, array $context = []): void
    {
        $log = date("d.m.Y H:i:s") . PHP_EOL . $message;
        file_put_contents(
            $_SERVER['DOCUMENT_ROOT'] . static::PATH_TO_LOG_FILE,
            $log . PHP_EOL,
            FILE_APPEND
        );
    }
}