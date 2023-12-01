<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - HttpClient.php
 * 26.11.2023 20:29
 * ==================================================
 */
namespace ANZ\BitUmc\SDK\Client;

use ANZ\BitUmc\SDK\Core\Contract\Connection\IClient;
use ANZ\BitUmc\SDK\Core\Contract\Model\IRequestModel;
use ANZ\BitUmc\SDK\Core\Dictionary\ClientScope;
use ANZ\BitUmc\SDK\Core\Dictionary\Protocol;
use ANZ\BitUmc\SDK\Core\Operation\Result;

/**
 * Class is not finished, because this SDK does not work with http-services yet
 * @class HttpClient
 * @package ANZ\BitUmc\SDK\Client
 */
class HttpClient implements IClient
{
    protected ClientScope $scope;

    /**
     * @param string $login
     * @param string $password
     * @param \ANZ\BitUmc\SDK\Core\Dictionary\Protocol $protocol
     * @param string $address
     * @param string $baseName
     * @param \ANZ\BitUmc\SDK\Core\Dictionary\ClientScope $scope
     * @return static
     */
    public static function create(string $login, string $password, Protocol $protocol, string $address, string $baseName, ClientScope $scope): static
    {
        return new static;
    }

    /**
     * @param \ANZ\BitUmc\SDK\Core\Dictionary\ClientScope $scope
     * @return void
     */
    public function setScope(ClientScope $scope): void
    {
        $this->scope = $scope;
    }

    /**
     * @return \ANZ\BitUmc\SDK\Core\Dictionary\ClientScope
     */
    public function getScope(): ClientScope
    {
        return $this->scope;
    }

    /**
     * @param \ANZ\BitUmc\SDK\Core\Contract\Model\IRequestModel $requestModel
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function send(IRequestModel $requestModel): Result
    {
        return new Result();
    }
}