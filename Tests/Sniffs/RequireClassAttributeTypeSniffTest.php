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
                5 => '"TestException::$variableWithNotTypeAndDoc" variable has undefined type.',
                24 => '"TestException::$staticVariableWithNotTypeAndDoc" variable has undefined type.',
                43 => '"TestException::constVariableWithNotTypeAndDoc" variable has undefined type.',
                62 => '"TestException::readonlyConstVariableWithNotTypeAndDoc" variable has undefined type.',
            ],
            $errors,
        );
    }
}