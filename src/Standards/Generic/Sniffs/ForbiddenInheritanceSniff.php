<?php

namespace Szymon\CodeSniffer\Standards\Generic\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ForbiddenInheritanceSniff implements Sniff
{
    public array $acceptedChildClassNameWithNamespaceRegExpPatterns = [];

    public array $acceptedParentClassNameWithNamespaceRegExpPatterns = [];

    public function register()
    {
        return [T_EXTENDS];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $childClassNameWithNamespace = $this->getChildClassNameWithNamespace($phpcsFile, $stackPtr);
        $isInAcceptedChildClassNamesPatterns = $this->isInPatternArray(
            $childClassNameWithNamespace,
            $this->acceptedChildClassNameWithNamespaceRegExpPatterns,
        );

        $parentClassNameWithNamespace = $this->getParentClassNameWithNamespace($phpcsFile, $stackPtr);
        $isInAcceptedParentClassNamesPatterns = $this->isInPatternArray(
            $parentClassNameWithNamespace,
            $this->acceptedParentClassNameWithNamespaceRegExpPatterns,
        );

        if ($isInAcceptedChildClassNamesPatterns === false
            && $isInAcceptedParentClassNamesPatterns === false
        ) {
            $phpcsFile->addError(
                'Forbidden inheritance "%s" from "%s".', $stackPtr,
                'aa',
                [
                    $childClassNameWithNamespace,
                    $parentClassNameWithNamespace,
                ],
            );
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

    private function getChildClassNameWithNamespace(File $phpcsFile, $stackPtr): string
    {
        $namespace = $this->getNamespace($phpcsFile, $stackPtr);
        $className = $this->getChildClassName($phpcsFile, $stackPtr);

        if ($namespace === '') {
            return '\\' . $className;
        }
        return '\\' . $namespace . '\\' . $className;
    }

    private function getParentClassNameWithNamespace(File $phpcsFile, $stackPtr): string
    {
        $namespace = $this->getNamespace($phpcsFile, $stackPtr);
        $endToken = $phpcsFile->findNext([T_OPEN_CURLY_BRACKET, T_IMPLEMENTS], $stackPtr);
        $startToken = $phpcsFile->findPrevious(T_EXTENDS, $endToken);

        $parentClassNameIndex = trim($phpcsFile->getTokensAsString($startToken + 1, $endToken - $startToken - 1));

        $explodedParentClassNameIndex = explode('\\', $parentClassNameIndex);
        $init = array_shift($explodedParentClassNameIndex);

        $imports = $this->getAllImports($phpcsFile, $stackPtr);
        if (array_key_exists($init, $imports)) {
            if (!empty($explodedParentClassNameIndex)) {
                return $imports[$init] . '\\' . implode('\\', $explodedParentClassNameIndex);
            }
            return $imports[$init];
        }

        if (substr($parentClassNameIndex, 0, 1) === '\\') {
            return $parentClassNameIndex;
        }

        if ($namespace === '') {
            return '\\' .  ltrim($parentClassNameIndex, '\\');
        }

        return '\\' . $namespace . '\\' .  ltrim($parentClassNameIndex, '\\');
    }

    private function getNamespace(File $phpcsFile, $stackPtr): string
    {
        $namespaceTokenPosition = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr - 1);
        if ($namespaceTokenPosition === false) {
            return '';
        }

        $endTokenPosition = $phpcsFile->findNext(T_SEMICOLON, $namespaceTokenPosition + 1);

        return trim($phpcsFile->getTokensAsString($namespaceTokenPosition + 1, $endTokenPosition - $namespaceTokenPosition - 1));
    }

    /**
     * @return string[]
     */
    private function getAllImports(File $phpcsFile, int $stackPtr): array
    {
        $importsRaw = [];

        do {
            $useTokenPosition = $phpcsFile->findPrevious(T_USE, $stackPtr - 1);
            if ($useTokenPosition === false) {
                break;
            }
            $endLinePosition = $phpcsFile->findNext(T_SEMICOLON, $useTokenPosition + 1);
            $importsRaw[] = trim($phpcsFile->getTokensAsString($useTokenPosition + 1, $endLinePosition - $useTokenPosition - 1));

            $stackPtr = $useTokenPosition - 1;
        } while (true);


        $imports = [];
        foreach ($importsRaw as $importRaw) {
            $hasAs = stripos($importRaw, ' as ');
            if ($hasAs) {
                $exploded = preg_split('/ as /i', $importRaw);
                $className = $exploded[1];
                $imports[$className] = '\\' . $exploded[0];
            } else {
                $exploded = explode('\\', $importRaw);
                $className = end($exploded);
                $imports[$className] = '\\' . $importRaw;
            }
        }

        return $imports;
    }
}