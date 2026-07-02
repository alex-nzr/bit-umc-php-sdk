<?php

declare(strict_types=1);

namespace ANZ\BitUmc\SDK\Tests\Unit\Tools;

use ANZ\BitUmc\SDK\Tools\DateFormatter;
use PHPUnit\Framework\TestCase;

final class DateFormatterTest extends TestCase
{
    public function testFormatsDurationFromIsoToSeconds(): void
    {
        self::assertSame(5400, DateFormatter::formatDurationFromIsoToSeconds('0001-01-01T01:30:00'));
        self::assertSame(0, DateFormatter::formatDurationFromIsoToSeconds('not-a-date'));
    }

    public function testCalculatesDurationFromSecondsUsingFullHoursAndMinutes(): void
    {
        self::assertSame('0001-01-01T01:36:00', DateFormatter::calculateDurationFromSeconds(5760));
        self::assertSame('0001-01-01T00:59:00', DateFormatter::calculateDurationFromSeconds(3599));
        self::assertSame('0001-01-01T00:45:00', DateFormatter::calculateDurationFromSeconds(2700));
    }
}
