<?php

namespace ANZ\BitUmc\SDK\Infrastructure\Soap;

use stdClass;

final class SoapOperation
{
    public function __construct(
        public readonly SoapMethod $method,
        public readonly stdClass $payload,
    ) {
    }
}
