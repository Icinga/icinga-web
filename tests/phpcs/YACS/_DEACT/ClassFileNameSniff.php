<?php

class YACS_Sniffs_Classes_ClassFileNameSniff implements PHP_CodeSniffer_Sniff { 
        
        public function register() {
                return array(T_CLASS, T_INTERFACE);
        }
        
        public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
                $fullPath = basename($phpcsFile->getFilename());
                $decName  = $phpcsFile->findNext(T_STRING, $stackPtr);
                $tokens   = $phpcsFile->getTokens();
                $fileName = substr($fullPath, 0, strrpos($fullPath, '.'));
                
                $parts = explode('.', $fullPath, 3);
                $extension = array_pop($parts);
                $type = null;
                
                if (count($parts) == 2) {
                        $type = array_pop($parts);
                }
                
                $className = array_pop($parts);
                
/*
 * ucfirst($tokens[$stackPtr]['content']),
                              $tokens[$stackPtr+2]['content'],
                              $tokens[$stackPtr]['content'],
                              $fileName,
 */

/*                
                if ($tokens[$decName]['content'] !== $className) {
                        $error = 'Filename %s does not match class %s';
                        $data  = array(
                                $fullPath,
                                $tokens[$stackPtr+2]['content']
                        );
                        $phpcsFile->addError($error, $stackPtr, 'NoMatch', $data);
                }
*/              
                if ($type == null) {
                        $error = "Type is missing for file: %s; expected %s.%s.php";
                        $phpcsFile->addError($error, $stackPtr, 'NoType', array(
                                $tokens[$stackPtr]['content'],
                                $className,
                                $tokens[$stackPtr]['content']
                        ));
                }
                
                if ($type !== null && $type !== $tokens[$stackPtr]['content']) {
                        $error = "Type is wrong for file: %s.%s.php; expected %s (%s %s)";
                        $phpcsFile->addError($error, $stackPtr, 'TypeMismatch', array(
                                $className,
                                $type,
                                $tokens[$stackPtr]['content'],
                                $tokens[$stackPtr]['content'],
                                $tokens[$stackPtr+2]['content']
                        ));
                }

/*                
                if (PHP_CodeSniffer::isCamelCaps($tokens[$stackPtr+2]['content'], true, false, false) === false) {
                        $error = "Class- or interface-name is not camel caps: %s";
                        $phpcsFile->addError($error, $stackPtr, 'WrongFormat', array(
                                $tokens[$stackPtr+2]['content']
                        ));
                }
*/
        }

}

?>
