<?php

declare(strict_types=1);

namespace Pest;

function version(): string
{
    return '3.8.4';
}

function testDirectory(string $file = ''): string
{
    return TestSuite::getInstance()->testPath.DIRECTORY_SEPARATOR.$file;
}
