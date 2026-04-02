<?php

declare(strict_types=1);

namespace ANZ\BitUmc\SDK\Tests\Unit\Infrastructure\Soap\Parser;

use ANZ\BitUmc\SDK\Domain\Exception\RemoteServiceException;
use ANZ\BitUmc\SDK\Infrastructure\Soap\Parser\CommonResultXmlParser;
use ANZ\BitUmc\SDK\Tests\FixtureHelper;
use PHPUnit\Framework\TestCase;

final class CommonResultXmlParserTest extends TestCase
{
    public function testParsesSuccessWithUid(): void
    {
        $xml = FixtureHelper::read('tests/Fixtures/soap/common/success-with-uid.xml');

        $result = (new CommonResultXmlParser())->parse($xml);
        self::assertArrayHasKey('uid', $result);
    }

    public function testThrowsOnFailure(): void
    {
        $xml = FixtureHelper::read('tests/Fixtures/soap/common/error-description.xml');

        $this->expectException(RemoteServiceException::class);
        (new CommonResultXmlParser())->parse($xml);
    }
}
