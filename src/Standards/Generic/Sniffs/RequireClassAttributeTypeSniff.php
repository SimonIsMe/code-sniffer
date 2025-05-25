<?php

namespace Szymon\CodeSniffer\Standards\Generic\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class RequireClassAttributeTypeSniff implements Sniff
{
    public function register()
    {
        return [
            T_PRIVATE,
            T_PUBLIC,
            T_PROTECTED,
        ];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $nextFunctionTokenPosition = $this->coalesce(
            $phpcsFile->findNext(T_FUNCTION, $stackPtr, null, false, null, true),
            $phpcsFile->numTokens,
        );
        $nextSemicolonTokenPosition = $this->coalesce(
            $phpcsFile->findNext([T_SEMICOLON], $stackPtr, null, false, null, true),
            $phpcsFile->numTokens,
        );
        $nextEqualTokenPosition = $this->coalesce(
            $phpcsFile->findNext([T_EQUAL], $stackPtr, null, false, null, true),
            $phpcsFile->numTokens,
        );

        $endToken = min($nextSemicolonTokenPosition, $nextEqualTokenPosition);

        if ($endToken > $nextFunctionTokenPosition) {
            return;
        }

        $hasDeclaredTypes = $this->hasDeclaredTypes($phpcsFile, $stackPtr);
        $hasDocumentedTypes = $this->hasDocumentedTypes($phpcsFile, $stackPtr);

        if ($hasDeclaredTypes === false && $hasDocumentedTypes === false) {
            $className = $phpcsFile->getDeclarationName($phpcsFile->findPrevious(T_CLASS, $stackPtr));
            $variableName = $this->getVariableName($phpcsFile, $stackPtr);

            $phpcsFile->addError(
                '"%s::%s" variable has undefined type.',
                $stackPtr,
                'RequireClassAttributeTypeSniff',
                [
                    $className,
                    $variableName,
                ],
            );
        }
    }

    private function getVariableName(File $phpcsFile, int $stackPtr): string
    {
        $endPosition = $phpcsFile->findNext([T_EQUAL, T_SEMICOLON], $stackPtr);
        $variablePosition = $phpcsFile->findPrevious([T_STRING, T_VARIABLE], $endPosition);
        return $phpcsFile->getTokensAsString($variablePosition, 1);
    }

    private function hasDeclaredTypes(File $phpcsFile, int $stackPtr): bool
    {
        $endPosition = $phpcsFile->findNext([T_EQUAL, T_SEMICOLON], $stackPtr);
        $variablePosition = $phpcsFile->findPrevious([T_STRING, T_VARIABLE], $endPosition);

        $propertyTextDeclarationWithoutVariableName = trim(
            $phpcsFile->getTokensAsString($stackPtr, $variablePosition - $stackPtr),
        );

        $array = ['public', 'static', 'private', 'protected', 'const', 'readonly'];
        $cleanedText = trim(
            str_replace(
                $array,
                '',
                strtolower($propertyTextDeclarationWithoutVariableName)
            ),
        );

        return strlen($cleanedText) > 0;
    }

    private function hasDocumentedTypes(File $phpcsFile, int $stackPtr): bool
    {
        $openDocPosition = $this->coalesce(
            $phpcsFile->findPrevious([T_DOC_COMMENT_OPEN_TAG], $stackPtr),
            0,
        );
        $otherElementPosition = $this->coalesce(
            $phpcsFile->findPrevious([T_SEMICOLON, T_OPEN_CURLY_BRACKET, T_CLOSE_CURLY_BRACKET], $stackPtr),
            0,
        );

        if ($otherElementPosition < $openDocPosition) {
            // there's a documentation comment
            $comment = $phpcsFile->getTokensAsString($openDocPosition, $stackPtr - $openDocPosition);
            preg_match('/@var \S+/', $comment, $matches);
            return empty($matches) === false;
        }

        return false;
    }

    private function coalesce(mixed $value, int $defaultValue): int
    {
        if ($value === false) {
            return $defaultValue;
        }

        return (int) $value;
    }
}