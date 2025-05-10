<?php

namespace Sniffs;

use PHPUnit\Framework\TestCase;
use Szymon\CodeSniffer\Sniffs\RequireClassAttributeTypeSniff;
use Tests\Helpers\ProcessFileService;

class RequireClassAttributeTypeSniffTest extends TestCase
{
    public function testWithoutParameters(): void
    {
        $service = new ProcessFileService();

        $errors = $service->processFile(
            __DIR__ . '/RequireClassAttributeTypeSniffTest.inc',
            RequireClassAttributeTypeSniff::class,
        );

        $this->assertEquals(
            [
                9 => 'Defined types for "TestException::$AAAAAAA" are not identical: "int|null|string" and "float|int|null|string"',
            ],
            $errors,
        );
    }
}