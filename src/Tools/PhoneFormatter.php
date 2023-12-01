<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - PhoneFormatter.php
 * 04.08.2022 22:31
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Tools;

/**
 * Class PhoneFormatter
 * @package ANZ\BitUmc\SDK\Tools
 */
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