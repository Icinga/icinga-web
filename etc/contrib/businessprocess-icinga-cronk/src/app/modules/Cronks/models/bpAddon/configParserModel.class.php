<?php

class Cronks_bpAddon_configParserModel extends CronksBaseModel
{
	public function jsonToConfigString($json) {
		$object = json_decode($json,true);
		if(!$object)
			throw new AgaviException("Invalid json provided");
		
		$config = ($this->parseProcessesFromArray($object));
		return $config;
	}
	
	public function configFileToJson($file) {
		$bpConfig = AgaviConfig::get("modules.cronks.bp");
		$abs_file = $bpConfig["paths"]["configTarget"]."/".$file.".conf";
		$ctx = $this->getContext();
		
		$parser = $ctx->getModel('bpAddon.bpCfgInterpreter',"Cronks",array($abs_file));
		$bps = $parser->parse(true);
		return json_encode($bps);
	}
	
	public function jsonToConfigFile($json,$file) {
		$object = json_decode($json,true);
		if(!$object)
			throw new AgaviException("Invalid json provided");
		$bpConfig = AgaviConfig::get("modules.cronks.bp");
		$config = ($this->parseProcessesFromArray($object));
		if(in_array($file,$bpConfig["blacklist"]) || in_array($file.".cfg",$bpConfig["blacklist"]))
			throw new AppKitException("This file name is not allowed");
		$path = $bpConfig["paths"]["configTarget"];
		$file = $path."/".$file.".conf";
		// Check writeability
		if(file_exists($file)) {
			if(!is_writeable($file))
				throw new AppKitException("File already exists and not writeable");
		} else {
			if(!is_writeable($path))
				throw new AppKitException("Can't write to config path ".$path);	
		}
		
		file_put_contents($file,$config);
	}
	protected function parseProcessesFromArray(array $object) {
		$cfgParts = array();
		
		foreach($object["children"] as $process) {
			$bp = $this->getContext()->getModel("bpAddon.businessProcess","Cronks",array($process));	
			$cfgParts[] = array("obj" => $bp, "str" => $bp->__toConfig());
		}
		
		$cfgString = $this->orderResultSet($cfgParts);
		
		$config = $this->getConfigHeader();
		$config .= implode(chr(10),$cfgString);
		return $config;
	} 
	
	protected function orderResultSet(array $cfgParts) {
		$orderChanged = false;
		$ringRef = array ();
		$i=0;
		
		do {
			$i++;
			$orderChanged = false;		
			$newOrder = array();
			$processed = array();
					
			foreach($cfgParts as $pos=>$name) {
				if(!is_array($name))
					continue;

				$bp = $name["obj"];
				
				foreach($bp->getSubProcesses() as $subProcess) {
					
					if(!$subProcess->hasCompleteConfiguration() 
							&& !in_array($subProcess->getName(),$processed)) {
						
						$process = $this->getBPFromCfgArray($subProcess->getName(),$cfgParts);
						$newOrder[] = $process;
						$processed[] = $subProcess->getName();
						$orderChanged = true;
					}				
				}
				$newOrder[] = $name;
				$processed[] = $bp->getName();
			}
			
			$cfgParts = $newOrder;
		} while($orderChanged && $i<2);
		$newOrder = array();
		foreach($cfgParts as $part) {
			$newOrder[] = $part["str"];
		}
		return $newOrder;
	}
	
	protected function getConfigHeader() {
		return 
"#######################################################################
#	Automatically generated config file for Business Process Addon
#	Generated on ".date('r')." 
#
#######################################################################
";
	}
	
	protected function getBPFromCfgArray($name,array $cfg) {
		foreach($cfg as $bp) {
			if(!is_array($bp))
				continue;

			if($bp["obj"]->getName() == $name) {
				return $bp;
			}
		}
		return null;
	}
	
	public function getConsistencyErrors($cfg) {
		$bp = AgaviConfig::get("modules.cronks.bp");
		if(!file_exists($bp["paths"]["bin"]."/".$bp["commands"]["checkConsistency"]))
			return "Couldn't check consistency: Invalid path provided in config";
		
		$tmp_dir = sys_get_temp_dir();
		$file = tempnam($tmp_dir,"bp");
		file_put_contents($file,$cfg);
		$ret = 0;
		// Call the check command and save 
		ob_start();
		system($bp["paths"]["bin"]."/".$bp["commands"]["checkConsistency"]." ".$file,$ret);
		$systemResult = ob_get_clean();

		unlink($file);
		if($ret)
			return $systemResult;
		return false;
	}
	
	public function listConfigFiles() {
		$bpConfig = AgaviConfig::get("modules.cronks.bp");
		$path = $bpConfig["paths"]["configTarget"];
		if(!file_exists($path) || !is_readable($path))
			throw new AppKitException("Configuration Error: Config path does not exist or is not readable");
		$files = scandir($path);
		$fileListing = array();
		foreach($files as $file) {
			if(!preg_match("/.conf$/",$file))
				continue;
			
			$fileListing[] = array(
				"filename"=>preg_replace("/.conf$/","",$file),
				"created" => filemtime($path."/".$file),
				"last_modified" => filectime($path."/".$file)
			);		
			
		}
		return $fileListing;
	}
	
	public function removeConfigFile($file) {
		$bpConfig = AgaviConfig::get("modules.cronks.bp");
		$abs_file = $bpConfig["paths"]["configTarget"]."/".$file.".conf";
		$ctx = $this->getContext();
		unlink($abs_file);
	}
}

?>