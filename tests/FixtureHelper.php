<?php

declare(strict_types=1);

namespace ANZ\BitUmc\SDK\Tests;

final class FixtureHelper
{
    public static function read(string $relativePath): string
    {
        $fullPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . $relativePath;
        if (!is_file($fullPath)) {
            throw new \RuntimeException(sprintf('Fixture file not found: %s', $fullPath));
        }

        $content = file_get_contents($fullPath);
        if ($content === false) {
            throw new \RuntimeException(sprintf('Fixture file can not be read: %s', $fullPath));
        }

        return $content;
    }
}
