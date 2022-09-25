<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - WsClient.php
 * 04.08.2022 02:12
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Api;

use ANZ\BitUmc\SDK\Core\Contract\ApiClient;
use ANZ\BitUmc\SDK\Core\Operation\Result;
use ANZ\BitUmc\SDK\Core\Soap\SoapClient;

/**
 * Class WsClient
 * @package ANZ\BitUmc\SDK\Api
 */
class WsClient implements ApiClient
{
    private string     $login;
    private string     $password;
    private bool       $https;
    private string     $address;
    private string     $baseName;
    private SoapClient $soapClient;

    /**
     * WsClient constructor.
     * @param $login
     * @param $password
     * @param $https
     * @param $address
     * @param $baseName
     * @throws \Exception
     */
    public function __construct($login, $password, $https, $address, $baseName)
    {
        $this->login      = $login;
        $this->password   = $password;
        $this->https      = $https;
        $this->address    = $address;
        $this->baseName   = $baseName;
        $this->soapClient = new SoapClient(
            $this->getFullBaseUrl(),
            $this->getSoapOptions()
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getFullBaseUrl(): string
    {
        $protocol = $this->https ? 'https' : 'http';
        return sprintf('%s://%s/%s/ws/ws1.1cws?wsdl', $protocol, $this->address, $this->baseName);
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        $protocol = $this->https ? 'https' : 'http';
        return sprintf('%s://%s/%s/ws/ws1.1cws', $protocol, $this->address, $this->baseName);
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getSoapOptions(): array
    {
        return [
            'login'          => $this->login,
            'password'       => $this->password,
            'stream_context' => stream_context_create(
                [
                    'ssl' => [
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                    ]
                ]
            ),
            'soap_version'       => SOAP_1_2,
            'location'           => $this->getLocation(),
            'cache_wsdl'         => WSDL_CACHE_NONE,
            'exceptions'         => true,
            'trace'              => 1,
            'connection_timeout' => 5000,
            'keep_alive'         => false,
        ];
    }

    /**
     * @param string $method
     * @param array $params
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function send(string $method, array $params = []): Result
    {
        return $this->soapClient->send($method, $params);
    }

    /**
     * @return bool
     */
    public function isHsScope(): bool
    {
        return false;
    }
}