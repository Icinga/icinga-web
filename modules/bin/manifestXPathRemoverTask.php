<?php

require_once "manifestBaseClass.php";
/**
 * Removes a node found via $removePath in config file $target described by $cfgPath
 * The removePath can contain a %VALUE% token that will be replaced to fit the result of $cfgPath
 * 
 *  
 * Ex. : 
 * The manifest has the following nodes:
 * <Config>
 * 		<Translator>
 * 			<Domain>Test</Domain>
 * 		</Translator>
 * </Config>

 * The following command will remove a <translator domain="Test"> node in translator.xml:
 *  
 * <manifestXPathRemover target="translation.xml" file="${manifest}" ns="default" uri="http://agavi.org/agavi/config/parts/translation/1.0" cfgPath="//Config/Translator/Domain" 
 *								removePath="//default:translator[@domain='%VALUE%']"/>
 *	
 * 
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class ManifestXPathRemoverTask extends manifestBaseClass {
	protected $target;
	protected $removePath;
	protected $cfgPath;
	protected $ns;
	protected $uri;
	
    public function main() {
		parent::main();
    	$this->removeRoutes();
	}
	
	/**
	 * The name of the config file 
	 * @param String $target
	 */
	public function setTarget($target) 	{
		$this->target = $target;
	}
	
	/**
	 * The xpath query describing how to find nodes to delete in the agavi
	 * config file
	 * 
	 * @param String $path
	 */
	public function setRemovePath($path) {
		$this->removePath = $path;
	}
	
	/**
	 * A xpath query that will be performed on the manifest and describes
	 * which nodes to delete
	 *  
	 * @param String $cfg
	 */
	public function setCfgPath($cfg) {
		$this->cfgPath = $cfg;
	}
	
	/**
	 * The namespace to use 
	 * @param String $ns
	 */
	public function setNs($ns) {
		$this->ns = $ns;
	}
	/**
	 * The uri of the namespace
	 * @param String $uri
	 */
	public function setUri($uri) {
		$this->uri = $uri;
	} 
	
	/**
	 * Iterates through all nodes returned by cfgPath, searches them in $file and removes
	 * them if found
	 * 
	 */
	public function removeRoutes() {
		$manifest = $this->getXMLObject();
		$configPath = $this->project->getUserProperty("PATH_Icinga")."/app/config/".$this->target;
		$configDOM = new DOMDocument("1.0");
		$configDOM->preserveWhiteSpaces = false;
		$configDOM->load($configPath);
		
		$configSearcher = new DOMXPath($configDOM);
		$configSearcher->registerNamespace($this->ns,$this->uri);
		$root = $this->getManifestNode();
		$counter = 0;
		foreach($root as $node) {
			$query = $this->buildQuery($node);
			$routeToRemove = $configSearcher->query($query)->item(0);
			if(!$routeToRemove) {
				echo ("\nCouldn't find a config note that was marked to be deleted - please check if you need to remove it manually!\n");
				continue;
			}
			$counter++;
			$routeToRemove->parentNode->removeChild($routeToRemove);				
		}
		echo $counter." config nodes removed in ".$this->target."\n";
		$configDOM->formatOutput = true;
		$configDOM->save($configPath);
		$this->reformat($configPath);
	}
	
	/**
	 * Performs the cfgPath xpath query on the manifest node and returns it's result
	 * 
	 * @return DOMNodeList The result of the query
	 */
	protected function getManifestNode() {
		$manifest = $this->getXMLObject();
		return $manifest->xpath($this->cfgPath);
	}
	
	/**
	 * builds the xpath query that is used to search for nodes in the agavi config files
	 * 
	 * @param SimpleXMLElement $node The ḿanifest node that describes the delete operation
	 * @return String
	 */
	protected function buildQuery(SimpleXMLElement $node) {
		$rawPath = $this->removePath;
		foreach($node->attributes() as $attr=>$value) {
			$rawPath = str_replace("%".$attr."%",$value,$rawPath);
		}
		$rawPath = str_replace("%VALUE%",(String) $node,$rawPath);
		return $rawPath;
	}
	
	protected function reformat($configPath) {
		// Reformat the xml (triple whitespaces to tab)
		$file = file_get_contents($configPath);
		$file = preg_replace("/\t/","   ",$file);
		$file = preg_replace("/ {3}/","\t",$file);
		file_put_contents($configPath,$file);
	}
	
}