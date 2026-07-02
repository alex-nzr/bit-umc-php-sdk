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

    public function testPreservesCustomFields(): void
    {
        $xml = <<<'XML'
<?xml version="1.0"?>
<Каталоги>
    <Каталог>
        <UID>item-uid</UID>
        <Наименование>Консультация</Наименование>
        <Артикул>ART-1</Артикул>
        <БазоваяЕдиницаИзмерения>шт</БазоваяЕдиницаИзмерения>
        <Цена>1500</Цена>
        <Продолжительность>0001-01-01T00:30:00</Продолжительность>
        <Вид>Услуга</Вид>
        <Родитель>parent-uid</Родитель>
        <ЭтоПапка>false</ЭтоПапка>
        <СкидкаПроцент>15</СкидкаПроцент>
    </Каталог>
</Каталоги>
XML;

        $result = (new NomenclatureXmlParser())->parse($xml);

        self::assertSame('Консультация', $result['item-uid']['name']);
        self::assertSame('15', $result['item-uid']['_extra']['СкидкаПроцент']);
        self::assertArrayNotHasKey('Наименование', $result['item-uid']['_extra']);
    }

    public function testThrowsOnErrorDescription(): void
    {
        $xml = FixtureHelper::read('tests/Fixtures/soap/nomenclature/error-description.xml');

        $this->expectException(RemoteServiceException::class);
        (new NomenclatureXmlParser())->parse($xml);
    }
}
