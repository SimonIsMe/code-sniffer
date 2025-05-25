<?php

declare(strict_types=1);

namespace Szymon\CodeSniffer\Standards\Generic\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class ForbiddenTraitsSniff implements Sniff
{
    public function register()
    {
        return [T_TRAIT];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $error = 'Traits are prohibited (found "%s")';
        $traitName = $phpcsFile->getDeclarationName($stackPtr);
        $phpcsFile->addError($error, $stackPtr, 'TraitFound', [$traitName]);
    }
}
