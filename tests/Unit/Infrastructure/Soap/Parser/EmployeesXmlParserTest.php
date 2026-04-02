<?php

declare(strict_types=1);

namespace ANZ\BitUmc\SDK\Tests\Unit\Infrastructure\Soap\Parser;

use ANZ\BitUmc\SDK\Infrastructure\Soap\Parser\EmployeesXmlParser;
use ANZ\BitUmc\SDK\Tests\FixtureHelper;
use PHPUnit\Framework\TestCase;

final class EmployeesXmlParserTest extends TestCase
{
    public function testParsesEmployeesAndServices(): void
    {
        $xml = FixtureHelper::read('tests/Fixtures/soap/employees/success.xml');

        $result = (new EmployeesXmlParser())->parse($xml);

        $employee = current($result);
        self::assertIsArray($employee);
        self::assertNotSame('', $employee['fullName']);
        self::assertIsArray($employee['services']);
    }
}
