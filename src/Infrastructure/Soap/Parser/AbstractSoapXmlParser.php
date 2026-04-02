<?php

namespace ANZ\BitUmc\SDK\Infrastructure\Soap\Parser;

use ANZ\BitUmc\SDK\Domain\Exception\RemoteServiceException;
use ANZ\BitUmc\SDK\Support\Xml\XmlElementReader;

abstract class AbstractSoapXmlParser
{
    public function __construct(
        protected readonly XmlElementReader $elementReader = new XmlElementReader(),
    ) {
    }

    protected function isTruthy(mixed $value): bool
    {
        return in_array((string) $value, ['true', '1', 'True', 'TRUE'], true);
    }

    protected function stringValue(mixed $value): string
    {
        if (is_array($value)) {
            $value = reset($value);
        }

        return is_scalar($value) ? trim((string) $value) : '';
    }

    protected function listify(mixed $value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        return is_array($value) && array_is_list($value) ? $value : [$value];
    }

    protected function failIfErrorMessage(string $message): void
    {
        if ($message !== '') {
            throw new RemoteServiceException($message);
        }
    }
}
