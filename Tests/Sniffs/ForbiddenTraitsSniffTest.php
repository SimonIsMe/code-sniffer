<?php

declare(strict_types = 1);

namespace Tests\Sniffs;

use PHPUnit\Framework\TestCase;
use Szymon\CodeSniffer\Standards\Generic\Sniffs\ForbiddenTraitsSniff;
use Tests\Helpers\ProcessFileService;

class ForbiddenTraitsSniffTest extends TestCase
{
    public function test(): void
    {
        $service = new ProcessFileService();

        $errors = $service->processFile(
            __DIR__ . '/ForbiddenTraitsSniffTest.inc',
            ForbiddenTraitsSniff::class
        );

        $this->assertEquals(
            [
                7 => 'Traits are prohibited (found "RegularTrait")',
                11 => 'Traits are prohibited (found "AnotherTrait")',
            ],
            $errors,
        );
    }
}