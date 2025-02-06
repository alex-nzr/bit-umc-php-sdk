<?php
/*
 * ==================================================
 * This file is part of project bit-umc-php-sdk
 * 25.11.2023
 * ==================================================
*/
namespace ANZ\BitUmc\SDK\Core\Contract\Connection;

use ANZ\BitUmc\SDK\Core\Contract\Model\IRequestModel;
use ANZ\BitUmc\SDK\Core\Dictionary\ClientScope;
use ANZ\BitUmc\SDK\Core\Dictionary\Protocol;
use ANZ\BitUmc\SDK\Core\Operation\Result;

interface IClient
{
    /**
     * @param string $login
     * @param string $password
     * @param \ANZ\BitUmc\SDK\Core\Dictionary\Protocol $protocol
     * @param string $address
     * @param string $baseName
     * @param \ANZ\BitUmc\SDK\Core\Dictionary\ClientScope $scope
     * @return static
     */
    public static function create(
        string $login, string $password, Protocol $protocol, string $address, string $baseName, ClientScope $scope
    ): static;

    /**
     * @param \ANZ\BitUmc\SDK\Core\Dictionary\ClientScope $scope
     * @return void
     */
    public function setScope(ClientScope $scope): void;

    /**
     * @return \ANZ\BitUmc\SDK\Core\Dictionary\ClientScope
     */
    public function getScope(): ClientScope;

    /**
     * @param \ANZ\BitUmc\SDK\Core\Contract\Model\IRequestModel $requestModel
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function send(IRequestModel $requestModel): Result;
}