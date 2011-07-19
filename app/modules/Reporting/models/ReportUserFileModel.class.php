<?php

class Reporting_ReportUserFileModel extends ReportingBaseModel implements AgaviISingletonModel {
    
    const STORE_NAME = 'org.icinga.reporting.userfile';
    const MAX_TIME = 300;
    
    private static $__arrayTestKeys = array (
        'filename', 'bytes', 'format', 'checksum',
        'reportname', 'pushname'
    );
    
    private $__extensionMap = array (
        'pdf' => 'pdf',
        'csv' => 'csv',
        'html' => 'html',
        'xml' => 'xml',
        'rtf' => 'rtf',
        'xls' => 'xls'
    );
    
    private $__dir = null;
    
    /**
     * @var AppKitSecurityUser
     */
    private $__user = null;
    
    private function fileGarbageCollector() {
        $it = new FilesystemIterator($this->__dir, FilesystemIterator::CURRENT_AS_FILEINFO);
        $count = 0;
        foreach ($it as $fileInfo) {
            $diff = time() - $fileInfo->getCTime();
            if ($diff > self::MAX_TIME) {
                if (unlink($fileInfo->getRealPath())) {
                    $count++;
                }
                else {
                    throw new AppKitModelException('Could not delete report user file: '. $fileInfo->getBaseName());
                }
            }
        }
        
        if ($count>0) {
            $this->getContext()->getLoggerManager()->log('Deleted '. $count. ' reporting user files', AgaviLogger::INFO);
            return true;
        }
        
        return false;
    }
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        
        $this->__dir = AgaviConfig::get('modules.reporting.dir.download');
        
        if (!is_dir($this->__dir)) {
            mkdir($this->__dir);
        }
        
        if (!is_dir($this->__dir)) {
            throw new AppKitModelException('Could not create dir: '. $this->__dir);
        }
        else {
            $this->fileGarbageCollector();
        }
        
        $this->__user = $this->getContext()->getUser();
        
        if (!$this->__user instanceof AgaviSecurityUser) {
            throw new AppKitModelException('Could not get user instance');
        }
    }
    
    public function getExtensionFromFormat($format) {
        if (array_key_exists($format, $this->__extensionMap)) {
            return $this->__extensionMap[$format];
        }
        
        throw new AppKitModelException('Extension for format '. $format. ' not found');
    }
    
    private function getNewFilename($extension) {
        $username = $this->__user->getNsmUser()->user_name;
        $md5 = md5($username. '-'. microtime(true). '-'. getmypid());
        return sprintf('%s_%s.%s', $username, $md5, $extension);
    }
    
    public function storeFile($data, $output_format, JasperResourceDescriptor $rd) {
        $extension = $this->getExtensionFromFormat($output_format);
        $filename = sprintf('%s/%s', $this->__dir, $this->getNewFilename($extension));
        $bytes = file_put_contents($filename, $data);
        
        if (!strlen($data) == $bytes) {
            throw new AppKitModelException('Bytes written different to source data');
        }
        
        $struct = array (
            'filename'         => $filename,
            'format'           => $output_format,
            'bytes'            => $bytes,
            'checksum'         => md5($data),
            'reportname'       => $rd->getResourceDescriptor()->getParameter(JasperResourceDescriptor::DESCRIPTOR_ATTR_NAME),
            'pushname'         => sprintf('%s.%s', $rd->getResourceDescriptor()->getParameter(JasperResourceDescriptor::DESCRIPTOR_ATTR_NAME), $extension)
            
        );
        
        $this->getContext()->getStorage()->write(self::STORE_NAME, $struct);
        
        return true;
    }
    
    public function getUserFileStruct() {
        $struct = $this->getContext()->getStorage()->read(self::STORE_NAME);
        
        if (!is_array($struct)) {
            throw new AppKitModelException('Could not get struct');
        }
        
        foreach (self::$__arrayTestKeys as $testKey) {
            if (!array_key_exists($testKey, $struct)) {
                throw new AppKitModelException('User file missing information: '. $testKey);
            }
        }
        
        if (!is_file($struct['filename'])) {
            throw new AppKitModelException('User does not exists anymore');
        }
        
        return $struct;
    }
    
    public function getFilePointer() {
        $struct = $this->getUserFileStruct();
        
        if (!is_file($struct['filename'])) {
            throw new AppKitModelException('User does not exists anymore');
        }
        
//         $contents = file_get_contents($struct['filename']);
        
//         if (!md5($contents) == $struct['checksum']) {
//             throw new AppKitModelException('Checksum does not match, abort!');
//         }
        
//         if ($delete_after_receive === true) {
//             unlink($struct['filename']);
//         }
        
        
        return fopen($struct['filename'], 'r');
    }
}

?>