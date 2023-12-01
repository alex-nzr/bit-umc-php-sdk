<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - Exchange.php
 * 04.08.2022 01:28
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Factory;

use ANZ\BitUmc\SDK\Core\Contract\Connection\IClient;
use ANZ\BitUmc\SDK\Core\Contract\IFactory;
use ANZ\BitUmc\SDK\Core\Dictionary\ClientScope;
use ANZ\BitUmc\SDK\Service\Exchange\Http;
use ANZ\BitUmc\SDK\Service\Exchange\Soap;

/**
 * @class ServiceFactory
 * @package ANZ\BitUmc\SDK\Factory
 */
class Exchange implements IFactory
{
    protected IClient $client;

    /**
     * Exchange constructor
     * @param \ANZ\BitUmc\SDK\Core\Contract\Connection\IClient $client
     */
    public function __construct(IClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return \ANZ\BitUmc\SDK\Service\Exchange\Http|\ANZ\BitUmc\SDK\Service\Exchange\Soap
     */
    public function create(): Http | Soap
    {
        $serviceClass = match ($this->client->getScope()) {
            ClientScope::HTTP_SERVICE => Http::class,
            ClientScope::WEB_SERVICE => Soap::class,
        };

        return (new $serviceClass($this->client));
    }
}