<?php

class BPAddon_businessProcessModel extends BPAddonBaseModel {

    protected static $availableProcesses = array();
    protected $name;
    protected $longName = "";
    protected $type = "AND";
    protected $template = "";
    protected $services = array();
    protected $status = "";
    protected $subProcesses = array();
    protected $minCount = 0;
    protected $priority = 0;
    protected $cfgSet = false;

    public function getName() {
        return $this->name;
    }

    public function getLongName() {
        return $this->longName;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getType() {
        return $this->type;
    }

    public function getTemplate() {
        return $this->template;
    }

    public function getServices() {
        return $this->services;
    }

    public function hasService($name) {
        return isset($this->services[$name]);
    }

    public function getSubProcesses() {
        return $this->subProcesses;
    }

    public function hasSubProcess($name) {
        return isset($this->subProcesses[$name]);
    }

    public function getMinCount() {
        return $this->minCount;
    }

    public function getPriority() {
        return $this->priority;
    }

    public function hasCompleteConfiguration() {
        return $this->cfgSet;
    }

    public function setName($name) {
        $this->name = trim(str_replace(":","",$name));
    }

    public function setLongName($name) {
        $this->longName = $name;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function setTemplate($template) {
        $this->template = $template;
    }

    public function addSubProcess($process) {
        if ($process instanceof BPAddon_businessProcessModel)
            $this->subProcesses[$process->getName()] = $process;
        else
            $this->subProcesses[$process] = $process;
    }

    public function addService(BPAddon_serviceModel $service) {
        if (!$this->hasService($service->getConfigName()))
            $this->services[$service->getConfigName()] = $service;
    }

    public function setMinCount($count) {
        $this->minCount = $count;
    }

    public function setPriority($prio) {
        $this->priority = $prio;
    }

    public function getChildProcess($name) {
        foreach($this->subProcesses as $subProcess) {
            if($subProcess->getName() == $name && $subProcess->isStub() == false)
                return $subProcess;
            if(($p = $subProcess->getChildProcess($name)) !== null)
                return $p;
        }
        return null;
    }
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);

        if (!empty($parameters[0])) {
            $this->__fromConfig($parameters[0]);
        }
    }

    protected function __fromConfig(array $params) {
        if (isset($params["bpName"]))
            $this->setName($params["bpName"]);
        if (isset($params["bpLongName"]))
            $this->setLongName($params["bpLongName"]);
        if (isset($params["bpStatus"]))
            $this->setStatus($params["bpStatus"]);
        if (isset($params["bpTemplate"]))
            $this->setTemplate($params["bpTemplate"]);
        if (isset($params["type"]))
            $this->setType($params["type"]);
        if (isset($params["min"]))
            $this->setMinCount($params["min"]);
        if (isset($params["prio"]))
            $this->setPriority($params["prio"]);

        if (isset($params["children"]))
            if (!empty($params["children"]))
                $this->parseChildren($params["children"]);
        // if this is not an alias, mark the node as already processed
        if (!isset($params["isAlias"]) && !$this->hasCompleteConfiguration()) {
            $this->cfgSet = true;
            self::$availableProcesses["bpName"] = $this;
        }
    }

    protected function parseChildren(array $children) {
        $ctx = $this->getContext();

        foreach ($children as $child) {
            if (isset($child["service"]))
                $this->addService($ctx->getModel('service', 'BPAddon', array($child)));
            if (isset($child["bpName"])) {
                $this->addSubProcess($ctx->getModel('businessProcess', 'BPAddon', array($child)));
            }
        }
    }

    public function __toArray() {
        $obj = array(
            "bpName" => trim($this->getName()),
            "bpLongName" => $this->getLongName(),
            "bpStatus" => $this->getStatus(),
            "bpTemplate" => $this->getTemplate(),
            "type" => $this->getType(),
            "min" => $this->getMinCount(),
            "prio" => $this->getPriority(),
            "children" => array()
        );

        foreach ($this->getServices() as $service)
            $obj["children"][] = $service->__toArray();
        foreach ($this->getSubProcesses() as $process) {
            if ($process instanceof BPAddon_businessProcessModel) {
                $obj["children"][] = $process->__toArray();
            } else {
                $obj["children"][] = array(
                    "isAlias" => true,
                    "bpName" => trim($process)
                );
            }
        }

        return $obj;
    }

    /**
     * Definition of config parts
     */
    private function cfgDeclaration() {
        $str = $this->getName() . " = ";
        return $str;
    }
    
    public function isStub() {
        return (count($this->subProcesses)+count($this->services) == 0); 
    }

    private function cfgFilterDefinition() {
        $str = "";
        if ($this->getType() == "MIN")
            $str.= $this->getMinCount() . " of: ";
        $glueString = " | ";
        $services = array();
        switch ($this->getType()) {
            case 'MIN':
                $glueString = " + ";
                break;
            case 'AND':
                $glueString = " & ";
                break;
            case 'OR':
                $glueString = " | ";
                break;
        }
        foreach ($this->getServices() as $service)
            $services[] = $service->__toConfig();
        foreach ($this->getSubProcesses() as $bp)
            $services[] = $bp->getName();

        return $str .= implode($glueString, $services);
    }

    private function cfgDisplay() {
        $str = "display " . $this->getPriority() . ";" . $this->getName() . ";" . $this->getLongName();
        return $str;
    }

    private function cfgExternal() {
        $str = "";
        if ($this->getStatus())
            $str = 'external_info ' . $this->getName() . ';echo "' . $this->getStatus() . '"';
        return $str;
    }

    private function cfgTemplate() {
        $str = "";
        if ($this->getTemplate())
            $str = 'template ' . $this->getName() . ';' . $this->getTemplate();
        return $str;
    }

    public function __toConfig($noSub = false) {
        $string = "";
        if(!$noSub)
            foreach ($this->subProcesses as $sub) {
                if ($sub->hasCompleteConfiguration()) {
                    $string.= $sub->__toConfig();
                }
         }
        // Write down the process information
        $string .= <<<PROCESS
		
#
#  Definition of BP : {$this->getName()}
#  Automatically generated by the icinga business process cronk 
#  
{$this->cfgDeclaration()}{$this->cfgFilterDefinition()}
{$this->cfgDisplay()}
{$this->cfgExternal()}
{$this->cfgTemplate()}
#  EOF {$this->getName()}
PROCESS;
        return $string;
    }

}
