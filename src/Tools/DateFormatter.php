<?php
/*
 * ==================================================
 * This file is part of project bit-umc-php-sdk
 * 10.08.2022
 * ==================================================
*/
namespace ANZ\BitUmc\SDK\Tools;

use DateTime;

class DateFormatter
{
    public static function formatDurationFromIsoToSeconds(string $isoTime): int
    {
        $timestamp = strtotime($isoTime);
        if ($timestamp === false) {
            return 0;
        }

        $minutes = date("i", $timestamp);
        $hours = date("H", $timestamp);
        return (int)$minutes*60 + (int)$hours*3600;
    }

    public static function calculateDurationFromSeconds(int $seconds): string
    {
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        $hours   = ($hours > 9) ? $hours : "0".$hours;
        $minutes = ($minutes > 9) ? $minutes : "0".$minutes;

        return "0001-01-01T".$hours.":".$minutes.":00";
    }

    /**
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

    public static function formatTimestampToISO(int $timestamp): string
    {
        return (new DateTime())->setTimestamp($timestamp)->format('Y-m-d\TH:i:s');
    }
}
