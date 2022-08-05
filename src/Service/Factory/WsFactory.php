<?php
/** @noinspection PhpPureAttributeCanBeAddedInspection */

/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - WsFactory.php
 * 04.08.2022 01:28
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Service\Factory;

use ANZ\BitUmc\SDK\Core\Contract\ApiClient;
use ANZ\BitUmc\SDK\Core\Contract\FactoryInterface;
use ANZ\BitUmc\SDK\Service\WebService\WsReader;
use ANZ\BitUmc\SDK\Service\WebService\WsWriter;

/**
 * Class WsFactory
 * @package ANZ\BitUmc\SDK\Service
 */
class WsFactory implements FactoryInterface
{
    private ApiClient $client;

    /**
     * WsFactory constructor.
     * @param \ANZ\BitUmc\SDK\Core\Contract\ApiClient $client
     */
    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return \ANZ\BitUmc\SDK\Service\WebService\WsReader
     */
    public function getReader(): WsReader
    {
        return (new WsReader($this->client));
    }

    /**
     * @return \ANZ\BitUmc\SDK\Service\WebService\WsWriter
     */
    public function getWriter(): WsWriter
    {
        return (new WsWriter($this->client));
    }
}