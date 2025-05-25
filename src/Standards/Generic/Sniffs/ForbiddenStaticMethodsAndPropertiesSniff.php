<?php

declare(strict_types=1);

namespace Szymon\CodeSniffer\Standards\Generic\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class ForbiddenStaticMethodsAndPropertiesSniff implements Sniff
{
    public function register()
    {
        return [T_STATIC];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        if ($phpcsFile->getTokens()[$stackPtr + 1]['type'] !== 'T_DOUBLE_COLON') {
            $phpcsFile->addError('Static methods and properties are forbidden.', $stackPtr, 'StaticFunction');
        }
    }
}
