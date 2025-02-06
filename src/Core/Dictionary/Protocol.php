<?php
/*
 * ==================================================
 * This file is part of project bit-umc-php-sdk
 * 26.11.2023
 * ==================================================
*/
namespace ANZ\BitUmc\SDK\Core\Dictionary;

enum Protocol: string
{
    case HTTP = 'http';
    case HTTPS = 'https';
}