<?php
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

use ANZ\BitUmc\SDK\Core\Contract\IFactory;
use ANZ\BitUmc\SDK\Core\Contract\Connection\IClient;
use ANZ\BitUmc\SDK\Core\Enumeration\ClientScope;
use ANZ\BitUmc\SDK\Service\OneC\HsReader;
use ANZ\BitUmc\SDK\Service\OneC\HsWriter;
use ANZ\BitUmc\SDK\Service\OneC\WsReader;
use ANZ\BitUmc\SDK\Service\OneC\WsWriter;

/**
 * Class ServiceFactory
 * @package ANZ\BitUmc\SDK\Service
 */
class ServiceFactory implements IFactory
{
    private IClient $client;

    /**
     * @param \ANZ\BitUmc\SDK\Core\Contract\Connection\IClient $client
     * @return static
     */
    public static function initByClient(IClient $client): static
    {
        $factory = new static();
        $factory->client = $client;
        return $factory;
    }

    /**
     * @return \ANZ\BitUmc\SDK\Service\OneC\HsReader|\ANZ\BitUmc\SDK\Service\OneC\WsReader
     */
    public function getReader(): HsReader | WsReader
    {
        $serviceClass = match ($this->client->getScope()) {
            ClientScope::HTTP_SERVICE => HsReader::class,
            ClientScope::WEB_SERVICE => WsReader::class,
        };

        return (new $serviceClass($this->client));
    }

    /**
     * @return \ANZ\BitUmc\SDK\Service\OneC\WsWriter|\ANZ\BitUmc\SDK\Service\OneC\HsWriter
     */
    public function getWriter(): WsWriter|HsWriter
    {
        $serviceClass = match ($this->client->getScope()) {
            ClientScope::HTTP_SERVICE => HsWriter::class,
            ClientScope::WEB_SERVICE => WsWriter::class,
        };

        return (new $serviceClass($this->client));
    }

    protected function __construct(){}
}