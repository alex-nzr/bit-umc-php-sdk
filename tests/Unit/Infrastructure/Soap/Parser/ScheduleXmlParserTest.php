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

    public function testPreservesScheduleAndSlotCustomFields(): void
    {
        $xml = <<<'XML'
<?xml version="1.0"?>
<ГрафикиДляСайта>
    <ГрафикДляСайта>
        <Клиника>clinic-uid</Клиника>
        <СотрудникФИО>Петров Петр Петрович</СотрудникФИО>
        <СотрудникID>employee-uid</СотрудникID>
        <Специализация>Терапия</Специализация>
        <ДлительностьПриема>0001-01-01T00:30:00</ДлительностьПриема>
        <Подразделение>department-uid</Подразделение>
        <ПериодыГрафика>
            <СвободноеВремя>
                <ПериодГрафика>
                    <Клиника>clinic-uid</Клиника>
                    <Дата>2026-04-05T00:00:00</Дата>
                    <ВремяНачала>2026-04-05T09:00:00</ВремяНачала>
                    <ВремяОкончания>2026-04-05T09:30:00</ВремяОкончания>
                    <ВидВремени>time-type-uid</ВидВремени>
                    <КастомноеПолеТаймслота>slot-value</КастомноеПолеТаймслота>
                </ПериодГрафика>
            </СвободноеВремя>
        </ПериодыГрафика>
    </ГрафикДляСайта>
</ГрафикиДляСайта>
XML;

        $result = (new ScheduleXmlParser())->parse($xml);
        $employee = $result['clinic-uid'][array_key_first($result['clinic-uid'])]['employee-uid'];
        $slot = $employee['timetable']['free']['05-04-2026'][0];

        self::assertSame('department-uid', $employee['_extra']['Подразделение']);
        self::assertArrayNotHasKey('ПериодыГрафика', $employee['_extra']);
        self::assertSame('slot-value', $slot['_extra']['КастомноеПолеТаймслота']);
        self::assertArrayNotHasKey('Дата', $slot['_extra']);
    }
}
