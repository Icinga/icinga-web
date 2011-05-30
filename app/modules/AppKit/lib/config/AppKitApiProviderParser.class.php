<?php
class AppKitApiProviderParser  {
    private $jsResult;
   

    public function execute(array $directProviders) {
        $paramList = array();
        foreach($directProviders as $provider) {
            $module = $provider['module'];
    		$action = $provider['action'];
            $dom = $this->getValidatorXMLForAction($provider['module'],$provider['action']);    	
            $paramList[] = $this->getRequestParams($module,$action,$dom);
            $this->checkActionType($module,$action);
        }
        
    }

    private function getRequestParams($module,$action,AgaviXmlConfigDomDocument $dom) { 
        $validationParser = new AppKitValidatorArgumentExtractor(); 
        return  array(
            "module" => $module, 
            "action" => $action, 
            "arguments" =>  $validationParser->execute($dom)
        );
    }

    private function checkActionType($module,$action) {
        $class = $this->getActionClass($module,$action);
        $reflected = new ReflectionClass($class);
        foreach($reflected->getInterfaceNames() as $interface) {
            echo $class." has interface ".$interface;
        }
    }
    
    private function getActionClass($moduleName,$actionName) {
        $actionName = AgaviToolkit::canonicalName($actionName);
		$longActionName = str_replace('/', '_', $actionName);
		$class = $moduleName . '_' . $longActionName . 'Action';
		if(!class_exists($class)) {
			if(false !== ($file = AgaviContext::getInstance()->getController()->checkActionFile($moduleName, $actionName))) {
				require($file);
			} else {
				throw new AgaviException('Could not find file for Action "' . $actionName . '" in module "' . $moduleName . '"');
			}
			
			if(!class_exists($class, false)) {
				throw new AgaviException('Could not find Action "' . $longActionName . '" for module "' . $moduleName . '"');
			}
		}
        return $class;
    }

    
    /**
    * Fetches the Validation xml for the action/module combination and returns it as
    * an DOMDocument
    *
    * @param    string	The module name
    * @param    string	The action to get the validation xml for
    * @return    AgaviXmlConfigDomDocument
    *
    * @author    Jannis Moßhammer<jannis.mosshammer@netways.de>
    * @throws    AgaviConfigurationException 	when module or action does not exist
    */
    protected function getValidatorXMLForAction($module,$action) {
        // get Module path
        $path = AgaviToolkit::literalize('%core.module_dir%')."/".$module;

        if(!file_exists(AgaviToolkit::normalizePath($path))) {
            throw new AgaviConfigurationException("Couldn't find module ".$module);
        }

        // get Validation file
        $actionPath = str_replace(".","/",$action);
        $xml = $path."/validate/".$actionPath.".xml";

        if(!file_exists(AgaviToolkit::normalizePath($path))) {
            throw new AgaviConfigurationException("Couldn't find validation file for ".$action);
        }

        $dom = new AgaviXmlConfigDomDocument();
        $dom->load(AgaviToolKit::normalizePath($xml));

        //TODO: Validate xml
        return $dom;
    }


}
