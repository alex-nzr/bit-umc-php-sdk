<?php

namespace ANZ\BitUmc\SDK\Infrastructure\Http;

use ANZ\BitUmc\SDK\Domain\Exception\UnsupportedTransportException;

final class HttpTransport
{
    public function __construct()
    {
        throw new UnsupportedTransportException('HTTP transport is not implemented yet. The reserved apiKey option will be used in the next stage.');
    }
}
