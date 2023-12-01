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

use DateTime;

/**
 * Class DateTime
 * @package ANZ\BitUmc\SDK\Tools
 */
class DateFormatter
{
    /**
     * formatting timestamp to ISO
     * @param int $timestamp
     * @return string
     */
    public static function formatTimestampToISO(int $timestamp): string
    {
        return (new DateTime())->setTimestamp($timestamp)->format('Y-m-d\TH:i:s');
    }

    /**
     * @param string $isoTime
     * @return int
     */
    public static function formatDurationFromIsoToSeconds(string $isoTime): int
    {
        $minutes = date("i", strtotime($isoTime));
        $hours = date("H", strtotime($isoTime));
        return (int)$minutes*60 + (int)$hours*3600;
    }

    /**
     * @param int $seconds
     * @return string
     */
    public static function calculateDurationFromSeconds(int $seconds): string
    {
        $hours = ($seconds >= 3600) ? round($seconds / 3600) : 0;
        $minutes = round(($seconds % 3600) / 60);

        $hours   = ($hours > 9) ? $hours : "0".$hours;
        $minutes = ($minutes > 9) ? $minutes : "0".$minutes;

        return "0001-01-01T".$hours.":".$minutes.":00";
    }

    /**
     * @param string $timeBegin
     * @param string $timeEnd
     * @return string
     * @throws \Exception
     */
    public static function calculateDurationFromInterval(string $timeBegin, string $timeEnd): string
    {
        $startDate = new DateTime($timeBegin);
        $diff = $startDate->diff(new DateTime($timeEnd));

        $hours   = ($diff->h > 9) ? $diff->h : "0".$diff->h;
        $minutes = ($diff->i > 9) ? $diff->i : "0".$diff->i;

        return "0001-01-01T".$hours.":".$minutes.":00";
    }
}