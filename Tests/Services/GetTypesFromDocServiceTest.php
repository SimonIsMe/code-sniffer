<?php

declare(strict_types = 1);

namespace Services;

use PHPUnit\Framework\TestCase;
use Szymon\CodeSniffer\Services\GetTypesFromDocService;

class GetTypesFromDocServiceTest extends TestCase
{
    public function testGetTypesFromDoc(): void
    {
        $service = new GetTypesFromDocService();

        $this->assertEquals(
            [],
            $service->getVarValueFromDoc('/** string something */')
        );

        $this->assertEquals(
            ['string'],
            $service->getVarValueFromDoc('/** @var string something */')
        );

        $this->assertEquals(
            ['int', 'null', 'string'],
            $service->getVarValueFromDoc('/** @var ?string | int | null something */')
        );

        $this->assertEquals(
            ['\RuntimeException[int]', 'int', 'string'],
            $service->getVarValueFromDoc('/**
            asdf
            @var string |  \RuntimeException[int]  |  int opis
            Lorem ipsum dolore
            */'),
        );

        $this->assertEquals(
            ['(float[int] | ?mixed)[]', 'float', 'int', 'null', 'string'],
            $service->getVarValueFromDoc('/**
            asdf
            @var string |  (float[int] | ?mixed)[]|  ?int | float opis
            Lorem ipsum dolore
            */'),
        );
    }
}