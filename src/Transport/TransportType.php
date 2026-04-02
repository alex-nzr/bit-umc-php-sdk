<?php

namespace ANZ\BitUmc\SDK\Transport;

enum TransportType: string
{
    case SOAP = 'soap';
    case HTTP = 'http';
}
