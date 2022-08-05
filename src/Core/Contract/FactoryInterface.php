<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - FactoryInterface.php
 * 06.08.2022 00:56
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Core\Contract;

/**
 * Interface FactoryInterface
 * @package ANZ\BitUmc\SDK\Core\Contract
 */
interface FactoryInterface
{
    public function __construct(ApiClient $client);

    /**
     * @return \ANZ\BitUmc\SDK\Core\Contract\ServiceInterface
     */
    public function getReader(): ServiceInterface;

    /**
     * @return \ANZ\BitUmc\SDK\Core\Contract\ServiceInterface
     */
    public function getWriter(): ServiceInterface;
}