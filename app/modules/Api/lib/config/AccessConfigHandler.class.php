<?php 



class AccessConfigHandler extends AgaviXmlConfigHandler {
    protected $document = null;
    private $parseModules = true;
    protected $xpath = null;

    private $instances = array();
    private $hosts = array();
    private $defaults = array(
        "r" => array(),
        "w" => array(),
        "rw" => array(),
        "x" => array()
    );
    private $defaultHost;
    private $importModules = true;

    const XML_NAMESPACE = 'http://icinga.org/api/config/parts/access/1.0';
        
    public function setImportModuleConfigurations($bool) {
        $this->importModules = $bool;
    }

    public function execute(AgaviXmlConfigDomDocument $document) {
        $this->document = $document;
        
        $this->setupXPath();
        $this->fetchDefaults();
        if($this->importModules)
            $this->importModuleConfigurations();

        $this->fetchHosts(); 
        $this->mapInstances();
        return $this->generate("return ".var_export(array(
            "instances" => $this->instances,
            "hosts" => $this->hosts,
            "defaults" => $this->defaults,
            "defaultHost" => $this->defaultHost
        ),true));
    }

    protected function setupXPath() {
        $this->xpath = new DOMXPath($this->document);
        $this->xpath->registerNamespace("ac",self::XML_NAMESPACE);
        $this->xpath->registerNamespace("ae","http://agavi.org/agavi/config/global/envelope/1.0");
    }

    private function importModuleConfigurations() {
        $moduleDir = AgaviToolkit::literalize("%core.module_dir%");
        $modules = scandir($moduleDir);
        foreach($modules as $folder) {
            $dir = $moduleDir;
            if($folder == ".." || $folder == "." || $folder == "Api")
                continue;
            $dir = $dir."/".$folder."/";
            
            if(!is_dir($dir) || !is_readable($dir))
                continue;
            $accessLocation = $dir."config/access.xml";
            if(file_exists($accessLocation) && is_readable($accessLocation))
                $this->importModuleXML($accessLocation); 
            
        }   
    }

    private function importModuleXML($accessLocation) {
        $config = include AgaviConfigCache::checkConfig(AgaviToolkit::expandDirectives($accessLocation));
        $this->instances = array_merge_recursive($this->instances,$config["instances"]);
        $this->defaults = array_merge_recursive($this->defaults,$config["defaults"]);
        $this->hosts = array_merge_recursive($this->hosts,$config["hosts"]);
    }

    private function fetchDefaults() {
        $defaultNodes = $this->xpath->query('//ac:defaults/node()');
        foreach($defaultNodes as $node) {
            if($node->nodeType != XML_ELEMENT_NODE)
                continue;
            $this->registerDefaults($node); 
        }
    }

    private function fetchHosts() {
        $defaultNodes = $this->xpath->query('//ac:hosts/node()');
        foreach($defaultNodes as $node) {
            if($node->nodeType != XML_ELEMENT_NODE)
                continue;
            $this->registerHost($node); 
        }
    }

    private function mapInstances() {
        $defaultNodes = $this->xpath->query('//ac:instances/node()');
        foreach($defaultNodes as $node) {
            if($node->nodeType != XML_ELEMENT_NODE)
                continue;
            $instance = $node->getAttribute("name");
            $hosts = explode(";",$node->nodeValue);
            
            foreach($hosts as $host) {
                if(!isset($this->hosts[$host]))
                    throw new AppKitException("Instance ".$instance." is mapped to unknown host ".$host);
            }
            $this->instances[$instance] = $hosts;

        }
    }

    private function registerDefaults(DOMNode $node) {
       
        foreach($node->childNodes as $child) {
            if($node->nodeType != XML_ELEMENT_NODE)
                continue;
            if($node->nodeName == 'defaultHost') {
                $this->defaultHost = $node->nodeValue; 
                continue;
            }
            if($node->nodeName != 'access')
                continue; // shouldn't happen, we have our dtd
            foreach($node->childNodes as $accessDefinition) {
                if($accessDefinition->nodeType != XML_ELEMENT_NODE)
                    continue;
  
                $this->parseAccessDefinition($accessDefinition);
            }
        }
    }
     
    private function registerHost(DOMNode $node) {
        $hostname = $node->getAttribute("name");
        if(!isset($this->hosts[$hostname])) {
            $this->hosts[$hostname] = array(
                "auth" => array(),
                "r" => array(),
                "w" => array(),
                "rw" => array(),
                "x" => array()
            );
        }
        $auth = &$this->hosts[$hostname]["auth"];
        foreach($node->childNodes as $hostinfo) {
            switch($hostinfo->nodeName) {
                case 'type':
                    $auth["type"] = $hostinfo->nodeValue;
                    break;
                case 'access':
                    // apply default rules to hosts if necessary
                    if($hostinfo->getAttribute("useDefaults") == "true")
                         $this->applyDefaultsToHost($hostname);
                    // apply host specific rules
                    if(!$hostinfo->hasChildNodes())
                        break;
                    foreach($hostinfo->childNodes as $accessNode) 
                        $this->parseAccessDefinition($accessNode,$hostname);
                    break;
                case 'ssh-config':
                    $this->applySSHConfig($hostinfo,$hostname); 
            }
        }
    }

    private function applyDefaultsToHost($hostname) {
        if(!isset($this->hosts[$hostname]))
            return;
        $host = &$this->hosts[$hostname];
        foreach($this->defaults as $type=>$value) {
            foreach($value as $symbol=>$path)
                if(!isset($host[$type][$symbol]))
                    $host[$type][$symbol] = $path;
        }
    }

    private function applySSHConfig(DOMNode $sshNode, $host) {
        $auth = &$this->hosts[$host]["auth"];
        foreach($sshNode->childNodes as $authInfo) {
            if($authInfo->nodeType != XML_ELEMENT_NODE)
                continue;
  
            switch($authInfo->nodeName) {
                case 'type':
                    $auth["method"] = $authInfo->nodeValue;
                case 'auth':
                    $this->applySSHConfig($authInfo,$host);
                    break;
                default:
                    $auth[$authInfo->nodeName] = $authInfo->nodeValue;
            }
        } 
    }

    private function parseAccessDefinition(DOMNode $accessNode,$host = null) {
        $type;
  
        switch($accessNode->nodeName) {
            case 'readwrite':
                $type = 'rw';
                break;
            case 'read':
                $type = 'r';
                break;
            case 'write':
                $type = 'w';
                break;
            case 'execute':
                $type = 'x';
                break;
            default: 
                continue;
        }
        // defaults are not additive
        if($host == null)
            $this->defaults[$type] = array();
        if(!$accessNode->hasChildNodes())
            return; 
        foreach($accessNode->childNodes as $resourceCollection) {
            if($resourceCollection->nodeName == 'files' || 
                    $resourceCollection->nodeName == 'folders')
                $this->addResource($resourceCollection,$resourceCollection->nodeName, $type,$host); 
        
        }
    }

    private function addResource(DOMNode $node,$resourceType,$accesstype, $host = null) {
       
        foreach($node->childNodes as $resource) {
            if($resource->nodeName != 'resource')
                continue;
            $symbol = $resource->getAttribute("name");
            $target = $resource->nodeValue;
            if($resourceType == "folders")
                $target = dirname($target).'/'.basename($target).'/*';
            if(!$host)
                $this->defaults[$accesstype][$symbol] = $target;
            else if(isset($this->hosts[$host]))
                $this->hosts[$host][$accesstype][$symbol] = $target;
        }

    }
}

class AccessConfigModuleHandler extends AccessConfigHandler {

    const XML_NAMESPACE = 'http://icinga.org/api/config/parts/access/module/1.0';
    public function execute(AgaviXmlConfigDomDocument $dom) {
        $this->setImportModuleConfigurations(false);
        return parent::execute($dom);
    }
    protected function setupXPath() {
        $this->xpath = new DOMXPath($this->document);
        $this->xpath->registerNamespace("ac",self::XML_NAMESPACE);
        $this->xpath->registerNamespace("ae","http://agavi.org/agavi/config/global/envelope/1.0");
    }
}
