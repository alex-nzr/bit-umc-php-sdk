<?php

declare(strict_types=1);

namespace ANZ\BitUmc\SDK\Tests\Unit\Infrastructure\Soap\Parser;

use ANZ\BitUmc\SDK\Infrastructure\Soap\Parser\AppointmentStatusXmlParser;
use ANZ\BitUmc\SDK\Tests\FixtureHelper;
use PHPUnit\Framework\TestCase;

final class AppointmentStatusXmlParserTest extends TestCase
{
    public function testParsesAppointmentStatus(): void
    {
        $xml = FixtureHelper::read('tests/Fixtures/soap/status/success.xml');

        $result = (new AppointmentStatusXmlParser())->parse($xml);

        self::assertSame('6', $result['statusId']);
        self::assertSame('Резерв времени', $result['statusTitle']);
    }
}
