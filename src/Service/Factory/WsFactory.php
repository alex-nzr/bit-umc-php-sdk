<?php
/** @noinspection PhpPureAttributeCanBeAddedInspection */

/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - ServiceFactory.php
 * 04.08.2022 01:28
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Service;

use ANZ\BitUmc\SDK\Core\Contract\ApiClient;
use ANZ\BitUmc\SDK\Core\Contract\FactoryInterface;
use ANZ\BitUmc\SDK\Core\Contract\ServiceInterface;
use ANZ\BitUmc\SDK\Service\OneC\Reader;
use ANZ\BitUmc\SDK\Service\OneC\Writer;

/**
 * Class ServiceFactory
 * @package ANZ\BitUmc\SDK\Service
 */
class ServiceFactory implements FactoryInterface
{
    private ApiClient $client;

    /**
     * ServiceFactory constructor.
     * @param \ANZ\BitUmc\SDK\Core\Contract\ApiClient $client
     */
    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return \ANZ\BitUmc\SDK\Service\OneC\Reader
     */
    public function getReader(): Reader
    {
        return (new Reader($this->client));
    }

    /**
     * @return \ANZ\BitUmc\SDK\Service\OneC\Writer
     */
    public function getWriter(): Writer
    {
        return (new Writer($this->client));
    }
}