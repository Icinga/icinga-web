<?php

class YACS_Sniffs_Classes_ClassDeclarationSniff implements PHP_CodeSniffer_Sniff {

        public function register() {
                return array(T_CLASS, T_INTERFACE);
        }
        
        public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
                $tokens = $phpcsFile->getTokens();
                $errorData = array($tokens[$stackPtr]['content']);
                
                $curlyBrace  = $tokens[$stackPtr]['scope_opener'];
                $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($curlyBrace - 1), $stackPtr, true);
                $classLine   = $tokens[$lastContent]['line'];
                $braceLine   = $tokens[$curlyBrace]['line'];
                
                if ($braceLine !== $classLine) {
                        $error = 'Open brace for %s must be on the same line (like K+R function)';
                        $phpcsFile->addError($error, $curlyBrace, 'OpenBraceSameLine', $errorData);
                }
        }
}

?>