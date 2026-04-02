<?php

declare(strict_types=1);

namespace ANZ\BitUmc\SDK\Tests\Unit\Infrastructure\Soap\Parser;

use ANZ\BitUmc\SDK\Infrastructure\Soap\Parser\ScheduleXmlParser;
use ANZ\BitUmc\SDK\Tests\FixtureHelper;
use PHPUnit\Framework\TestCase;

final class ScheduleXmlParserTest extends TestCase
{
    public function testParsesSchedule(): void
    {
        $xml = FixtureHelper::read('tests/Fixtures/soap/schedule/success.xml');

        $result = (new ScheduleXmlParser())->parse($xml);
        $clinic = current($result);
        $specialty = current($clinic);
        $employee = current($specialty);

        self::assertSame('Барбышева Евгения Петровна', $employee['employeeName']);
        self::assertSame(900, $employee['durationInSeconds']);
        self::assertNotEmpty($employee['timetable']['freeFormatted']);
        self::assertNotEmpty($employee['timetable']['busy']);
    }
}
