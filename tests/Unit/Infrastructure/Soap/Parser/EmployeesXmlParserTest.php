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

    public function testPreservesEmployeeAndServiceCustomFields(): void
    {
        $xml = <<<'XML'
<?xml version="1.0"?>
<Сотрудники>
    <Сотрудник>
        <UID>employee-uid</UID>
        <Фамилия>Петров</Фамилия>
        <Имя>Петр</Имя>
        <Отчество>Петрович</Отчество>
        <Специализация>Терапия</Специализация>
        <Организация>clinic-uid</Организация>
        <Подразделение>department-uid</Подразделение>
        <ОсновныеУслуги>
            <ОсновнаяУслуга>
                <UID>service-uid</UID>
                <Продолжительность>0001-01-01T00:30:00</Продолжительность>
                <ВозрастОт>18</ВозрастОт>
            </ОсновнаяУслуга>
        </ОсновныеУслуги>
    </Сотрудник>
</Сотрудники>
XML;

        $result = (new EmployeesXmlParser())->parse($xml);

        self::assertSame('department-uid', $result['employee-uid']['_extra']['Подразделение']);
        self::assertArrayNotHasKey('UID', $result['employee-uid']['_extra']);
        self::assertSame('18', $result['employee-uid']['services']['service-uid']['_extra']['ВозрастОт']);
        self::assertArrayNotHasKey('Продолжительность', $result['employee-uid']['services']['service-uid']['_extra']);
    }
}
