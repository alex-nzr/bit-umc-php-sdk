<?php

namespace ANZ\BitUmc\SDK\Infrastructure\Soap\Transport\Stream;

use ANZ\BitUmc\SDK\Domain\Exception\ResponseParseException;

final class SoapEnvelopeExtractionState
{
    private int $depth = 0;
    private ?int $returnDepth = null;
    private ?int $faultDepth = null;
    private int $payloadBytes = 0;
    private bool $hasPayload = false;
    private string $faultText = '';

    public function __construct(
        private readonly mixed $payloadStream,
        private readonly int $maxBytes,
    ) {
    }

    public function startElement(\XMLParser $parser, string $name): void
    {
        $this->depth++;
        if ($this->localName($name) === 'return') {
            if ($this->returnDepth !== null) {
                throw new ResponseParseException('SOAP response contains nested return payloads.');
            }

            $this->returnDepth = $this->depth;
            $this->hasPayload = true;
        }

        if ($this->localName($name) === 'Fault') {
            $this->faultDepth = $this->depth;
        }
    }

    public function endElement(\XMLParser $parser, string $name): void
    {
        if ($this->returnDepth === $this->depth && $this->localName($name) === 'return') {
            $this->returnDepth = null;
        }

        if ($this->faultDepth === $this->depth && $this->localName($name) === 'Fault') {
            $this->faultDepth = null;
        }

        $this->depth--;
    }

    public function readText(\XMLParser $parser, string $data): void
    {
        if ($this->returnDepth === null || $this->depth < $this->returnDepth) {
            if ($this->faultDepth !== null && $this->depth >= $this->faultDepth) {
                $this->faultText .= $data;
            }

            return;
        }

        $this->payloadBytes += strlen($data);
        if ($this->payloadBytes > $this->maxBytes || fwrite($this->payloadStream, $data) === false) {
            throw new ResponseParseException('SOAP response payload exceeds the configured limit or can not be stored.');
        }
    }

    public function hasPayload(): bool
    {
        return $this->hasPayload;
    }

    public function hasFault(): bool
    {
        return trim($this->faultText) !== '';
    }

    public function getFaultMessage(): string
    {
        $message = trim(preg_replace('/\s+/', ' ', $this->faultText) ?? '');

        return $message === '' ? 'SOAP response contains a fault.' : 'SOAP response contains a fault: ' . $message;
    }

    private function localName(string $name): string
    {
        $separatorPosition = strrpos($name, '|');

        return $separatorPosition === false ? $name : substr($name, $separatorPosition + 1);
    }
}
