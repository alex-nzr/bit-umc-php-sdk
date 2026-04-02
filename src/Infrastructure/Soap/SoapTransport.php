<?php

namespace ANZ\BitUmc\SDK\Infrastructure\Soap;

use ANZ\BitUmc\SDK\Domain\Exception\InvalidArgumentException;
use ANZ\BitUmc\SDK\Domain\Exception\TransportException;
use ANZ\BitUmc\SDK\Transport\ConnectionOptions;
use ANZ\BitUmc\SDK\Transport\Endpoint\EndpointResolver;
use SoapClient;
use Throwable;

final class SoapTransport
{
    private SoapClient $client;

    public function __construct(
        private readonly ConnectionOptions $options,
        private readonly EndpointResolver $endpointResolver = new EndpointResolver(),
    ) {
        if (!class_exists(SoapClient::class)) {
            throw new TransportException('SOAP extension not found.');
        }

        if (($this->options->auth->getLogin() ?? '') === '' || ($this->options->auth->getPassword() ?? '') === '') {
            throw new InvalidArgumentException('SOAP transport requires runtime login and password in ConnectionOptions auth.');
        }

        try {
            set_error_handler(static fn (): bool => true);
            $this->client = new SoapClient(
                $this->endpointResolver->resolveWsdl($this->options),
                [
                    'login' => $this->options->auth->getLogin(),
                    'password' => $this->options->auth->getPassword(),
                    'stream_context' => stream_context_create([
                        'ssl' => [
                            'verify_peer' => $this->options->verifyPeer,
                            'verify_peer_name' => $this->options->verifyPeer,
                        ],
                    ]),
                    'soap_version' => SOAP_1_2,
                    'location' => $this->endpointResolver->resolveSoapLocation($this->options),
                    'cache_wsdl' => WSDL_CACHE_NONE,
                    'exceptions' => true,
                    'trace' => 0,
                    'connection_timeout' => $this->options->timeoutSeconds,
                    'keep_alive' => false,
                ]
            );
        } catch (Throwable $exception) {
            throw new TransportException($exception->getMessage(), (int) $exception->getCode(), $exception);
        } finally {
            restore_error_handler();
        }
    }

    public function execute(SoapOperation $operation): mixed
    {
        try {
            return $this->client->__soapCall($operation->method->value, [$operation->payload]);
        } catch (Throwable $exception) {
            throw new TransportException($exception->getMessage(), (int) $exception->getCode(), $exception);
        }
    }
}
