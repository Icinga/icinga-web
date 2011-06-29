<?php

class Reporting_JasperSoapFactoryModel extends ReportingBaseModel implements AgaviISingletonModel {
    
    const SERVICE_SCHEDULER        = 'ReportScheduler';
    const SERVICE_PERMISSIONS      = 'PermissionsManagementService';
    const SERVICE_USER             = 'UserAndRoleManagementService';
    const SERVICE_REPOSITORY       = 'repository';
    const SERVICE_ADMIN            = 'AdminService';
    const SERVICE_VERSION          = 'Version';
    
    private $clients               = array ();
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        
        $config_name = $this->getParameter('jasperconfig');
        $config = AgaviConfig::get($config_name);
        
        if (is_array($config) == false) {
            throw new AppKitModelException('Namespace for jasperconfig "'. $config_name. '" not found');
        }
        
        $this->setParameters($config);
    }
    
    protected function wrapWsdl($service_name) {
        return sprintf('%s/services/%s?wsdl', $this->getParameter('jasper_url'), $service_name);
    }
    
    /**
     * Creates a configured SOAP client
     * @param string $url
     * @param array $additional_options
     * @return SoapClient
     */
    protected function getSoapClient($wsdl, array $additional_options=array()) {
        if (!isset($this->clients[$wsdl]) || !$this->clients[$wsdl] instanceof SoapClient) {
        
        $options = array (
            'cache_wsdl'    => WSDL_CACHE_NONE,
            'trace'         => true,
            'exceptions'	=> true
        );
        
        if ($this->getParameter('jasper_user') !== null) {
            $options['login'] = $this->getParameter('jasper_user');
        }
        
        if ($this->getParameter('jasper_pass') !== null) {
            $options['password'] = $this->getParameter('jasper_pass');
        }
        
        $this->clients[$wsdl] = new SoapClient($wsdl, $options);
        
        }
        
        return $this->clients[$wsdl];
    }
    
    /**
     * Just a wrapper to get the configured client for a Jasper service name (class constants)
     * @param string $service_name
     * @return SoapClient
     */
    public function getSoapClientForWSDL($service_name) {
        return $this->getSoapClient($this->wrapWsdl($service_name));
    }
    
    /**
     * Checks if we can use the jasper server at the soap side
     * @return boolean true response
     */
    public function pingServer() {
        try {
            $client = $this->getSoapClientForWSDL(self::SERVICE_VERSION);
            $response = $client->getVersion();
            
        } catch (Exception $e) {
            $response = '';
        }
        
        return ( preg_match('/^apache axis[^\d]+\d+\.\d+/i', $response) ) ? true : false;
    }
    
}

?>