<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - ClientScope.php
 * 25.11.2023 03:29
 * ==================================================
 */
namespace ANZ\BitUmc\SDK\Core\Dictionary;

/**
 * @enum ClientScope
 * @package ANZ\BitUmc\SDK\Core\Dictionary
 */
enum ClientScope: string
{
    case HTTP_SERVICE = 'hs';
    case WEB_SERVICE = 'ws';
}