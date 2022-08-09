<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - DateTime.php
 * 10.08.2022 00:30
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Tools;

/**
 * Class DateTime
 * @package ANZ\BitUmc\SDK\Tools
 */
class DateTime extends \DateTime
{
    /**
     * formatting timestamp to ISO
     * @param int $timestamp
     * @return string
     */
    public static function formatTimestampToISO(int $timestamp): string
    {
        return (new static())->setTimestamp($timestamp)->format('Y-m-d\TH:i:s');
    }

    public static function formatDurationFromIsoToSeconds(string $isoTime): int
    {
        $minutes = date("i", strtotime($isoTime));
        $hours = date("H", strtotime($isoTime));
        return (int)$minutes*60 + (int)$hours*3600;
    }
}