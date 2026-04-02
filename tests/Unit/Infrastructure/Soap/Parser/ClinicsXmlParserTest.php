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
}
