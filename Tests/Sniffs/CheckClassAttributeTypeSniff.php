<?php

namespace Sniffs;

use PHPUnit\Framework\TestCase;
use Szymon\CodeSniffer\Sniffs\CheckClassAttributeTypeSniff;
use Tests\Helpers\ProcessFileService;

class CheckClassAttributeTypeSniff extends TestCase
{
    public function testWithoutParameters(): void
    {
        $service = new ProcessFileService();

        $errors = $service->processFile(
            __DIR__ . '/CheckClassAttributeTypeSniffTest.inc',
            CheckClassAttributeTypeSniff::class,
        );

        $this->assertEquals(
            [
                6 => 'Defined types for "TestException::$value" are not identical: "array" and "string".',
                18 => 'Defined types for "TestException::$AAAAAAA" are not identical: "int|null|string" and "float|int|null|string".',
            ],
            $errors,
        );
    }
}