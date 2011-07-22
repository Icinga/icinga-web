<?php

class Reporting_JasperSchedulerModel extends JasperConfigBaseModel {
    
    /**
     * @var string
     */
    private $__uri = null;
    
    /**
     * @var JasperSoapMultipartClient
     */
    private $__client = null;
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        
        $this->__uri = $this->getParameter('uri');
        
        $this->__client = $this->getParameter('client');
        
        if (!$this->__client instanceof JasperSoapMultipartClient) {
            throw new AppKitModelException('Model needs a JasperSoapMultipart client to get work');
        }
        
        if (!$this->__uri) {
            throw new AppKitModelException('Parameter uri is mandatory');
        }
        
        if (!$this->checkUri($this->__uri)) {
            throw new AppKitModelException('URI does not match jasper config. Possible security issue!');
        }
    }
    
    public function getScheduledJobs() {
        $re = $this->__client->getReportJobs($this->__uri);
        $out = array ();
        foreach ($re as $stdclass) {
            $out[] = (array)$stdclass;
        }
        return $out;
    }
}

?>