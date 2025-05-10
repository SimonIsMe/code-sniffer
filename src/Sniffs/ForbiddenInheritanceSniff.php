<?php

namespace Szymon\CodeSniffer\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ForbiddenInheritanceSniff implements Sniff
{
    public array $acceptedChildClassNameRegExpPatterns = [];

    public array $acceptedParentClassNameRegExpPatterns = [];

    public function register()
    {
        return [T_EXTENDS];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $childClassName = $this->getChildClassName($phpcsFile, $stackPtr);
        $isInAcceptedChildClassNamesPatterns = $this->isInPatternArray(
            $childClassName,
            $this->acceptedChildClassNameRegExpPatterns,
        );

        $parentClassName = $this->getParentClassNameWithoutNamespace($phpcsFile, $stackPtr);
        $isInAcceptedParentClassNamesPatterns = $this->isInPatternArray(
            $parentClassName,
            $this->acceptedParentClassNameRegExpPatterns,
        );

        if ($isInAcceptedChildClassNamesPatterns === false
            && $isInAcceptedParentClassNamesPatterns === false
        ) {
            $phpcsFile->addError('Forbidden inheritance "%s" from "%s".', $stackPtr, 'aa', [$childClassName, $parentClassName]);
        }
    }

    private function isInPatternArray(string $className, array $classNamePatterns): bool
    {
        foreach ($classNamePatterns as $classNamePattern) {
            if(@preg_match($classNamePattern, '') === false){
                throw new \Exception('Invalid regular expression pattern " ' . $classNamePattern . '".');
            }

            if (preg_match($classNamePattern, $className) === 1) {
                return true;
            }
        }

        return false;
    }

    private function getChildClassName(File $phpcsFile, $stackPtr): string
    {
        for ($i = $stackPtr - 1; $i >= 0; $i--) {
            $token = $phpcsFile->getTokens()[$i];
            if ($token['type'] === 'T_STRING') {
                return $token['content'];
            }
        }

        throw new \Exception('No child class name found');
    }

    private function getParentClassNameWithoutNamespace(File $phpcsFile, $stackPtr): string
    {
        $countAllTokens = count($phpcsFile->getTokens()) - 1;
        for ($i = $stackPtr + 1; $i < $countAllTokens; $i++) {
            $token = $phpcsFile->getTokens()[$i];
            $nextToken = $phpcsFile->getTokens()[$i + 1];
            if ($token['type'] === 'T_NS_SEPARATOR' || $nextToken['type'] === 'T_NS_SEPARATOR') {
                continue;
            }
            if ($token['type'] === 'T_STRING') {
                return $token['content'];
            }
        }

        throw new \Exception('No parent class name found');
    }
}