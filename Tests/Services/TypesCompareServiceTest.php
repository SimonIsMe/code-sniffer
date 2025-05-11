<?php

declare(strict_types = 1);

namespace Services;

use PHPUnit\Framework\TestCase;
use Szymon\CodeSniffer\Services\TypesCompareService;

class TypesCompareServiceTest extends TestCase
{
    public function testCompare(): void
    {
        $service = new TypesCompareService();

        $this->assertTrue(
            $service->areIdentical(
                ['bool', 'int', 'string'],
                ['bool', 'int', 'string'],
            ),
        );

        $this->assertTrue(
            $service->areIdentical(
                ['bool', 'int', 'string'],
                ['bool', 'int', 'string'],
            ),
        );

        $this->assertTrue(
            $service->areIdentical(
                ['array'],
                ['int[]', 'array'],
            ),
        );

        $this->assertTrue(
            $service->areIdentical(
                ['array'],
                ['\Exception[]'],
            ),
        );

        $this->assertTrue(
            $service->areIdentical(
                ['array', 'float'],
                ['\Exception[int|string]', '(mixed|int[])[]', 'float'],
            ),
        );
    }
}