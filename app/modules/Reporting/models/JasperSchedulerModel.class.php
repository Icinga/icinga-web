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
        if (is_array($re)) {
            foreach ($re as $stdclass) {
                
                if (isset($stdclass->previousFireTime)) {
                    $stdclass->previousFireTime = $this->context->getTranslationManager()->_d($stdclass->previousFireTime, 'date-tstamp');
                }
                
                if (isset($stdclass->nextFireTime)) {
                    $stdclass->nextFireTime = $this->context->getTranslationManager()->_d($stdclass->nextFireTime, 'date-tstamp');
                }
                
                $out[] = (array)$stdclass;
            }
        }
        return $out;
    }
    
    public function getJobDetail($job_id) {
        $out = $this->__client->getJob($job_id);
        return $out;
    }
    
    public function deleteJob($job_id) {
        $this->__client->deleteJob($job_id);
        return true;
    }
}

?>