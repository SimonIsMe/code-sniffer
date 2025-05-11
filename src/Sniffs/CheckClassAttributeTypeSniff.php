<?php

namespace Szymon\CodeSniffer\Sniffs;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use Szymon\CodeSniffer\Services\GetTypesFromDocService;
use Szymon\CodeSniffer\Services\TypesCompareService;

class CheckClassAttributeTypeSniff implements Sniff
{
    public function register()
    {
        return [
            T_PRIVATE,
            T_PUBLIC,
            T_PROTECTED,
            T_CONST,
        ];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $nextFunctionTokenPosition = $phpcsFile->findNext([T_FUNCTION], $stackPtr + 1);
        $nextVariableTokenPosition = $phpcsFile->findNext([T_VARIABLE], $stackPtr + 1);

        if ($nextFunctionTokenPosition !== false && $nextFunctionTokenPosition < $nextVariableTokenPosition) {
            // it's not a parameter declaration
            return;
        }

        $declaredTypes = $this->getDeclaredTypes($phpcsFile, $stackPtr);
        $documentedTypes = $this->getDocumentedTypes($phpcsFile, $stackPtr);

        $className = $phpcsFile->getDeclarationName($phpcsFile->findPrevious(T_CLASS, $stackPtr));
        $variableName = $phpcsFile->getTokensAsString($nextVariableTokenPosition, 1);

        if (empty($declaredTypes) && empty($documentedTypes)) {
            $phpcsFile->addError(
                'Undefined type for "%s::%s".',
                $stackPtr,
                'aa',
                [
                    $className,
                    $variableName,
                ],
            );
        }

        if ($this->areTypesIdentical($declaredTypes, $documentedTypes)) {
            $phpcsFile->addError(
                'Defined types for "%s::%s" are not identical: "%s" and "%s".',
                $stackPtr,
                'aa',
                [
                    $className,
                    $variableName,
                    implode('|', $declaredTypes),
                    implode('|', $documentedTypes),
                ]
            );
        }
    }

    private function areTypesIdentical(array $declaredTypes, array $documentedTypes): bool
    {
        $typesCompareService = new TypesCompareService();
        $hasDeclaredTypes = empty($declaredTypes);
        $hasDocumentedTypes = empty($documentedTypes);

        return $hasDeclaredTypes === false
            && $hasDocumentedTypes === false
            && $typesCompareService->areIdentical($declaredTypes, $documentedTypes) === false;
    }

    /**
     * @return string[]
     */
    private function getDeclaredTypes(File $phpcsFile, int $stackPtr): array
    {
        $endPosition = $phpcsFile->findNext([T_EQUAL, T_SEMICOLON], $stackPtr + 1);
        $propertyTextDeclaration = trim($phpcsFile->getTokensAsString($stackPtr, $endPosition - $stackPtr));
        return $this->getPropertyTypes($propertyTextDeclaration);
    }

    /**
     * @return string[]
     */
    private function getDocumentedTypes(File $phpcsFile, int $stackPtr): array
    {
        $openDocPosition = $phpcsFile->findPrevious([T_DOC_COMMENT_OPEN_TAG], $stackPtr - 1);
        $otherElementPosition = $phpcsFile->findPrevious([T_SEMICOLON, T_OPEN_CURLY_BRACKET, T_CLOSE_CURLY_BRACKET], $stackPtr - 1);
        if ($openDocPosition !== false && $otherElementPosition < $openDocPosition) {
            return $this->getVarValueFromDoc(
                $phpcsFile->getTokensAsString($openDocPosition, $stackPtr - $openDocPosition),
            );
        }

        return [];
    }

    /**
     * @return string[]
     */
    private function getVarValueFromDoc(string $docContent): array
    {
        $service = new GetTypesFromDocService();
        return $service->getVarValueFromDoc($docContent);
    }

    private function getPropertyName(string $declarationText): string
    {
        $exploded = explode(' ', $declarationText);
        return end($exploded);
    }

    /**
     * @return string[]
     */
    private function getPropertyTypes(string $declarationText): array
    {
        $cleanedText = trim(str_replace(['public', 'private', 'protected', 'static', 'const'], '', strtolower($declarationText)));
        $propertyName = $this->getPropertyName($declarationText);

        $rawTypes = trim(substr($cleanedText, 0, -strlen($propertyName)));

        return $this->convertStringToArrayOfTypes($rawTypes);
    }

    /**
     * @param string $types
     * @return string[]
     */
    private function convertStringToArrayOfTypes(string $types): array
    {
        $types = array_map(
            'trim',
            explode('|', $types),
        );

        $types = array_filter(
            $types,
            fn (string $type): bool => $type !== '',
        );

        // change "?<type>" to [ null, <type> ] array
        foreach ($types as $i => $type) {
            if (substr($type, 0, 1) === '?') {
                $types[$i] = substr($type,  1);
                $types[] = 'null';
            }
        }

        $types = array_values(array_unique($types));

        sort($types);

        return $types;
    }
}