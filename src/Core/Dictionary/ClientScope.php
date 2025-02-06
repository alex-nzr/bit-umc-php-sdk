<?php
/*
 * ==================================================
 * This file is part of project bit-umc-php-sdk
 * 25.11.2023
 * ==================================================
*/
namespace ANZ\BitUmc\SDK\Core\Dictionary;

enum ClientScope: string
{
    case HTTP_SERVICE = 'hs';
    case WEB_SERVICE = 'ws';
}