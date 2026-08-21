<?php

declare(strict_types=1);

namespace ANZ\BitUmc\SDK\Tests\Unit\Infrastructure\Soap\Stream;

use ANZ\BitUmc\SDK\Domain\Exception\ResponseParseException;
use ANZ\BitUmc\SDK\Infrastructure\Soap\Transport\Stream\SoapEnvelopeExtractor;
use ANZ\BitUmc\SDK\Infrastructure\Soap\Parser\ClinicsXmlParser;
use ANZ\BitUmc\SDK\Support\Xml\XmlElementReader;
use PHPUnit\Framework\TestCase;

final class SoapEnvelopeExtractorTest extends TestCase
{
    public function testExtractsEscapedReturnPayload(): void
    {
        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope"><soap:Body><response><return>&lt;items&gt;&lt;item&gt;one&lt;/item&gt;&lt;/items&gt;</return></response></soap:Body></soap:Envelope>');
        rewind($stream);

        $payload = (new SoapEnvelopeExtractor())->extract($stream, 1024);

        self::assertSame('<items><item>one</item></items>', $payload->read());
        $reader = (new XmlElementReader())->createReaderFromFile($payload->getPath());
        self::assertTrue($reader->read());
        self::assertSame('items', $reader->localName);
        $reader->close();
        fclose($stream);
    }

    public function testThrowsWhenPayloadExceedsLimit(): void
    {
        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, '<Envelope><Body><return>12345</return></Body></Envelope>');
        rewind($stream);

        $this->expectException(ResponseParseException::class);
        (new SoapEnvelopeExtractor())->extract($stream, 4);
    }

    public function testThrowsWhenEnvelopeContainsSoapFault(): void
    {
        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, '<Envelope><Body><Fault><Reason><Text>Access denied</Text></Reason></Fault></Body></Envelope>');
        rewind($stream);

        $this->expectException(ResponseParseException::class);
        $this->expectExceptionMessage('Access denied');
        (new SoapEnvelopeExtractor())->extract($stream, 1024);
    }

    public function testPayloadCanBeParsedFromFile(): void
    {
        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, '<Envelope><Body><return>&lt;СписокКлиник&gt;&lt;Клиника&gt;&lt;УИД&gt;clinic-uid&lt;/УИД&gt;&lt;Наименование&gt;Клиника&lt;/Наименование&gt;&lt;/Клиника&gt;&lt;/СписокКлиник&gt;</return></Body></Envelope>');
        rewind($stream);

        $payload = (new SoapEnvelopeExtractor())->extract($stream, 1024);

        self::assertSame('Клиника', (new ClinicsXmlParser())->parseFile($payload->getPath())['clinic-uid']['name']);
        fclose($stream);
    }
}
