<?php

abstract class JasperConfigBaseModel extends IcingaBaseModel {
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        $this->applyJasperConfigToParameters($this->getParameter('jasperconfig'));
    }
    
    private function applyJasperConfigToParameters($config_ns) {
        $config = AgaviConfig::get($config_ns);
        
        if ($config === null) {
            throw new AppKitModelException('Jasperconfiguration "'. $config_ns. '" not in agavi config space');
        }
        
        if (!is_array($config)) {
            throw new AppKitModelException('Jasperconfig must be an array!');
        }
        
        if (!array_key_exists('jasper_url', $config)) {
            throw new AppKitModelException('Jasperconfig needs "jasper_url" as parameter');
        }
        
        $this->setParameters($config);
    }
}
?>