<?php

class Reporting_Provider_TreeLoaderSuccessView extends ReportingBaseView {
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);
		$this->setAttribute('_title', 'Provider.TreeLoader');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {
	    
	    $factory = $this->getContext()->getModel('JasperSoapFactory', 'Reporting', array(
	        'jasperconfig' => $rd->getParameter('jasperconfig')
	    ));
	    
	    $client = $factory->getSoapClientForWSDL(Reporting_JasperSoapFactoryModel::SERVICE_REPOSITORY);

	    $params = array (
	        'client'    => $client,
	        'parentid'  => $rd->getParameter('node')
	    );
	    
	    $filter_val = $rd->getParameter('filter', null);
	    
	    if ($filter_val) {
	        $filter = $this->getContext()->getModel('JasperTreeFilter', 'Reporting');
	        
	        if ($filter_val == 'reports') {
	            $filter->addFilter(Reporting_JasperTreeFilterModel::TYPE_DESCRIPTOR, JasperResourceDescriptor::DESCRIPTOR_TYPE, '/^folder|reportunit$/i');
	        }
	        
	        $params['filter'] = $filter;
	    }
	    
	    $tree = $this->getContext()->getModel('JasperTreeStruct', 'Reporting', $params);
	    
	    return json_encode($tree->getJsonStructure());
	}
	
	
}

?>