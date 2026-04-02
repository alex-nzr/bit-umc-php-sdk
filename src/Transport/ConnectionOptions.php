<?php

namespace ANZ\BitUmc\SDK\Transport;

use ANZ\BitUmc\SDK\Domain\Exception\InvalidArgumentException;
use ANZ\BitUmc\SDK\Transport\Auth\AuthInterface;

final class ConnectionOptions
{
    public function __construct(
        public readonly Protocol $protocol,
        public readonly string $host,
        public readonly string $baseName,
        public readonly AuthInterface $auth,
        public readonly ?string $apiKey = null,
        public readonly int $timeoutSeconds = 30,
        public readonly bool $verifyPeer = false,
    ) {
        $host = trim($this->host);
        $baseName = trim($this->baseName, " /\t\n\r\0\x0B");

        if ($host === '') {
            throw new InvalidArgumentException('Connection host can not be empty.');
        }

        if ($baseName === '') {
            throw new InvalidArgumentException('Connection baseName can not be empty.');
        }

        if ($this->timeoutSeconds <= 0) {
            throw new InvalidArgumentException('Connection timeoutSeconds must be greater than zero.');
        }
    }

    public function getResolvedApiKey(): ?string
    {
        return $this->apiKey ?: $this->auth->getApiKey();
    }
}
