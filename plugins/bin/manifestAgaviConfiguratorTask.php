<?php
require_once "phing/Task.php";
require_once "manifestStore.php";

/**
 * Task that configurates the agavi config files via DOM
 * 
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class ManifestAgaviConfiguratorTask extends Task {
	/**
	 * The manifest file to use
	 * @var String
	 */
	private $file = null;
	
	/**
	 * The manifest DOM representation
	 * @var DOMDocument
	 */
	private $xmlObject = null;
	
	/**
	 * Sets the manifest filename
	 * @param String $str
	 */
    public function setFile($str) {
        $this->file = $str;
    }

    /**
     * Sets the Manifest DOM Representation
     * @param $xml
     */
    public function setXMLObject(DOMDocument $xml) {
    	$this->xmlObject = $xml;
    }

    /**
     * returns the manifest filename
     * @return String Filename of manifest.xml
     */
    public function getFile() {
		return $this->file;
	}

	/**
	 * Returns the manifest DOMDocument
	 * @return DOMDocument The manifest.xml DOM
	 */
    public function getXMLObject() {
    	return $this->xmlObject;
    }
    
    public function init() {
		
    }

    /**
     * Loads the manifest and processes all config vars
     * 
     */
    public function main() {
    	$file = $this->getFile();
    	$DOM = new DOMDocument("1.0","UTF-8");
    	$DOM->load($file);
		$this->setXMLObject($DOM);
		
		$manifest = $this->getXMLObject();
		$manifest->preserveWhiteSpace = false;
		$manifestSearcher = new DOMXPath($manifest);
		/**
		 * Process Config->Files section
		 */
		$cfgFiles = $manifestSearcher->query("//Config/Files/*");
		foreach($cfgFiles as $file) {
			$file = $file->nodeName;
			$this->setConfigVars($file);
		}
	
		// set routes and translations
		$this->registerRoutes();
    	$this->addTranslations();
    }
	
    /**
     * Function that sets config parameters in %icinga/app/config/$file.xml
     * 
     * A XML Property description can have the following parameters:
     * 		type : 		Either 'append', 'overwrite'.
     * 					Determines wheter to overwrite the node if it already exists,
     * 					append it.
     * 		name :  	The name of the setting
     * 		pname:  	The name of the parameter (optional)
     * 					If no pname is given, a nameless parameter will be appended
     * 		textnode: 	True if a setting should not contain a parameter, but only text
     * 		asXML :		Just append the the content of this node. 
     * 		fromConfig:	Export this property from the original config file and insert it 
     *					on install
     * 
     * @param String $file The Config-Filename
     */
	protected function setConfigVars($file) {
		if(!$file)
			return null;
		
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
			if($config->getAttribute("fromConfig")) {
				$this->injectSettingFromXML($config,$configDOM,$file);
				continue;
			}
			
			$attr = $config->getAttribute("name");
			$type = $config->getAttribute("type");
			$pname = $config->getAttribute("paramName");
			$textnode = $config->getAttribute("textnode");
			$isXML = $config->getAttribute("asXML");
			$value = $config->nodeValue;
			
			$entries = $xpathSearcher->query("//default:setting[@name='".$attr."']");
			if($entries->length < 1) {
				$setting = $configDOM->createElement("setting");
				$setting->setAttribute("name",$attr);
				// check whether to create only  a text node or parameter node
				if($textnode) {
					$setting->nodeValue = $value;
				} else if($isXML) {
					$setting->appendChild($this->getXMLChild($configDOM,$config));
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
				 	if($isXML) {
						$setting->appendChild($this->getXMLChild($configDOM,$config));
					} else if(!$pname) {
						$setting->appendChild($this->createParameter($configDOM,$value));
					} else {
						$params = $xpathSearcher->query("//default:parameter[@name='".$pname."']");
						if($params->length<1)
							$setting->appendChild($this->createParameter($configDOM,$value));
						else if($type == "overwrite")
							$params->item(0)->nodeValue = $value;
					}
				}
			}
		}
		// Save and reformat
		$configDOM->formatOutput = true;
		$configDOM->save($configPath);

		$this->reformat($configPath);
	}
	
	
	/**
	 * Called when a node contains fromConfig. Injects an exported config setting $config 
	 * from $filename into $cfgDOM
	 * 
	 * @param DOMElement $config The config node to process
	 * @param DOMDocument $cfgDOM The agavi-config file DOM
	 * @param String $filename the filename of the xml file containing exported settings
	 *
	 */
	protected function injectSettingFromXML($config,DOMDocument $cfgDOM,$filename) { 
		$name = $config->getAttribute("name");
		$pname = $config->getAttribute("pname");
		$type = $config->getAttribute("type");
		$xml = new DOMDocument("1.0","UTF-8");
		$xml->load("./src/".$filename.".xml");

		$xpathSearcher = new DOMXPath($xml);
		$xpathSearcher->registerNamespace("default","http://agavi.org/agavi/1.0/config");
		
		$manifestSearcher = new DOMXPath($cfgDOM);
		$manifestSearcher->registerNamespace("default","http://agavi.org/agavi/1.0/config");
		
		// fetch extracted node from plugin folder		
		if($pname)	{
			$query = "//default:setting[@name='".$name."']//default:parameter[@name='".$pname."']";
		} else {
			$query = "//default:setting[@name='".$name."']/*";
		}
		
		$nodeToInsert = $xpathSearcher->query($query)->item(0);
		if(!$nodeToInsert) {
			echo "Setting for ".$name." not found, skipping \n";
			return null;
		}
		
		// get position in agavi config file
		if($pname && $type == "overwrite") { 
			$query = "//default:setting[@name='".$name."']//default:parameter[@name='".$pname."']";
			echo $query;
			$node = $manifestSearcher->query($query)->item(0);
			if($node) {
				$parent = $node->parentNode;
				$parent->removeChild($node);			
			}
		} 
		
		if($type == "overwrite" || $pname) {
			$query = "//default:setting[@name='".$name."']";
		} else {
			$query = "/*";
		}
		
		$node = $manifestSearcher->query($query)->item(0);
		if(!$node && !$type == "overwrite") {
			echo "Couldn't insert setting ".$name." skipping \n";
			return null;
		}
		
		if($type == "overwrite" && !$pname) {
			if($node->hasChildNodes())  {
				foreach($node->childNodes as $child)
					$node->removeChild($child);
			}			
		}
		$node->appendChild($cfgDOM->importNode($nodeToInsert,true));
	}
	
	/**
	 * Returns an xml childnode that is not a comment or textnode
	 * @param DOMDocument $dom 
	 * @param DOMElement $node
	 */
	protected function getXMLChild(DOMDocument $dom, DOMElement $node) {
		foreach($node->childNodes as $childNode) {
			if($childNode->nodeType == XML_COMMENT_NODE || $childNode->nodeType == XML_TEXT_NODE)
				continue;
			return $dom->importNode($childNode,true);
		}		
	}
	
	/**
	 * Adds a <xi:include> tag to the icinga.xml which points to the plugin include
	 * 
	 */
	protected function addSettingsInclude() {
			
		$configPath = $this->project->getUserProperty("PATH_Icinga")."/app/config/icinga.xml";
		$configDOM = new DOMDocument("1.0");
		$configDOM->preserveWhiteSpace = false;
		$configDOM->load($configPath);
		
		$xpathSearcher = new DOMXPath($configDOM);
		$xpathSearcher->registerNamespace("default","http://agavi.org/agavi/1.0/config");
		$xpathSearcher->registerNamespace("xi","http://www.w3.org/2001/XInclude");
		
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
	
	/**
	 * Creates a parameter $name  with $value 
	 *
	 * @param DOMDocument $configDOM
	 * @param String $value The value of the parameter
	 * @param String $pname The name of the parameter (optional)
	 */
	protected function createParameter(DOMDocument $configDOM,$value,$pname = null) {
		$parameter = $configDOM->createElement("parameter");
		if($pname)
			$parameter->setAttribute("name",$pname);
		$parameter->nodeValue = $value;
		return $parameter;
	}
	
	/**
	 * Registers the routes exported to src/routes.xml (if any) in the 
	 * agavi routing.xml
	 *
	 */
	protected function registerRoutes() {
		$routes = new DOMDocument("1.0");
		$routes->preserveWhiteSpace = false;
		if(!file_exists("src/routes.xml"))
			return null;
			
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
			// check if the context already exists
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

	/**
	 * Adds translation domains and locales to the agavi translations.xml
	 * 
	 */
	protected function addTranslations() {
		$translation = new DOMDocument("1.0");
		$translation->preserveWhiteSpace = false;
		if(!file_exists("src/translations.xml"))
			return null;
			
		$translation->load("src/translations.xml");
		
		$configPath = $this->project->getUserProperty("PATH_Icinga")."/app/config/translation.xml";
		$configDOM = new DOMDocument("1.0");
		$configDOM->preserveWhiteSpace = false;
		$configDOM->load($configPath);
		
		$xpathSearcher = new DOMXPath($translation);
		$xpathSearcher->registerNamespace("default","http://agavi.org/agavi/config/parts/translation/1.0");
		$xpathSearcher->registerNamespace("ae","http://agavi.org/agavi/config/global/envelope/1.0");
	
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
		$translatorNode = $translationSearcher->query("//default:translator[@domain='icinga']//ae:parameter[@name='text_domains']")->item(0);
		foreach($translators as $translator) {
			$id = $translator->getAttribute("domain");
			//check if node already exists 
			if($translationSearcher->query("//default:translator[@domain='icinga']//ae:parameter[@name='".$id."']")->item(0)) {
				echo "\nTranslation domain ".$id." already exists, skipping\n";
				continue;
			}
			$translatorNode->appendChild($configDOM->importNode($translator,true));
		}
					
		$configDOM->formatOutput = true;
		$configDOM->save($configPath);
		$this->reformat($configPath);
	}
	
	/**
	 * Reformats an xml, so it looks nice again
	 * 
	 * @param String $configPath The path to the xml file
	 */
	protected function reformat($configPath) {
		// Reformat the xml (triple whitespaces to tab)
		$file = file_get_contents($configPath);
		$file = preg_replace("/\t/","   ",$file);
		$file = preg_replace("/ {3}/","\t",$file);
		file_put_contents($configPath,$file);
	}
	
	/**
	 * Create a context config in the routes.xml
	 * 
	 * @param DOMDocument $dom
	 * @param String $context the new context 
	 * @return Returns the new DOM node
	 */
	protected function createContextConfig(DOMDocument $dom,$context) {
		$elem = $dom->createElement("configuration");
		$elem->setAttribute("context",$context);
		$dom->getElementsByTagName("configurations")->item(0)->appendChild($elem);
		$routes = $dom->createElement("routes");
		$elem->appendChild($routes);
		return $routes;
	}
}
