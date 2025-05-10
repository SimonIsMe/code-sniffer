<?php

namespace Tests\Sniffs;

use PHPUnit\Framework\TestCase;
use Szymon\CodeSniffer\Sniffs\ForbiddenInheritanceSniff;
use Tests\Helpers\ProcessFileService;

class ForbiddenInheritanceSniffTest extends TestCase
{
    public function testWithoutParameters(): void
    {
        $service = new ProcessFileService();

        $errors = $service->processFile(
            __DIR__ . '/ForbiddenInheritanceSniffTest.inc',
            ForbiddenInheritanceSniff::class,
        );

        $this->assertEquals(
            [
                5 => 'Forbidden inheritance "Children" from "Parent".',
                7 => 'Forbidden inheritance "SpecificException" from "Exception".',
                9 => 'Forbidden inheritance "AnotherOne" from "RunException".',
            ],
            $errors,
        );
    }

    public function testWithParameterForParents(): void
    {
        $service = new ProcessFileService();

        $errors = $service->processFile(
            __DIR__ . '/ForbiddenInheritanceSniffTest.inc',
            ForbiddenInheritanceSniff::class,
            [
                'acceptedParentClassNameRegExpPatterns[]' => '/^(.*)Exception$/',
            ]
        );

        $this->assertEquals(
            [
                5 => 'Forbidden inheritance "Children" from "Parent".',
            ],
            $errors,
        );
    }

    public function testWithParameterForChildren(): void
    {
        $service = new ProcessFileService();

        $errors = $service->processFile(
            __DIR__ . '/ForbiddenInheritanceSniffTest.inc',
            ForbiddenInheritanceSniff::class,
            [
                'acceptedChildClassNameRegExpPatterns[]' => '/^(.*)Exception$/',
            ]
        );

        $this->assertEquals(
            [
                5 => 'Forbidden inheritance "Children" from "Parent".',
                9 => 'Forbidden inheritance "AnotherOne" from "RunException".',
            ],
            $errors,
        );
    }

    public function testWithParameterForChildrenAndParents(): void
    {
        $service = new ProcessFileService();

        $errors = $service->processFile(
            __DIR__ . '/ForbiddenInheritanceSniffTest.inc',
            ForbiddenInheritanceSniff::class,
            [
                'acceptedParentClassNameRegExpPatterns[]' => '/^(.*)Parent/',
                'acceptedChildClassNameRegExpPatterns[]' => '/^(.*)Exception$/',
            ]
        );

        $this->assertEquals(
            [
                9 => 'Forbidden inheritance "AnotherOne" from "RunException".',
            ],
            $errors,
        );
    }
}