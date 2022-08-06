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

namespace ANZ\BitUmc\SDK\Service\Factory;

use ANZ\BitUmc\SDK\Config\Constants;
use ANZ\BitUmc\SDK\Core\Contract\ApiClient;
use ANZ\BitUmc\SDK\Core\Contract\FactoryInterface;
use ANZ\BitUmc\SDK\Service\OneC\HsReader;
use ANZ\BitUmc\SDK\Service\OneC\HsWriter;
use ANZ\BitUmc\SDK\Service\OneC\WsReader;
use ANZ\BitUmc\SDK\Service\OneC\WsWriter;

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
     * @return \ANZ\BitUmc\SDK\Service\OneC\WsReader|\ANZ\BitUmc\SDK\Service\OneC\HsReader
     */
    public function getReader(): WsReader | HsReader
    {
        $serviceClass = match ($this->client->getScope()){
            Constants::WS_SCOPE => WsReader::class,
            Constants::HS_SCOPE => HsReader::class,
        };
        return (new $serviceClass($this->client));
    }

    /**
     * @return \ANZ\BitUmc\SDK\Service\OneC\WsWriter|\ANZ\BitUmc\SDK\Service\OneC\HsWriter
     */
    public function getWriter(): WsWriter | HsWriter
    {
        $serviceClass = match ($this->client->getScope()){
            Constants::WS_SCOPE => WsWriter::class,
            Constants::HS_SCOPE => HsWriter::class,
        };
        return (new $serviceClass($this->client));
    }
}