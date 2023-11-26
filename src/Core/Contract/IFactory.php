<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - IFactory.php
 * 26.11.2023 18:42
 * ==================================================
 */
namespace ANZ\BitUmc\SDK\Core\Contract;

use ANZ\BitUmc\SDK\Core\Contract\Connection\IClient;
use ANZ\BitUmc\SDK\Core\Contract\Service\IService;

/**
 * @interface IFactory
 * @package ANZ\BitUmc\SDK\Core\Contract
 */
interface IFactory
{
    /**
     * @param \ANZ\BitUmc\SDK\Core\Contract\Connection\IClient $client
     * @return static
     */
    public static function initByClient(IClient $client): static;

    /**
     * @return \ANZ\BitUmc\SDK\Core\Contract\Service\IService
     */
    public function getReader(): IService;

    /**
     * @return \ANZ\BitUmc\SDK\Core\Contract\Service\IService
     */
    public function getWriter(): IService;
}