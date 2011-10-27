<?php

class Api_ApiCommandInfoSuccessView extends IcingaApiBaseView {
    /**
     * @var Api_Commands_CommandInfoModel
     */
    private $model = null;
    
    public function initialize(AgaviExecutionContainer $container) {
        parent::initialize($container);
        $this->model = $container->getContext()->getModel('Commands.CommandInfo', 'Api');
    }
    
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);
		
		$this->setAttribute('_title', 'ApiCommandInfo');
	}
	
	public function executeXml(AgaviRequestDataHolder $rd) {
	    $commands = $this->model->getInfo($rd->getParameter('command', null));
	    
	    $dom = new DOMDocument('1.0', 'utf-8');
	    $root = $dom->createElement('results');
	    $dom->appendChild($root);
	    $this->xml2Array($commands, $root, $dom);
	    
	    return $dom->saveXML();
	}
	
	private function xml2Array(array $array, DOMElement $root, DOMDocument $dom) {
	    foreach ($array as $key=>$value) {
	        if (is_array($value)) {
	            $sub = $dom->createElement($key);
	            $this->xml2Array($value, $sub, $dom);
	            $root->appendChild($sub);
	        } else {
	            $root->appendChild($dom->createElement($key, $value));
	            
	        }
	    }
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {
	    return json_encode(array(
	        'success' => true,
	        'results' => $this->model->getInfo($rd->getParameter('command', null))
	    ));
	}
}