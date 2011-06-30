<?php

class Reporting_ReportUserFileModel extends ReportingBaseModel {
    
    private $__extensionMap = array (
        'pdf' => 'pdf',
        'csv' => 'csv'
    );
    
    private $__dir = null;
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        
        $this->__dir = AgaviConfig::get('modules.reporting.dir.download');
        
        if (!is_dir($this->__dir)) {
            mkdir($this->__dir);
        }
        
        if (!is_dir($this->__dir)) {
            throw new AppKitModelException('Could not create dir: '. $this->__dir);
        }
    }
    
    public function getExtensionFromFormat($format) {
        if (array_key_exists($format, $this->__extensionMap)) {
            return $this->__extensionMap[$format];
        }
        
        throw new AppKitModelException('Extension for format '. $format. ' not found');
    }
    
    public function storeFile($data, $output_format) {
        
    }
}

?>