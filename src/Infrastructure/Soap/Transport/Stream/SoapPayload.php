<?php

namespace ANZ\BitUmc\SDK\Infrastructure\Soap\Transport\Stream;

use ANZ\BitUmc\SDK\Domain\Exception\TransportException;

final class SoapPayload
{
    public function __construct(
        private readonly string $path,
    ) {
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function read(): string
    {
        $payload = file_get_contents($this->path);
        if ($payload === false) {
            throw new TransportException('Unable to read streamed SOAP payload.');
        }

        return $payload;
    }

    public function __destruct()
    {
        if (is_file($this->path)) {
            unlink($this->path);
        }
    }
}
