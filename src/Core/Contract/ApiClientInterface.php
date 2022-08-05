<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - ApiClientInterface.php
 * 04.08.2022 02:06
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Core\Contract;

/**
 * Interface ApiClientInterface
 * @package ANZ\BitUmc\SDK\Core\Contract
 */
interface ApiClientInterface
{
    /**
     * @param string $login
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClientInterface
     */
    public function setLogin(string $login): ApiClientInterface;

    /**
     * @param string $password
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClientInterface
     */
    public function setPassword(string $password): ApiClientInterface;

    /**
     * @param bool $enabled
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClientInterface
     */
    public function setHttps(bool $enabled): ApiClientInterface;

    /**
     * @param string $address
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClientInterface
     */
    public function setAddress(string $address): ApiClientInterface;

    /**
     * @param string $baseName
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClientInterface
     */
    public function setBaseName(string $baseName): ApiClientInterface;
}