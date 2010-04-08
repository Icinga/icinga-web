<?php
require_once "phing/Task.php";
require_once "manifestStore.php";

class ManifestAgaviConfiguratorTask extends Task {
    private $file = null;
	private $xmlObject = null;
	
    public function setFile($str) {
        $this->file = $str;
    }

    public function setXMLObject(DOMDocument $xml) {
    	$this->xmlObject = $xml;
    }
	public function getFile() {
		return $this->file;
	}

    public function getXMLObject() {
    	return $this->xmlObject;
    }
    
    public function init() {
		
    }
	
    public function main() {
    	$file = $this->getFile();
    	$DOM = new DOMDocument("1.0","UTF-8");
    	$DOM->load($file);
		$this->setXMLObject($DOM);
		
		$manifest = $this->getXMLObject();
		$manifest->preserveWhiteSpace = false;
		$manifestSearcher = new DOMXPath($manifest);
		$cfgFiles = $manifestSearcher->query("//Config/Files/*");
		foreach($cfgFiles as $file) {
			$file = $file->nodeName;
			$this->setConfigVars($file);
		}
		
		$this->registerRoutes();
    	$this->addTranslations();
    }
	
	protected function setConfigVars($file) {
		$manifest = $this->getXMLObject();
		$manifestSearcher = new DOMXPath($manifest);
		
		$configPath = $this->project->getUserProperty("PATH_Icinga")."/app/config/".strtolower($file).".xml";
		$configDOM = new DOMDocument("1.0");
		$configDOM->preserveWhiteSpace = false;
		$configDOM->load($configPath);
		
		$xpathSearcher = new DOMXPath($configDOM);
		$xpathSearcher->registerNamespace("default","http://agavi.org/agavi/1.0/config");
		$xpathSearcher->registerNamespace("xi","http://www.w3.org/2001/XInclude");
		
		$settings = $manifestSearcher->query("//Config/Files/".$file."/*");
		foreach($settings as $config) {
			
			if($config->nodeType != XML_ELEMENT_NODE)
				continue;
			// fetch node Values
			$attr = $config->getAttribute("name");
			$type = $config->getAttribute("type");
			$pname = $config->getAttribute("paramName");
			$textnode = $config->getAttribute("textnode");
			$value = $config->nodeValue;
			
			$entries = $xpathSearcher->query("//default:setting[@name='".$attr."']");
			if($entries->length < 1) {
				$setting = $configDOM->createElement("setting");
				$setting->setAttribute("name",$attr);
				// check whether to create only  a text node or parameter node
				if($textnode) {
					$setting->nodeValue = $value;
				} else {
					$setting->appendChild($this->createParameter($configDOM,$value,$pname));
	
				} 
				echo "New config-node added: ".$attr." (".$value.")\n";				
				$configDOM->lastChild->appendChild($setting);
			} else {
				$setting = $entries->item(0);
				// if it's a textnode and we're not allowed to overwrite it, no changes are possible
				if($textnode && !$type == "overwrite")
					continue;
				if($textnode && $type == "overwrite")
					$setting->nodeValue = $value;	
			
				// if its a parameter node, check if this parameter already exists
				if(!$textnode) {
					if(!$pname) {
						$setting->appendChild($this->createParameter($configDOM,$value));
					} else {
						$params = $xpathSearcher->query("//default:parameter[@name=".$pname."]");
						if($params->length<1)
							$setting->appendChild($this->createParameter($configDOM,$value));
						else if($type == "overwrite")
							$params->item(0)->nodeValue = $value;
					}
				}
			}
		}
		// finally add am xi:include 
		$pluginName = $this->project->getUserProperty("PLUGIN_Name");
		$includes = $xpathSearcher->query("//xi:include[@href='plugins/".$pluginName.".xml']")->item(0);
		if(!$includes) {
			$include = $configDOM->createElementNS("http://www.w3.org/2001/XInclude","xi:include");
			$include->setAttribute("href","plugins/".$pluginName.".xml");
			$configDOM->lastChild->appendChild($include);
		}
	
		
		$configDOM->formatOutput = true;
		$configDOM->save($configPath);

		$this->reformat($configPath);
	}
	
	protected function createParameter(DOMDocument $configDOM,$value,$pname = null) {
		$parameter = $configDOM->createElement("parameter");
		if($pname)
			$parameter->setAttribute("name",$pname);
		$parameter->nodeValue = $value;
		return $parameter;
	}
	
	protected function registerRoutes() {
		$routes = new DOMDocument("1.0");
		$routes->preserveWhiteSpace = false;
		$routes->load("src/routes.xml");
		
		$configPath = $this->project->getUserProperty("PATH_Icinga")."/app/config/routing.xml";
		$configDOM = new DOMDocument("1.0");
		$configDOM->preserveWhiteSpace = false;
		$configDOM->load($configPath);
		
		$xpathSearcher = new DOMXPath($routes);
		$xpathSearcher->registerNamespace("default","http://agavi.org/agavi/1.0/config");

		$routingSearcher = new DOMXPath($configDOM);
		$routingSearcher->registerNamespace("default","http://agavi.org/agavi/1.0/config");
		
		
		$configurations = $configDOM->getElementsByTagName("configurations")->item(0);
		$routeDefinitions = $routes->getElementsByTagName("RouteDefinition");
		foreach($routeDefinitions as $route)	{
			$routeName = $route->getAttribute("fullname");
			$context = $route->getAttribute("context");
			
			foreach($route->childNodes as $child) {
				if($child->nodeName != "route")
					continue;
				$route = $child;
				break;
			}			
			if($routingSearcher->query("//default:configuration[@context='".$context."']/default:routes/default:route[@name='".$routeName."']")->item(0)) {
				echo("Route ".$routeName." already exists - skipping\n");
				continue;
			}
			$contextConfigs = $routingSearcher->query("//default:configuration[@context='".$context."']/default:routes");
			$config = null;
			if($contextConfigs->length < 1) {
				$config = $this->createContextConfig($configDOM,$context);

			} else {
				$config = $contextConfigs->item(0);
			}
			
			$config->appendChild($configDOM->importNode($route,true));
		}
			
		$configDOM->formatOutput = true;
		$configDOM->save($configPath);
		$this->reformat($configPath);

		
	}
	
	protected function addTranslations() {
		$translation = new DOMDocument("1.0");
		$translation->preserveWhiteSpace = false;
		$translation->load("src/translations.xml");
		
		$configPath = $this->project->getUserProperty("PATH_Icinga")."/app/config/translation.xml";
		$configDOM = new DOMDocument("1.0");
		$configDOM->preserveWhiteSpace = false;
		$configDOM->load($configPath);
		
		$xpathSearcher = new DOMXPath($translation);
		$xpathSearcher->registerNamespace("default","http://agavi.org/agavi/config/parts/translation/1.0");

	
		$translationSearcher = new DOMXPath($configDOM);
		$translationSearcher->registerNamespace("default","http://agavi.org/agavi/config/parts/translation/1.0");
		// import locales
		$locales = $xpathSearcher->query("//default:available_locales/*");
		$localeNode = $translationSearcher->query("//default:available_locales")->item(0);
		foreach($locales as $locale) {
			$id = $locale->getAttribute("identifier");
			//check if node already exists 
			if($translationSearcher->query("//default:available_locale[@identifier='".$id."']")->item(0)) {
				echo "\nLocale ".$id." already exists, skipping\n";
				continue;
			}
			$localeNode->appendChild($configDOM->importNode($locale,true));
		}
		
		//import domains
		$translators = $xpathSearcher->query("//default:translators/*");
		$translatorNode = $translationSearcher->query("//default:translators")->item(0);
		foreach($translators as $translator) {
			$id = $translator->getAttribute("domain");
			//check if node already exists 
			if($translationSearcher->query("//default:translator[@domain='".$id."']")->item(0)) {
				echo "\nTranslation domain ".$id." already exists, skipping\n";
				continue;
			}
			$translatorNode->appendChild($configDOM->importNode($translator,true));
		}
					
		$configDOM->formatOutput = true;
		$configDOM->save($configPath);
		$this->reformat($configPath);
	}
	
	protected function reformat($configPath) {
		// Reformat the xml (triple whitespaces to tab)
		$file = file_get_contents($configPath);
		$file = preg_replace("/\t/","   ",$file);
		$file = preg_replace("/ {3}/","\t",$file);
		file_put_contents($configPath,$file);
	}
	
	protected function createContextConfig(DOMDocument $dom,$context) {
		$elem = $dom->createElement("configuration");
		$elem->setAttribute("context",$context);
		$dom->getElementsByTagName("configurations")->item(0)->appendChild($elem);
		$routes = $dom->createElement("routes");
		$elem->appendChild($routes);
		return $routes;
	}
}