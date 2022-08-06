<?php /** @noinspection PhpPureAttributeCanBeAddedInspection */

/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - ClientBuilder.php
 * 06.08.2022 21:52
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Service\Builder;

use ANZ\BitUmc\SDK\Config\Constants;
use ANZ\BitUmc\SDK\Core\Contract\ApiClient;
use ANZ\BitUmc\SDK\Core\Contract\BuilderInterface;
use ANZ\BitUmc\SDK\Service\Api\HsClient;
use ANZ\BitUmc\SDK\Service\Api\WsClient;
use Exception;

/**
 * Class ClientBuilder
 * @package ANZ\BitUmc\SDK\Service\Builder
 */
class ClientBuilder implements BuilderInterface
{
    private string $login;
    private string $password;
    private bool   $https;
    private string $address;
    private string $baseName;
    private string $scope;

    public function __construct()
    {
        $this->scope = Constants::WS_SCOPE;
    }

    /**
     * @return \ANZ\BitUmc\SDK\Service\Builder\ClientBuilder
     */
    public static function init(): ClientBuilder
    {
        return new static();
    }

    /**
     * @param string $login
     * @return $this
     */
    public function setLogin(string $login): ClientBuilder
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): ClientBuilder
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setHttps(bool $enabled): ClientBuilder
    {
        $this->https = $enabled;
        return $this;
    }

    /**
     * @param string $address
     * @return $this
     */
    public function setAddress(string $address): ClientBuilder
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @param string $baseName
     * @return $this
     */
    public function setBaseName(string $baseName): ClientBuilder
    {
        $this->baseName = $baseName;
        return $this;
    }

    public function setWsScope(): ClientBuilder
    {
        $this->scope = Constants::WS_SCOPE;
        return $this;
    }

    public function setHsScope(): ClientBuilder
    {
        $this->scope = Constants::HS_SCOPE;
        return $this;
    }

    /**
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClient
     * @throws \Exception
     */
    public function build(): ApiClient
    {
        if (empty($this->scope))
        {
            throw new Exception('Can not create client without selected scope');
        }

        $clientClass = match ($this->scope) {
            Constants::WS_SCOPE => WsClient::class,
            Constants::HS_SCOPE => HsClient::class,
        };

        return new $clientClass( $this->login, $this->password, $this->https, $this->address, $this->baseName );
    }
}