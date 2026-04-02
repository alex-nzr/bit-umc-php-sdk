<?php

declare(strict_types=1);

namespace ANZ\BitUmc\SDK\Tests\Unit\Infrastructure\Soap\Parser;

use ANZ\BitUmc\SDK\Domain\Exception\RemoteServiceException;
use ANZ\BitUmc\SDK\Infrastructure\Soap\Parser\NomenclatureXmlParser;
use ANZ\BitUmc\SDK\Tests\FixtureHelper;
use PHPUnit\Framework\TestCase;

final class NomenclatureXmlParserTest extends TestCase
{
    public function testSkipsFoldersAndParsesProducts(): void
    {
        $xml = FixtureHelper::read('tests/Fixtures/soap/nomenclature/success.xml');

        $result = (new NomenclatureXmlParser())->parse($xml);

        self::assertNotEmpty($result);
        $item = current($result);
        self::assertIsArray($item);
        self::assertArrayHasKey('duration', $item);
        self::assertArrayHasKey('price', $item);
    }

    public function testThrowsOnErrorDescription(): void
    {
        $xml = FixtureHelper::read('tests/Fixtures/soap/nomenclature/error-description.xml');

        $this->expectException(RemoteServiceException::class);
        (new NomenclatureXmlParser())->parse($xml);
    }
}
