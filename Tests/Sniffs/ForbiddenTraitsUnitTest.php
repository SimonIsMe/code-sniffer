<?php

declare(strict_types = 1);

namespace Tests\Sniffs;

use Tests\Helpers\ProcessFileService;
use PHPUnit\Framework\TestCase;
use Szymon\CodeSniffer\Sniffs\ForbiddenTraitsSniff;

class ForbiddenTraitsUnitTest extends TestCase
{
    public function test(): void
    {
        $service = new ProcessFileService();

        $errors = $service->processFile(
            __DIR__ . '/ForbiddenTraitsUnitTest.inc',
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