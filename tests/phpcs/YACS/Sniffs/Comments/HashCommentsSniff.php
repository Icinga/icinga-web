<?php

class YACS_Sniffs_Comments_HashCommentsSniff implements PHP_CodeSniffer_Sniff {

        public function register() {
                return array(T_COMMENT);
        }
        
        public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
                $tokens = $phpcsFile->getTokens();
                if ($tokens[$stackPtr]['content']{0} === '#') {
                    $error = 'Hash comments are prohibited; found %s';
                    $data  = array(trim($tokens[$stackPtr]['content']));
                    $phpcsFile->addError($error, $stackPtr, 'Found', $data);
                }
        }
}

?>