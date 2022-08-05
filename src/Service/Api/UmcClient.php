<?php /** @noinspection PhpSwitchCanBeReplacedWithMatchExpressionInspection */
/** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */
/** @noinspection PhpPureAttributeCanBeAddedInspection */

/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - UmcClient.php
 * 04.08.2022 02:12
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Service\Api;

use ANZ\BitUmc\SDK\Core\Contract\ApiClient;
use ANZ\BitUmc\SDK\Core\Contract\Result;
use ANZ\BitUmc\SDK\Core\Soap\SoapClient;
use ANZ\BitUmc\SDK\Core\Soap\SoapMethod;
use Exception;

/**
 * Class UmcClient
 * @package ANZ\BitUmc\SDK\Service\Api
 */
class UmcClient implements ApiClient
{
    private string $login;
    private string $password;
    private bool   $https;
    private string $address;
    private string $baseName;
    private SoapClient $soapClient;

    public static function create(): ApiClient
    {
        return new static();
    }

    /**
     * @param string $login
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClient
     */
    public function setLogin(string $login): ApiClient
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @param string $password
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClient
     */
    public function setPassword(string $password): ApiClient
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @param bool $enabled
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClient
     */
    public function setHttps(bool $enabled): ApiClient
    {
        $this->https = $enabled;
        return $this;
    }

    /**
     * @param string $address
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClient
     */
    public function setAddress(string $address): ApiClient
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @param string $baseName
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClient
     */
    public function setBaseName(string $baseName): ApiClient
    {
        $this->baseName = $baseName;
        return $this;
    }

    /**
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClient
     * @throws \Exception
     */
    public function init(): ApiClient
    {
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
     * @return array
     * @throws \Exception
     */
    private function getSoapOptions(): array
    {
        if (empty($this->login) || empty($this->password)){
            throw new Exception('Can not init client without login or password');
        }

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
            'soap_version' => SOAP_1_2,
            'exceptions' => true,
            'trace' => 1,
            'connection_timeout' => 5000,
            'keep_alive' => false,
        ];
    }

    /**
     * @param string $method
     * @param array $params
     * @return \ANZ\BitUmc\SDK\Core\Contract\Result
     */
    public function send(string $method, array $params = []): Result
    {
        return $this->soapClient->send($method, $params);
    }
}