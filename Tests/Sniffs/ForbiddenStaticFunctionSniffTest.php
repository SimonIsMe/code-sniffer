<?php

namespace Tests\Sniffs;

use PHPUnit\Framework\TestCase;
use Szymon\CodeSniffer\Standards\Generic\Sniffs\ForbiddenStaticMethodsAndPropertiesSniff;
use Tests\Helpers\ProcessFileService;

class ForbiddenStaticFunctionSniffTest extends TestCase
{
    public function test(): void
    {
        $service = new ProcessFileService();

        $errors = $service->processFile(
            __DIR__ . '/ForbiddenStaticMethodsAndPropertiesSniffTest.inc',
            ForbiddenStaticMethodsAndPropertiesSniff::class
        );

        $this->assertEquals(
            [
                7 => 'Static methods and properties are forbidden.',
                9 => 'Static methods and properties are forbidden.',
                11 => 'Static methods and properties are forbidden.',
                13 => 'Static methods and properties are forbidden.',
            ],
            $errors,
        );
    }
}