<?php

namespace ANZ\BitUmc\SDK\Infrastructure\Soap\Transport;

use ANZ\BitUmc\SDK\Domain\Exception\InvalidArgumentException;
use ANZ\BitUmc\SDK\Domain\Exception\TransportException;
use ANZ\BitUmc\SDK\Infrastructure\Soap\SoapOperation;
use ANZ\BitUmc\SDK\Infrastructure\Soap\Transport\Stream\SoapEnvelopeExtractor;
use ANZ\BitUmc\SDK\Infrastructure\Soap\Transport\Stream\SoapPayload;
use ANZ\BitUmc\SDK\Infrastructure\Soap\Transport\Stream\SoapRequestCaptureClient;
use ANZ\BitUmc\SDK\Transport\ConnectionOptions;
use ANZ\BitUmc\SDK\Transport\Endpoint\EndpointResolver;
use SoapClient;
use Throwable;

final class StreamingSoapTransport
{
    public function __construct(
        private readonly ConnectionOptions $options,
        private readonly EndpointResolver $endpointResolver = new EndpointResolver(),
        private readonly SoapEnvelopeExtractor $envelopeExtractor = new SoapEnvelopeExtractor(),
    ) {
        if (!class_exists(SoapClient::class)) {
            throw new TransportException('SOAP extension not found.');
        }

        if (($this->options->auth->getLogin() ?? '') === '' || ($this->options->auth->getPassword() ?? '') === '') {
            throw new InvalidArgumentException('SOAP transport requires runtime login and password in ConnectionOptions auth.');
        }
    }

    public function execute(SoapOperation $operation): SoapPayload
    {
        try {
            $capturedRequest = $this->captureRequest($operation);
            $stream = fopen($this->endpointResolver->resolveSoapLocation($this->options), 'rb', false, $this->createStreamContext($capturedRequest->request, $capturedRequest->action));
            $responseHeaders = $http_response_header ?? [];
            if ($stream === false) {
                throw new TransportException('Unable to open SOAP response stream.');
            }

            $this->assertSuccessfulHttpResponse($responseHeaders);

            try {
                return $this->envelopeExtractor->extract($stream, $this->options->responseMaxBytes);
            } finally {
                fclose($stream);
            }
        } catch (TransportException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw new TransportException($exception->getMessage(), (int) $exception->getCode(), $exception);
        }
    }

    /**
     * @throws \SoapFault
     */
    private function captureRequest(SoapOperation $operation): SoapRequestCaptureClient
    {
        $client = new SoapRequestCaptureClient($this->endpointResolver->resolveWsdl($this->options), $this->getSoapOptions());
        $client->__soapCall($operation->method->value, [$operation->payload], ['one_way' => 1]);

        if ($client->request === '') {
            throw new TransportException('Unable to capture SOAP request.');
        }

        return $client;
    }

    private function createStreamContext(string $request, string $action)
    {
        $authorization = base64_encode($this->options->auth->getLogin() . ':' . $this->options->auth->getPassword());
        $headers = [
            'Authorization: Basic ' . $authorization,
            'Content-Type: application/soap+xml; charset=utf-8; action="' . $action . '"',
            'Connection: close',
        ];

        return stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $request,
                'timeout' => $this->options->timeoutSeconds,
                'ignore_errors' => true,
                'follow_location' => 0,
            ],
            'ssl' => [
                'verify_peer' => $this->options->verifyPeer,
                'verify_peer_name' => $this->options->verifyPeer,
            ],
        ]);
    }

    private function assertSuccessfulHttpResponse(array $headers): void
    {
        $statusLine = $headers[0] ?? '';
        if (!is_string($statusLine) || $statusLine === '') {
            throw new TransportException('SOAP transport did not return an HTTP status line.');
        }

        if (!preg_match('/^HTTP\/\S+\s+(\d{3})\b/', $statusLine, $matches)) {
            throw new TransportException('SOAP transport returned an unexpected HTTP status line.');
        }

        $statusCode = (int) $matches[1];
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new TransportException(sprintf('SOAP transport returned HTTP status %d.', $statusCode));
        }
    }

    private function getSoapOptions(): array
    {
        return [
            'login' => $this->options->auth->getLogin(),
            'password' => $this->options->auth->getPassword(),
            'soap_version' => SOAP_1_2,
            'location' => $this->endpointResolver->resolveSoapLocation($this->options),
            'cache_wsdl' => WSDL_CACHE_NONE,
            'exceptions' => true,
            'connection_timeout' => $this->options->timeoutSeconds,
            'keep_alive' => false,
        ];
    }
}
