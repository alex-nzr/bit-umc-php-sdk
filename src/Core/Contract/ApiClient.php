<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - ApiClient.php
 * 04.08.2022 02:06
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Core\Contract;

/**
 * Interface ApiClientInterface
 * @package ANZ\BitUmc\SDK\Core\Contract
 */
interface ApiClient
{
    /**
     * @param string $login
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClient
     */
    public function setLogin(string $login): ApiClient;

    /**
     * @param string $password
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClient
     */
    public function setPassword(string $password): ApiClient;

    /**
     * @param bool $enabled
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClient
     */
    public function setHttps(bool $enabled): ApiClient;

    /**
     * @param string $address
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClient
     */
    public function setAddress(string $address): ApiClient;

    /**
     * @param string $baseName
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClient
     */
    public function setBaseName(string $baseName): ApiClient;

    public function send(string $method, array $params);
}