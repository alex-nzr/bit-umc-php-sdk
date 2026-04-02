<?php

namespace ANZ\BitUmc\SDK\Transport\Endpoint;

use ANZ\BitUmc\SDK\Transport\ConnectionOptions;
use ANZ\BitUmc\SDK\Transport\TransportType;

final class EndpointResolver
{
    public function __construct(
        private readonly string $soapServicePath = 'ws/Integration',
    ) {
    }

    public function resolveWsdl(ConnectionOptions $options): string
    {
        return sprintf('%s://%s/%s/%s?wsdl', $options->protocol->value, $this->normalizeHost($options->host), $this->normalizeBaseName($options->baseName), $this->normalizePath($this->soapServicePath));
    }

    public function resolveSoapLocation(ConnectionOptions $options): string
    {
        return sprintf('%s://%s/%s/%s', $options->protocol->value, $this->normalizeHost($options->host), $this->normalizeBaseName($options->baseName), $this->normalizePath($this->soapServicePath));
    }

    public function resolveBaseEndpoint(TransportType $transportType, ConnectionOptions $options): string
    {
        $segment = match ($transportType) {
            TransportType::SOAP => $this->normalizePath($this->soapServicePath),
            TransportType::HTTP => 'hs/bwi/',
        };

        return sprintf('%s://%s/%s/%s', $options->protocol->value, $this->normalizeHost($options->host), $this->normalizeBaseName($options->baseName), $segment);
    }

    private function normalizeHost(string $host): string
    {
        return trim($host);
    }

    private function normalizeBaseName(string $baseName): string
    {
        return trim($baseName, " /\t\n\r\0\x0B");
    }

    private function normalizePath(string $path): string
    {
        return trim($path, " /\t\n\r\0\x0B");
    }
}
