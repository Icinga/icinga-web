<?php
class AppKitApiProviderParser  {
    private $jsResult;
    private $descriptorHandler = array(
                                     "AppKitExtJSDataStoreWriter"
                                 );

    public function execute(array $directProviders) {
        $descriptors = array();
        /*
                foreach($directProviders as $provider) {
                    $module = $provider['module'];
            	    $action = $provider['action'];
                    $dom = $this->getValidatorXMLForAction($provider['module'],$provider['action']);
                    $paramList = $this->getRequestParams($module,$action,$dom);
                    $descriptor = $this->getJSDescriptor($module,$action);
                    if($descriptor) {
                        $this->enhanceDescriptorByValidationParams($descriptor,$paramList);
                        $descriptor["module"] = $module;
                        $descriptor["action"] = $action;
                        $descriptors[$module."_".$action] = $descriptor;
                    }
                }
                foreach($this->descriptorHandler as $handler) {
                    $c = new $handler();
                    $c->write($descriptors,AgaviConfig::get('org.icinga.appkit.exthandler.jsfile'));
                }*/
    }


    private function getRequestParams($module,$action,AgaviXmlConfigDomDocument $dom) {
        $validationParser = new AppKitValidatorArgumentExtractor();
        $paramList = $validationParser->execute($dom);
        return $paramList;
    }

    private function getJSDescriptor($module,$action) {
        $class = $this->getActionClass($module,$action);
        $reflected = new ReflectionClass($class);
        foreach($reflected->getInterfaceNames() as $interface) {
            if ($interface == 'IAppKitDataStoreProviderAction') {
                return $this->fetchDataStoreDescriptor($class);
            }
        }
    }

    private function getActionClass($moduleName,$actionName) {
        $actionName = AgaviToolkit::canonicalName($actionName);
        $longActionName = str_replace('/', '_', $actionName);
        $class = $moduleName . '_' . $longActionName . 'Action';

        if (!class_exists($class)) {
            if (false !== ($file = AppKitAgaviUtil::getAgaviControllerInstance()->checkActionFile($moduleName, $actionName))) {
                require($file);
            } else {
                throw new AgaviException('Could not find file for Action "' . $actionName . '" in module "' . $moduleName . '"');
            }

            if (!class_exists($class, false)) {
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

        if (!file_exists(AgaviToolkit::normalizePath($path))) {
            throw new AgaviConfigurationException("Couldn't find module ".$module);
        }

        // get Validation file
        $actionPath = str_replace(".","/",$action);
        $xml = $path."/validate/".$actionPath.".xml";

        if (!file_exists(AgaviToolkit::normalizePath($path))) {
            throw new AgaviConfigurationException("Couldn't find validation file for ".$action);
        }

        $dom = new AgaviXmlConfigDomDocument();
        $dom->load(AgaviToolKit::normalizePath($xml));

        //TODO: Validate xml
        return $dom;
    }

    private function fetchDataStoreDescriptor($class) {
        $obj = new $class();
        $jsDescriptor = array();

        /**
        * There is no controller available at this time, so we have to handle modelloading by ourselves
        **/
        $modelDefinitionList = $obj->getDataStoreModel();

        if (!is_array($modelDefinitionList)) {
            return array();
        }

        if (isset($modelDefinitionList["model"])) { // allow objects with a flatter object structure
            $modelDefinitionList = array($modelDefinitionList);
        }

        $jsDescriptor = array();
        foreach($modelDefinitionList as $modelDefinition) {
            $modelName = $modelDefinition["model"];
            $moduleName = $modelDefinition["module"];
            $descArr = array();
            // fetch JS Descriptor
            $dataStore = AgaviContext::getInstance()->getModel($modelName,$moduleName,array("request" => new AgaviRequestDataHolder()));
            $dataStore->initialize(AgaviContext::getInstance(),array("request" => new AgaviRequestDataHolder()));
            foreach($dataStore->getModifiers() as $modifier) {
                $descArr[] = $modifier->__getJSDescriptor();
            }
            $jsDescriptor[$modelDefinition["id"]] = $descArr;
        }
        return  $jsDescriptor;

    }

    private $validatorParamsToSubmit = array(
                                           "pattern","min","max","type","format"

                                       );
    /**
    * Takes the parameters defined by the descriptor modifers (e.g. DataStoreFilterModifier) and checks them
    * against the actions validation xml. Arguments that can't be set, because there is no definition in the
    * validation file will be removed, other arguments will be expanded by their type and additional parameters
    * (see @validatorParamsToSubmit)
    *
    * @param    Array   The array of modification descriptors, as returned by @see: IDataStoreModifier::__getJSDescriptor()
    * @param    Array   The parameters defined in the actions validation xml, listed in an array
    *
    * @author   Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    private function enhanceDescriptorByValidationParams(array &$descriptor = array() ,array $paramList = array()) {
        if (empty($paramList)) {
            $descriptor = array();
        }

        foreach($descriptor as &$modifier) {
            $toRemove = array();

            if (!isset($modifier["params"])) {
                continue;
            }

            if (!is_array($modifier["params"])) {
                $modifier["params"] = array($modifier["params"]);
            }

            foreach($modifier["params"] as $key=>&$param) {

                $found = $this->expandParamFromValidation($key,$param,$paramList);

                if (!$found) {
                    $toRemove[] = $key;
                }
            }
            foreach($toRemove as $key) {
                unset($modifier["params"][$key]);
            }
        }
    }

    /**
    * Helper function for @see enhanceDescriptorByValidationParams which checks a descriptor param against the parameter
    * list from the validation xml and adds additional fields. Returns false if the parameter is not defined in the validator
    *
    * @param    String      The key of this parameter
    * @param    String      An reference to the param which should be expanded or removed
    * @param    Array       Parameter list as defined
    * @returns  Boolean     Whether the parameter was found in the validation xml
    *
    * @author   Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    private function expandParamFromValidation($key,&$param,array $paramList) {
        $found = false;
        foreach($paramList as $validatorParam) {
            if ($validatorParam["argument"] == $param) {
                $found = true;
                $param = array("argument"=>$param,"type"=>$validatorParam["type"]);
                foreach($this->validatorParamsToSubmit as $additional) {
                    if (isset($validatorParam["parameters"][$additional])) {
                        $param[$additional] = $validatorParam["parameters"][$additional];
                    }
                }
            }
        }
        return $found;
    }
}
