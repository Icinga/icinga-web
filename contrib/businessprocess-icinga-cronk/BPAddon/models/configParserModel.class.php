<?php

class BPAddon_configParserModel extends BPAddonBaseModel {
    private $processed =array();
    private $ordered = array();
    private $cfgParts = array();
    
    public function jsonToConfigString($json) {
        $object = json_decode($json, true);
        if (!$object)
            throw new AgaviException("Invalid json provided");

        $config = ($this->parseProcessesFromArray($object));
        return $config;
    }

    public function configFileToJson($file) {
        $bpConfig = AgaviConfig::get("modules.bpaddon.bp");
        $abs_file = $bpConfig["paths"]["configTarget"] . "/" . $file . ".conf";
        $ctx = $this->getContext();

        $parser = $ctx->getModel('bpCfgInterpreter', "BPAddon", array($abs_file));
        $bps = $parser->parse(true);
        return json_encode($bps);
    }

    public function jsonToConfigFile($json, $file) {
        $object = json_decode($json, true);
        if (!$object)
            throw new AgaviException("Invalid json provided");
        $bpConfig = AgaviConfig::get("modules.bpaddon.bp");
        $config = ($this->parseProcessesFromArray($object));
        if (in_array($file, $bpConfig["blacklist"]) || in_array($file . ".cfg", $bpConfig["blacklist"]))
            throw new AppKitException("This file name is not allowed");
        $path = $bpConfig["paths"]["configTarget"];
        $file = $path . "/" . $file . ".conf";
        // Check writeability
        if (file_exists($file)) {
            if (!is_writeable($file))
                throw new AppKitException("File already exists and not writeable");
        } else {
            if (!is_writeable($path))
                throw new AppKitException("Can't write to config path " . $path);
        }
        if ($this->getConsistencyErrors($config)) {
            throw new AppKitException("Config check failed, please check your syntax");
        }
        file_put_contents($file, $config);
    }

    protected function parseProcessesFromArray(array $object) {
        

        foreach ($object["children"] as $process) {
            $bp = $this->getContext()->getModel("businessProcess", "BPAddon", array($process));
            $this->cfgParts[] = array("obj" => $bp, "str" => $bp->__toConfig());
        }
        $this->orderResultSet();

        $config = $this->getConfigHeader();
        $config .= implode(chr(10), $this->ordered);
        return $config;
    }
    
    private function getProcessObject($name) {
        foreach($this->cfgParts as $part) {
            $bpObj = $part["obj"];
            if($bpObj->getName() == $name)
                return $bpObj;
            $child = $bpObj->getChildProcess($name);
            if($child !== null)
                return $child;
        }
        return null;
    }

    private function resolveOrderConflicts($bpObj, array &$inPath =array()) {
        if($bpObj->isStub())
            $bpObj = $this->getProcessObject($bpObj->getName());
        foreach($bpObj->getSubProcesses() as $subProcess) {
            
            if(in_array($subProcess->getName(),$inPath))
                    throw new AppKitException("Recursive loop detected in ".$bpObj->getName()." : ".$subProcess->getName()." causes an infinite recursion");
            if(!in_array($subProcess->getName(),$this->processed)) {
                $inPath[] = $subProcess->getName();
                $this->resolveOrderConflicts($subProcess,  $inPath);
                array_pop($inPath);
            }
        }
        
        if(!in_array($bpObj->getName(),$this->processed)) {
            $this->processed[] = $bpObj->getName();            
            $this->ordered[] = $bpObj->__toConfig(true);
        } 

    }
    
    protected function orderResultSet() {
        
        foreach ($this->cfgParts as $pos => $bp) {
            if (!is_array($bp))
                continue;

            $this->resolveOrderConflicts($bp["obj"]);
            
        }

    }

    protected function getConfigHeader() {
        return
                "#######################################################################
#	Automatically generated config file for Business Process Addon
#	Generated on " . date('r') . " 
#
#######################################################################
";
    }

    protected function getBPFromCfgArray($name, array $cfg) {
        foreach ($cfg as $bp) {
            if (!is_array($bp))
                continue;

            if ($bp["obj"]->getName() == $name) {
                return $bp;
            }
        }
        return null;
    }

    public function getConsistencyErrors($cfg) {
        $bp = AgaviConfig::get("modules.bpaddon.bp");
        if (!file_exists($bp["paths"]["bin"] . "/" . $bp["commands"]["checkConsistency"]))
            return "Couldn't check consistency: Invalid path provided in config";

        $tmp_dir = sys_get_temp_dir();
        $file = tempnam($tmp_dir, "bp");
        file_put_contents($file, $cfg);
        $ret = 0;
        // Call the check command and save 
        $systemResult = array();
        exec($bp["paths"]["bin"] . "/" . $bp["commands"]["checkConsistency"] . " " . $file, $systemResult, $ret);
        $systemResult = implode("\n", $systemResult);

        unlink($file);
        if ($ret)
            return $systemResult;
        return false;
    }

    public function listConfigFiles() {
        $bpConfig = AgaviConfig::get("modules.bpaddon.bp");
        $path = $bpConfig["paths"]["configTarget"];
        if (!file_exists($path) || !is_readable($path))
            throw new AppKitException("Configuration Error: Config path does not exist or is not readable");
        $files = scandir($path);
        $fileListing = array();
        foreach ($files as $file) {
            if (!preg_match("/.conf$/", $file))
                continue;

            $fileListing[] = array(
                "filename" => preg_replace("/.conf$/", "", $file),
                "created" => filemtime($path . "/" . $file),
                "last_modified" => filectime($path . "/" . $file)
            );
        }
        return $fileListing;
    }

    public function removeConfigFile($file) {
        $bpConfig = AgaviConfig::get("modules.bpaddon.bp");
        $abs_file = $bpConfig["paths"]["configTarget"] . "/" . $file . ".conf";
        $ctx = $this->getContext();
        unlink($abs_file);
    }

}

?>
