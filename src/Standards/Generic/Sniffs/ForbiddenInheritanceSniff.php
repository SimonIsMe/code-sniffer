<?php

namespace Szymon\CodeSniffer\Standards\Generic\Sniffs;

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
        return $phpcsFile->getTokens()[
            $phpcsFile->findPrevious(T_STRING, $stackPtr - 1)
        ]['content'];
    }

    private function getParentClassNameWithoutNamespace(File $phpcsFile, $stackPtr): string
    {
        $tokens = $phpcsFile->getTokens();
        $parentClassNameIndex = $phpcsFile->findPrevious(
            T_STRING,
            $phpcsFile->findNext(T_OPEN_CURLY_BRACKET, $stackPtr),
        );

        return $tokens[$parentClassNameIndex]['content'];
    }
}