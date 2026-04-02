<?php

declare(strict_types=1);

namespace ANZ\BitUmc\SDK\Tests\Unit\Infrastructure\Soap;

use ANZ\BitUmc\SDK\Domain\Exception\RemoteServiceException;
use ANZ\BitUmc\SDK\Infrastructure\Soap\SoapMethod;
use ANZ\BitUmc\SDK\Infrastructure\Soap\SoapResponseParser;
use ANZ\BitUmc\SDK\Tests\FixtureHelper;
use PHPUnit\Framework\TestCase;

final class SoapResponseParserTest extends TestCase
{
    public function testReturnsSuccessForPlainOkResponse(): void
    {
        $response = (object) ['return' => FixtureHelper::read('tests/Fixtures/soap/common/ok.txt')];

        self::assertSame(['success' => true], (new SoapResponseParser())->parse(SoapMethod::CREATE_APPOINTMENT, $response));
    }

    public function testThrowsRemoteServiceExceptionForPlainErrorResponse(): void
    {
        $response = (object) ['return' => FixtureHelper::read('tests/Fixtures/soap/common/error.txt')];

        $this->expectException(RemoteServiceException::class);
        (new SoapResponseParser())->parse(SoapMethod::CREATE_APPOINTMENT, $response);
    }

    public function testThrowsRemoteServiceExceptionForPlainTextErrorResponse(): void
    {
        $response = (object) ['return' => FixtureHelper::read('tests/Fixtures/soap/status/error-description.txt')];

        $this->expectException(RemoteServiceException::class);
        (new SoapResponseParser())->parse(SoapMethod::GET_APPOINTMENT_STATUS, $response);
    }

    public function testThrowsRemoteServiceExceptionForXmlErrorResponse(): void
    {
        $response = (object) ['return' => FixtureHelper::read('tests/Fixtures/soap/common/error-description.xml')];

        $this->expectException(RemoteServiceException::class);
        (new SoapResponseParser())->parse(SoapMethod::DELETE_APPOINTMENT, $response);
    }
}
