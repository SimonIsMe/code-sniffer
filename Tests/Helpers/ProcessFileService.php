<?php

declare(strict_types = 1);

namespace Tests\Helpers;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Ruleset;

class ProcessFileService
{
    public function processFile(
        string $absoluteFilePath,
        string $sniffClassName,
        array $properties = [],
    ): array
    {
        $ruleset = new Ruleset(
            new Config()
        );
        $ruleset->sniffs = [];
        $ruleset->sniffs[$sniffClassName] = new $sniffClassName();
        $ruleset->populateTokenListeners();

        foreach ($properties as $property => $value) {
            $ruleset->setSniffProperty($sniffClassName, $property, $value);
        }

        $localFile = new LocalFile(
            $absoluteFilePath,
            $ruleset,
            new Config(),
        );

        $localFile->process();
        $errors = $localFile->getErrors();

        return array_map(
            fn (array $error): string => reset($error)[0]['message'],
            $errors,
        );
    }
}