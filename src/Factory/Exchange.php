<?php
/*
 * ==================================================
 * This file is part of project bit-umc-php-sdk
 * 04.08.2022
 * ==================================================
*/
namespace ANZ\BitUmc\SDK\Factory;

use ANZ\BitUmc\SDK\Core\Contract\Connection\IClient;
use ANZ\BitUmc\SDK\Core\Contract\IFactory;
use ANZ\BitUmc\SDK\Core\Dictionary\ClientScope;
use ANZ\BitUmc\SDK\Service\Exchange\Http;
use ANZ\BitUmc\SDK\Service\Exchange\Soap;

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