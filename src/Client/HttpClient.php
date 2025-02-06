<?php
/*
 * ==================================================
 * This file is part of project bit-umc-php-sdk
 * 26.11.2023
 * ==================================================
*/
namespace ANZ\BitUmc\SDK\Client;

use ANZ\BitUmc\SDK\Core\Contract\Connection\IClient;
use ANZ\BitUmc\SDK\Core\Contract\Model\IRequestModel;
use ANZ\BitUmc\SDK\Core\Dictionary\ClientScope;
use ANZ\BitUmc\SDK\Core\Dictionary\Protocol;
use ANZ\BitUmc\SDK\Core\Operation\Result;

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