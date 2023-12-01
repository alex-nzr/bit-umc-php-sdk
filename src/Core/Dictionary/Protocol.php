<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - Protocol.php
 * 26.11.2023 19:23
 * ==================================================
 */


namespace ANZ\BitUmc\SDK\Core\Dictionary;

/**
 * @enum Protocol
 * @package ANZ\BitUmc\SDK\Core\Dictionary
 */
enum Protocol: string
{
    case HTTP = 'http';
    case HTTPS = 'https';
}