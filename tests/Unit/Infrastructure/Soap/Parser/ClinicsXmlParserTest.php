<?php

declare(strict_types=1);

namespace ANZ\BitUmc\SDK\Tests\Unit\Infrastructure\Soap\Parser;

use ANZ\BitUmc\SDK\Infrastructure\Soap\Parser\ClinicsXmlParser;
use ANZ\BitUmc\SDK\Tests\FixtureHelper;
use PHPUnit\Framework\TestCase;

final class ClinicsXmlParserTest extends TestCase
{
    public function testParsesClinics(): void
    {
        $xml = FixtureHelper::read('tests/Fixtures/soap/clinics/success.xml');

        $result = (new ClinicsXmlParser())->parse($xml);

        self::assertCount(3, $result);
        self::assertSame('Центральная клиника', $result['f679444a-22b7-11df-8618-002618dcef2c']['name']);
    }

    public function testPreservesCustomNestedFields(): void
    {
        $xml = <<<'XML'
<?xml version="1.0"?>
<СписокКлиник>
    <Клиника>
        <Наименование>Центральная клиника</Наименование>
        <УИД>clinic-uid</УИД>
        <Подразделения>
            <Подразделение>
                <ГУИД>department-1</ГУИД>
                <Наименование>Терапия</Наименование>
            </Подразделение>
            <Подразделение>
                <ГУИД>department-2</ГУИД>
                <Наименование>Диагностика</Наименование>
            </Подразделение>
        </Подразделения>
    </Клиника>
</СписокКлиник>
XML;

        $result = (new ClinicsXmlParser())->parse($xml);

        self::assertSame('Центральная клиника', $result['clinic-uid']['name']);
        self::assertArrayNotHasKey('УИД', $result['clinic-uid']['_extra']);
        self::assertSame('department-1', $result['clinic-uid']['_extra']['Подразделения']['Подразделение'][0]['ГУИД']);
        self::assertSame('Диагностика', $result['clinic-uid']['_extra']['Подразделения']['Подразделение'][1]['Наименование']);
    }
}
