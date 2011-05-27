<?php

class YACS_Sniffs_Functions_FunctionDeclarationSniff  implements PHP_CodeSniffer_Sniff {

        public function register() {
                return array(T_CLASS, T_INTERFACE);
        }
        
        public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
                $tokens = $phpcsFile->getTokens();
                
                $start = $tokens[$stackPtr]['scope_opener'];
                $end = $tokens[$stackPtr]['scope_closer'];
                
                $wantedTokens = array(T_FUNCTION);
                $accessors = array(T_PUBLIC, T_PRIVATE, T_PROTECTED);
                
                
                $next = $phpcsFile->findNext($wantedTokens, ($start + 1), $end);
                while ($next !== false && $next < $end) {
                        
                        if ($tokens[$next]['level'] == 1) {
                                        $fnamePtr = $phpcsFile->findNext(T_STRING, $next+1, $next+3);
                                        $fname = $tokens[$fnamePtr]['content'];
                                        if ($fnamePtr !== false && $fname) {
                                                $accessorPtr = $phpcsFile->findPrevious($accessors, $next-1, $next-3);
                                                if ($accessorPtr == false) {
                                                        $error = '%s method %s::%s must have an accessor';
                                                        $phpcsFile->addError($error, $stackPtr, 'NoAccessorFound', array (
                                                                ucfirst($tokens[$stackPtr]['content']),
                                                                $tokens[$stackPtr+2]['content'],
                                                                $fname
                                                        ));
                                                }
                                        }
                        }
                        
                        $next = $phpcsFile->findNext($wantedTokens, ($next + 1), $end);
                }
        }
}

?>