<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - IClient.php
 * 25.11.2023 00:40
 * ==================================================
 */
namespace ANZ\BitUmc\SDK\Core\Contract\Connection;

use ANZ\BitUmc\SDK\Core\Enumeration\ClientScope;
use ANZ\BitUmc\SDK\Core\Enumeration\Protocol;
use ANZ\BitUmc\SDK\Core\Operation\Result;

/**
 * @interface IClient
 * @package ANZ\BitUmc\SDK\Core\Contract\OneC
 */
interface IClient
{
    /**
     * @param string $login
     * @param string $password
     * @param \ANZ\BitUmc\SDK\Core\Enumeration\Protocol $protocol
     * @param string $address
     * @param string $baseName
     * @param \ANZ\BitUmc\SDK\Core\Enumeration\ClientScope $scope
     * @return static
     */
    public static function create(
        string $login, string $password, Protocol $protocol, string $address, string $baseName, ClientScope $scope
    ): static;

    /**
     * @param \ANZ\BitUmc\SDK\Core\Enumeration\ClientScope $scope
     * @return void
     */
    public function setScope(ClientScope $scope): void;

    /**
     * @return \ANZ\BitUmc\SDK\Core\Enumeration\ClientScope
     */
    public function getScope(): ClientScope;

    /**
     * @param string $method
     * @param array $params
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function send(string $method, array $params): Result;
}