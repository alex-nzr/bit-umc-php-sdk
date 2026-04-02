<?php

declare(strict_types=1);

namespace ANZ\BitUmc\SDK\Tests\Unit\Transport;

use ANZ\BitUmc\SDK\Transport\Auth\BasicAuth;
use ANZ\BitUmc\SDK\Transport\ConnectionOptions;
use ANZ\BitUmc\SDK\Transport\Endpoint\EndpointResolver;
use ANZ\BitUmc\SDK\Transport\Protocol;
use PHPUnit\Framework\TestCase;

final class EndpointResolverTest extends TestCase
{
    public function testResolvesSoapIntegrationEndpoints(): void
    {
        $options = new ConnectionOptions(
            protocol: Protocol::HTTP,
            host: '127.0.0.1:8080',
            baseName: 'umc',
            auth: new BasicAuth('user', 'pass'),
        );

        $resolver = new EndpointResolver();

        self::assertSame('http://127.0.0.1:8080/umc/ws/Integration?wsdl', $resolver->resolveWsdl($options));
        self::assertSame('http://127.0.0.1:8080/umc/ws/Integration', $resolver->resolveSoapLocation($options));
    }
}
