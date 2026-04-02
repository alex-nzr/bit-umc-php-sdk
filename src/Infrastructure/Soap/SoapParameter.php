<?php

namespace ANZ\BitUmc\SDK\Infrastructure\Soap;

use stdClass;

final class SoapParameter extends stdClass
{
    public function __construct(protected string $name, protected string $Value)
    {
    }
}
