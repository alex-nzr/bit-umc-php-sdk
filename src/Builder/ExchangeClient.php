<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - ExchangeClient.php
 * 06.08.2022 21:52
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Builder;

use ANZ\BitUmc\SDK\Client\HttpClient;
use ANZ\BitUmc\SDK\Client\SoapClient;
use ANZ\BitUmc\SDK\Core\Contract\Connection\IClient;
use ANZ\BitUmc\SDK\Core\Contract\IBuilder;
use ANZ\BitUmc\SDK\Core\Dictionary\ClientScope;
use ANZ\BitUmc\SDK\Core\Dictionary\Protocol;
use Exception;

/**
 * @class ExchangeClient
 * @package ANZ\BitUmc\SDK\Builder
 */
class ExchangeClient implements IBuilder
{
    protected ?string $login = null;
    protected ?string $password = null;
    protected ?Protocol $publicationProtocol = null;
    protected ?string $publicationAddress = null;
    protected ?string $baseName = null;
    protected ?ClientScope $scope = null;

    /**
     * @return static
     */
    public static function init(): static
    {
        return new static();
    }

    /**
     * @param string $login
     * @return $this
     */
    public function setLogin(string $login): ExchangeClient
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): ExchangeClient
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @param \ANZ\BitUmc\SDK\Core\Dictionary\Protocol $protocol
     * @return $this
     */
    public function setPublicationProtocol(Protocol $protocol): ExchangeClient
    {
        $this->publicationProtocol = $protocol;
        return $this;
    }

    /**
     * @param string $publicationAddress
     * @return $this
     */
    public function setPublicationAddress(string $publicationAddress): ExchangeClient
    {
        $this->publicationAddress = $publicationAddress;
        return $this;
    }

    /**
     * @param string $baseName
     * @return $this
     */
    public function setBaseName(string $baseName): ExchangeClient
    {
        $this->baseName = $baseName;
        return $this;
    }

    /**
     * @param \ANZ\BitUmc\SDK\Core\Dictionary\ClientScope $scope
     * @return $this
     */
    public function setScope(ClientScope $scope): ExchangeClient
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * @return \ANZ\BitUmc\SDK\Core\Contract\Connection\IClient
     * @throws \Exception
     */
    public function build(): IClient
    {
        $this->checkFields();

        /** @var null | IClient $clientClass */
        $clientClass = match ($this->scope){
            ClientScope::HTTP_SERVICE => HttpClient::class,
            ClientScope::WEB_SERVICE => SoapClient::class,
            default => null
        };

        if (is_null($clientClass))
        {
            throw new Exception('Can not determine class of client by scope ' . $this->scope->value);
        }

        return $clientClass::create(
            $this->login,
            $this->password,
            $this->publicationProtocol,
            $this->publicationAddress,
            $this->baseName,
            $this->scope
        );
    }

    /**
     * @throws \Exception
     */
    protected function checkFields(): void
    {
        $errorMessage = '';

        if (empty($this->login)){
            $errorMessage = $this->getErrorMessageByProperty('login');
        }
        if (empty($this->password)){
            $errorMessage = $this->getErrorMessageByProperty('password');
        }
        if (empty($this->publicationProtocol)){
            $errorMessage = $this->getErrorMessageByProperty('publicationProtocol');
        }
        if (empty($this->publicationAddress))
        {
            $errorMessage = $this->getErrorMessageByProperty('publicationAddress');
        }
        if (empty($this->baseName))
        {
            $errorMessage = $this->getErrorMessageByProperty('baseName');
        }
        if (empty($this->scope))
        {
            $errorMessage = $this->getErrorMessageByProperty('scope');
        }

        if (!empty($errorMessage))
        {
            throw new Exception($errorMessage);
        }
    }

    /**
     * @param string $propName
     * @return string
     */
    protected function getErrorMessageByProperty(string $propName): string
    {
        return sprintf(
            'Can not init client without %s. Use set%s() method of ' . static::class,
            $propName, ucfirst($propName)
        );
    }
}