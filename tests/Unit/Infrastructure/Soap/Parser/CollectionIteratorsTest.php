<?php

declare(strict_types=1);

namespace ANZ\BitUmc\SDK\Tests\Unit\Infrastructure\Soap\Parser;

use ANZ\BitUmc\SDK\Infrastructure\Soap\Parser\ClinicsXmlParser;
use ANZ\BitUmc\SDK\Infrastructure\Soap\Parser\EmployeesXmlParser;
use ANZ\BitUmc\SDK\Infrastructure\Soap\Parser\NomenclatureXmlParser;
use ANZ\BitUmc\SDK\Tests\FixtureHelper;
use PHPUnit\Framework\TestCase;

final class CollectionIteratorsTest extends TestCase
{
    public function testIteratorsReturnTheSameDataAsLegacyParsers(): void
    {
        $cases = [
            [new ClinicsXmlParser(), 'tests/Fixtures/soap/clinics/success.xml'],
            [new EmployeesXmlParser(), 'tests/Fixtures/soap/employees/success.xml'],
            [new NomenclatureXmlParser(), 'tests/Fixtures/soap/nomenclature/success.xml'],
        ];

        foreach ($cases as [$parser, $fixture]) {
            $xml = FixtureHelper::read($fixture);

            self::assertSame($parser->parse($xml), iterator_to_array($parser->iterate($xml)));
        }
    }

    public function testEmployeeIteratorCanBeStoppedWithoutCollectingAllItems(): void
    {
        $xml = FixtureHelper::read('tests/Fixtures/soap/employees/success.xml');
        $iterator = (new EmployeesXmlParser())->iterate($xml);

        foreach ($iterator as $employee) {
            self::assertNotSame('', $employee['uid']);
            break;
        }

        self::assertTrue(true);
    }
}
