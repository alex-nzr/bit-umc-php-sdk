<?php

declare(strict_types=1);

namespace ANZ\BitUmc\SDK\Tests\Unit\Infrastructure\Soap\Parser;

use ANZ\BitUmc\SDK\Domain\Exception\RemoteServiceException;
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
        $xml = self::customScheduleXml();

        $result = (new ScheduleXmlParser())->parse($xml);
        $employee = $result['clinic-uid'][array_key_first($result['clinic-uid'])]['employee-uid'];
        $slot = $employee['timetable']['free']['05-04-2026'][0];

        self::assertSame('department-uid', $employee['_extra']['Подразделение']);
        self::assertArrayNotHasKey('ПериодыГрафика', $employee['_extra']);
        self::assertSame('slot-value', $slot['_extra']['КастомноеПолеТаймслота']);
        self::assertArrayNotHasKey('Дата', $slot['_extra']);
    }

    public function testIteratesScheduleRecordsSequentially(): void
    {
        $records = (new ScheduleXmlParser())->iterate(self::customScheduleXml());

        $employeeRecord = $records->current();
        self::assertSame('employee', $employeeRecord['kind']);
        self::assertSame('employee-uid', $employeeRecord['employeeUid']);
        self::assertSame(1800, $employeeRecord['employee']['durationInSeconds']);

        $records->next();
        $freePeriodRecord = $records->current();
        self::assertSame('period', $freePeriodRecord['kind']);
        self::assertSame('free', $freePeriodRecord['timeType']);
        self::assertCount(2, $freePeriodRecord['formattedSlots']['05-04-2026']);

        $records->next();
        $busyPeriodRecord = $records->current();
        self::assertSame('busy', $busyPeriodRecord['timeType']);
        self::assertSame([], $busyPeriodRecord['formattedSlots']);
    }

    public function testParseAggregatesIteratorToLegacyFormat(): void
    {
        $parser = new ScheduleXmlParser();
        $fromString = $parser->parse(self::customScheduleXml());
        $fromFile = $parser->parseFile(dirname(__DIR__, 4) . '/Fixtures/soap/schedule/success.xml');

        self::assertSame($fromString, $parser->parse(self::customScheduleXml()));
        self::assertNotEmpty($fromFile);
    }

    public function testThrowsRemoteServiceExceptionBeforeYieldingScheduleRecords(): void
    {
        $xml = <<<'XML'
<?xml version="1.0"?>
<ГрафикиДляСайта>
    <ГрафикДляСайта>
        <Клиника>clinic-uid</Клиника>
        <СотрудникФИО>Петров Петр Петрович</СотрудникФИО>
        <СотрудникID>employee-uid</СотрудникID>
        <Специализация>Терапия</Специализация>
    </ГрафикДляСайта>
    <ОписаниеОшибки>schedule error</ОписаниеОшибки>
</ГрафикиДляСайта>
XML;

        $this->expectException(RemoteServiceException::class);
        iterator_to_array((new ScheduleXmlParser())->iterate($xml));
    }

    public function testIteratesLargeScheduleFileWithoutLinearMemoryGrowth(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'bit-umc-large-schedule-');
        if ($path === false) {
            self::fail('Unable to create temporary schedule fixture.');
        }

        try {
            $this->writeLargeScheduleFixture($path, 2000);

            $count = 0;
            $startMemory = memory_get_usage(true);
            $maxMemory = $startMemory;

            foreach ((new ScheduleXmlParser())->iterateFile($path) as $record) {
                $count++;
                $maxMemory = max($maxMemory, memory_get_usage(true));
                self::assertContains($record['kind'], ['employee', 'period']);
            }

            self::assertSame(2001, $count);
            self::assertLessThan(16 * 1024 * 1024, $maxMemory - $startMemory);
        } finally {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    private static function customScheduleXml(): string
    {
        return <<<'XML'
<?xml version="1.0"?>
<ГрафикиДляСайта>
    <ГрафикДляСайта>
        <Клиника>clinic-uid</Клиника>
        <СотрудникФИО>Петров Петр Петрович</СотрудникФИО>
        <СотрудникID>employee-uid</СотрудникID>
        <Специализация>Терапия</Специализация>
        <Подразделение>department-uid</Подразделение>
        <ПериодыГрафика>
            <СвободноеВремя>
                <ПериодГрафика>
                    <Клиника>clinic-uid</Клиника>
                    <Дата>2026-04-05T00:00:00</Дата>
                    <ВремяНачала>2026-04-05T09:00:00</ВремяНачала>
                    <ВремяОкончания>2026-04-05T10:00:00</ВремяОкончания>
                    <ВидВремени>time-type-uid</ВидВремени>
                    <КастомноеПолеТаймслота>slot-value</КастомноеПолеТаймслота>
                </ПериодГрафика>
            </СвободноеВремя>
            <ЗанятоеВремя>
                <ПериодГрафика>
                    <Клиника>clinic-uid</Клиника>
                    <Дата>2026-04-05T00:00:00</Дата>
                    <ВремяНачала>2026-04-05T10:00:00</ВремяНачала>
                    <ВремяОкончания>2026-04-05T10:30:00</ВремяОкончания>
                    <ВидВремени>busy-time-type-uid</ВидВремени>
                </ПериодГрафика>
            </ЗанятоеВремя>
        </ПериодыГрафика>
        <ДлительностьПриема>0001-01-01T00:30:00</ДлительностьПриема>
    </ГрафикДляСайта>
</ГрафикиДляСайта>
XML;
    }

    private function writeLargeScheduleFixture(string $path, int $periodCount): void
    {
        $stream = fopen($path, 'wb');
        if ($stream === false) {
            self::fail('Unable to open temporary schedule fixture.');
        }

        fwrite($stream, <<<'XML'
<?xml version="1.0"?>
<ГрафикиДляСайта>
    <ГрафикДляСайта>
        <Клиника>clinic-uid</Клиника>
        <СотрудникФИО>Петров Петр Петрович</СотрудникФИО>
        <СотрудникID>employee-uid</СотрудникID>
        <Специализация>Терапия</Специализация>
        <ПериодыГрафика>
            <СвободноеВремя>
XML);

        for ($index = 0; $index < $periodCount; $index++) {
            fwrite($stream, sprintf(
                <<<'XML'
                <ПериодГрафика>
                    <Клиника>clinic-uid</Клиника>
                    <Дата>2026-04-05T00:00:00</Дата>
                    <ВремяНачала>2026-04-05T%02d:00:00</ВремяНачала>
                    <ВремяОкончания>2026-04-05T%02d:30:00</ВремяОкончания>
                    <ВидВремени>time-type-uid</ВидВремени>
                </ПериодГрафика>
XML,
                $index % 23,
                $index % 23,
            ));
        }

        fwrite($stream, <<<'XML'
            </СвободноеВремя>
        </ПериодыГрафика>
        <ДлительностьПриема>0001-01-01T00:30:00</ДлительностьПриема>
    </ГрафикДляСайта>
</ГрафикиДляСайта>
XML);
        fclose($stream);
    }
}
