<?php

declare(strict_types=1);

namespace Szymon\CodeSniffer\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class ForbiddenStaticFunctionSniff implements Sniff
{
    protected $warningMessage = 'Static method cannot be intercepted and its use is discouraged.';

    protected $warningCode = 'StaticFunction';

    public function register()
    {
        return [T_STATIC];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $posOfFunction = $phpcsFile->findNext(T_FUNCTION, $stackPtr) + 1;
        $tokens = array_slice($phpcsFile->getTokens(), $stackPtr, $posOfFunction - $stackPtr);

        $allowedTypes = [T_STATIC => true, T_WHITESPACE => true, T_FUNCTION => true];
        foreach ($tokens as $token) {
            $code = $token['code'];
            if (!array_key_exists($code, $allowedTypes)) {
                break;
            }

            if ($code === T_FUNCTION) {
                $phpcsFile->addError($this->warningMessage, $posOfFunction, $this->warningCode);
            }
        }
    }
}
