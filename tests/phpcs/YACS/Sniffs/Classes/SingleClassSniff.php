<?php

class YACS_Sniffs_Classes_SingleClassSniff implements PHP_CodeSniffer_Sniff {

        public function register() {
                return array(T_CLASS, T_INTERFACE);
        }
        
        public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
                
                $nextClass = $phpcsFile->findNext($this->register(), ($stackPtr + 1));
                
                if ($nextClass !== false) {
                    $error = 'Only one interface or class is allowed in a file';
                    $phpcsFile->addError($error, $nextClass, 'MultipleClasses');
                }
        }
}

?>