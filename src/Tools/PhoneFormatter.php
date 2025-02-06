<?php
/*
 * ==================================================
 * This file is part of project bit-umc-php-sdk
 * 04.08.2022
 * ==================================================
*/
namespace ANZ\BitUmc\SDK\Tools;

class PhoneFormatter
{
    /**
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
}