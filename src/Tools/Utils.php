<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - Utils.php
 * 04.08.2022 22:31
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Tools;

use DateTime;

/**
 * Class Utils
 * @package ANZ\BitUmc\SDK\Tools
 */
class Utils
{
    private function __construct(){}

    /**
     * phone number formatting
     * @param string $phone
     * @return string
     */
    public static function formatPhone(string $phone): string
    {
        $phone = preg_replace(
            '/[^0-9]/',
            '',
            $phone);

        if(strlen($phone) > 10)
        {
            $phone = substr($phone, -10);
            return  '+7' . $phone;
        }
        else
        {
            return  $phone;
        }
    }

    /**
     * creates array of date interval
     * @param int $interval
     * @return array
     */
    public static function getDateInterval(int $interval): array
    {
        $start  = self::formatDateToISO(strtotime('today + 1 days'));
        $end    = self::formatDateToISO(strtotime('today + ' . $interval . ' days'));
        return [
            "StartDate" => $start,
            "FinishDate" => $end,
        ];
    }

    /**
     * formatting timestamp to ISO
     * @param int $timestamp
     * @return string
     */
    public static function formatDateToISO(int $timestamp): string
    {
        return (new DateTime())->setTimestamp($timestamp)->format('Y-m-d\TH:i:s');
    }

    public static function formatDurationToSeconds(string $isoTime): int
    {
        $minutes = date("i", strtotime($isoTime));
        $hours = date("H", strtotime($isoTime));
        return (int)$minutes*60 + (int)$hours*3600;
    }

    /**
     * Tests if an array is associative or not.
     * @param array array to check
     * @return boolean
     */
    public static function is_assoc(array $array): bool
    {
        if (!is_array($array)){
            return false;
        }

        // Keys of the array
        $keys = array_keys($array);
        // If the array keys of the keys match the keys, then the array must
        // not be associative (e.g. the keys array looked like {0:0, 1:1...}).
        return array_keys($keys) !== $keys;
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
     * @param string $errorMessage
     * @return string
     */
    public static function getErrorResponse(string $errorMessage): string
    {
        return json_encode(['error' => $errorMessage]);
    }
}