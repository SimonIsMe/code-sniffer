<?php

namespace Tests\Sniffs;

use PHPUnit\Framework\TestCase;
use Szymon\CodeSniffer\Standards\Generic\Sniffs\ForbiddenInheritanceSniff;
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
                5 => 'Forbidden inheritance "\Children" from "\ParentClass".',
                7 => 'Forbidden inheritance "\SpecificException" from "\Exception".',
                9 => 'Forbidden inheritance "\AnotherOne" from "\RunException".',
                16 => 'Forbidden inheritance "\a\b\c\ChildrenA" from "\a\b\c\ParentClass".',
                18 => 'Forbidden inheritance "\a\b\c\ChildrenB" from "\ParentClass".',
                28 => 'Forbidden inheritance "\d\e\f\ChildrenA" from "\d\e\f\ParentClass".',
                30 => 'Forbidden inheritance "\d\e\f\ChildrenB" from "\a\b\c\ParentClass".',
                32 => 'Forbidden inheritance "\d\e\f\ChildrenC" from "\a\b\c\ParentClass".',
                34 => 'Forbidden inheritance "\d\e\f\ChildrenD" from "\a\b\c\ParentClass".',
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
                'acceptedParentClassNameWithNamespaceRegExpPatterns[]' => '/^(.*)Exception$/',
            ]
        );

        $this->assertEquals(
            [
                5 => 'Forbidden inheritance "\Children" from "\ParentClass".',
                16 => 'Forbidden inheritance "\a\b\c\ChildrenA" from "\a\b\c\ParentClass".',
                18 => 'Forbidden inheritance "\a\b\c\ChildrenB" from "\ParentClass".',
                28 => 'Forbidden inheritance "\d\e\f\ChildrenA" from "\d\e\f\ParentClass".',
                30 => 'Forbidden inheritance "\d\e\f\ChildrenB" from "\a\b\c\ParentClass".',
                32 => 'Forbidden inheritance "\d\e\f\ChildrenC" from "\a\b\c\ParentClass".',
                34 => 'Forbidden inheritance "\d\e\f\ChildrenD" from "\a\b\c\ParentClass".',
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
                'acceptedChildClassNameWithNamespaceRegExpPatterns[]' => '/^(.*)Exception$/',
            ]
        );

        $this->assertEquals(
            [
                5 => 'Forbidden inheritance "\Children" from "\ParentClass".',
                9 => 'Forbidden inheritance "\AnotherOne" from "\RunException".',
                16 => 'Forbidden inheritance "\a\b\c\ChildrenA" from "\a\b\c\ParentClass".',
                18 => 'Forbidden inheritance "\a\b\c\ChildrenB" from "\ParentClass".',
                28 => 'Forbidden inheritance "\d\e\f\ChildrenA" from "\d\e\f\ParentClass".',
                30 => 'Forbidden inheritance "\d\e\f\ChildrenB" from "\a\b\c\ParentClass".',
                32 => 'Forbidden inheritance "\d\e\f\ChildrenC" from "\a\b\c\ParentClass".',
                34 => 'Forbidden inheritance "\d\e\f\ChildrenD" from "\a\b\c\ParentClass".',
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
                'acceptedParentClassNameWithNamespaceRegExpPatterns[]' => '/^(.*)Parent/',
                'acceptedChildClassNameWithNamespaceRegExpPatterns[]' => '/^(.*)Exception$/',
            ]
        );

        $this->assertEquals(
            [
                9 => 'Forbidden inheritance "\AnotherOne" from "\RunException".',
            ],
            $errors,
        );
    }
}