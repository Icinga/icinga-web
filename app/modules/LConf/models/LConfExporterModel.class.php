<?php
/**
 * Exporter task for creating lconf configs from the webinterface
 * 
 * @author jmosshammer
 *
 */
class LConfExporterErrorException extends AgaviException {};

class LConf_LConfExporterModel extends IcingaLConfBaseModel
{
	protected $lconfConsole = null;
	protected $instanceConsole = null;
	protected $statusCounter = 0;
	protected $ldapClient = null;
	protected $filterSatellites = false;
	protected $prefix = "";
	public function getStatus() {
		return $this->status[$statusCounter];
	}

	public function getConsole($instance) {
		if(!$this->lconfConsole)
			$this->lconfConsole = AgaviContext::getInstance()->getModel('Console.ConsoleInterface',"Api",array("host"=>$instance));
		return $this->lconfConsole;
	}

    protected function log($msg) {
        $log = $this->getContext()->getLoggerManager()->getLogger("icinga-debug");
        $log->log(new AgaviLoggerMessage("LConf export: ".$msg));
    }

    public function getExportBase(LConf_LDAPConnectionModel $ldap_config) {
        $cfg = AgaviConfig::get('modules.lconf.lconfExport');
        if(isset($cfg['exportDN'])) {
            
            return $cfg['exportDN'].",".$ldap_config->getBaseDN();
        } return null;
        
    }

	public function exportConfig(LConf_LDAPConnectionModel $ldap_config,$satellites = array()) {
		//$satellites = $this->fetchExportSatellites($ldap_config);	

        $ctx = $this->getContext();
		$this->log("Export started for connection ".$ldap_config->getConnectionName());
        $lconfExportInstance = AgaviConfig::get('modules.lconf.lconfExport.lconfConsoleInstance');
		$this->prefix = AgaviConfig::get('modules.lconf.prefix');
		$this->log("Setting up consolehandler for ".$lconfExportInstance);
        $console = $this->getConsole($lconfExportInstance);
		$this->tm = $ctx->getTranslationManager();
		
        $exportCmd =  $ctx->getModel(
			'Console.ConsoleCommand',
			"Api",
			array(
				"command" => "lconf_export",
				"connection" => $console, 
				"arguments" => $satellites
			)
			
		);
        $this->log("Executing export");
		$console->exec($exportCmd);
        $this->log("Export finished");
		if($exportCmd->getReturnCode() != 0) { 
            $err = $this->getCommandError($exportCmd);
            $this->log("Export failed ".$err);
			throw new LConfExporterErrorException($err);
		} else {
            $this->log("Export suceeded, updating export time");
			$this->updateExportTime($ldap_config,$satellites);
            $this->log("Export time updated");

            return $this->parseSuccessfulOutput($exportCmd);
		}
	}
	
	public function getChangedSatellites(LConf_LDAPConnectionModel $ldap_config) {
        $this->log("Fetching modified satellites");
		$satellites = $this->fetchExportSatellites($ldap_config);
        
		$this->filterSatellites = true;
		$satellites_new = $this->fetchExportSatellites($ldap_config);
		$this->log(count($satellites)." Satellites found");
        return array("Available"=>$satellites, "Updated" => $satellites_new);
	}
	
	protected function updateExportTime($conn,$satellites = array()) {
		
		if(empty($satellites))
			return true;

		$ctx = $this->getContext();
		$elem = $this->fetchExportSatellites($conn,true);
		
		foreach($elem as $key=>$strucObj)  {  		
			if(!is_numeric($key) || !isset($strucObj["satellite_name"]))
				continue;
			// only update exported satellite times
			if(count(array_intersect($strucObj["satellite_name"],$satellites)) <= 0)
				continue;
			
			if(!isset($strucObj["description"])) {
				$strucObj["description"] = array();
			}
		
			$toDelete = array();
			
			foreach($strucObj["description"] as $descKey => $desc)  {	
				if(!is_numeric($descKey))
					continue;
						
				if(preg_match('/Last export by lconf for icinga-web: /',$desc) > 0) {		
					$toDelete[] = "description_".$descKey;	
				}	
			}	
			
			$this->ldapClient->removeNodeProperty($strucObj["dn"],$toDelete);
			$this->ldapClient->addNodeProperty($strucObj["dn"],
				array(
					"property"=>"description",
					"value" => 'Last export by lconf for icinga-web: '.date('c')
				)
			);
		}
	}


	protected function getCommandError(Api_Console_ConsoleCommandModel $exportCmd) {
        
		switch(intval($exportCmd->getReturnCode())) {
			case 126: //execution error
				return $this->tm->_("Cannot execute exporter, please check your permissions");
				break;
			case 127: //command not found
				return $this->tm->_("Exporter not found - check your configuration");
				break;
			default:
				return $this->getErrorFromCommandOutput($exportCmd);
			
		}
	}

	protected function parseSuccessfulOutput(Api_Console_ConsoleCommandModel $exportCmd) {
		$output = utf8_encode($exportCmd->getOutput());
		$matches = array();
		$result = array();
		preg_match_all("/[\t ]*?Checked[\t ]*?(?P<number>\d+)[\t ]*?(?P<category>[ \w]+)\./",$output,$matches);
		for($i=0;$i<count($matches["number"]);$i++) {
			$result[] = array(
				"type" => trim($matches["category"][$i]),
				"count" => intval($matches["number"][$i])
			);
		}
		return $result;
	}

	protected function getErrorFromCommandOutput(Api_Console_ConsoleCommandModel $exportCmd) {
		$output = $exportCmd->getOutput();
			
		if(($err = $this->checkForLDAPErrors($output)) != false)
			return $err;
		if(($err = $this->checkForIcingaErrors($output)) != false)
			return $err;	
		return $this->tm->_("An unknown error occured, check your server logs");
	}

	protected function checkForLDAPErrors($output) {
		if(preg_match("/.*Export config from LDAP\nOK - No errors/",$output))
			return false;
		if(preg_match("/.*Can't connect to ldap server/",$output)) 
			return $this->tm->_("Exporter couldn't connect to ldap db. Please check your config.");	
	}

	protected function checkForIcingaErrors($output) {
		$errors = array();
		$errStr = "";
		if(preg_match_all("/Error: (.*)/",$output,$errors)) {
			foreach($errors[0] as $error) 
				$errStr .= $error."\n";
			return $this->tm->_("Config verification failed: \n".$errStr);	
		}
		return false;
	}

	protected function fetchExportSatellites(LConf_LDAPConnectionModel $ldap_config,$asObject = false) {

		$ctx = $this->getContext();
		$filterGroup = $ctx->getModel('LDAPFilterGroup','LConf');
		$objectClassFilter =  $ctx->getModel("LDAPFilter","LConf",array("objectclass","*",false,"exact"));
		$filter = $ctx->getModel('LDAPFilter','LConf',array(
			'description','LCONF->EXPORT->CLUSTER',null,'contains'
		));
		$filterGroup->addFilter($objectClassFilter);
		$filterGroup->addFilter($filter);

        $client = $ctx->getModel('LDAPClient','LConf',array($ldap_config));
		$client->connect();
		$this->ldapClient = $client;
        
        $this->log("Searching for satellites underneath ".$this->getExportBase($ldap_config));
		$entries = $client->searchEntries($filterGroup,$this->getExportBase($ldap_config),array('dn','description','objectclass','modifytimestamp'));
		$satellites = array();
        if($entries == null)
            $entries = array();
        $this->log(count($entries)." Satellites found");
        if($this->filterSatellites) {
			$entries = $this->removeUnchangedSatellites($entries);	
		}

		foreach($entries as $val=>&$cluster) {
			if(!is_numeric($val))
				continue;	
			if(!$this->isStructuralObject($cluster))
				continue;
	
			foreach($cluster['description'] as $key=>$val) {
				if(!is_numeric($key))
					continue;
				$matches = array();
	
				preg_match_all('/^LCONF->EXPORT->CLUSTER[\t ]*?=[\t ]*?(?P<satellite>[\w ]+?[ \t]*?$)/i',$val,$matches);
			
				if(is_array($matches['satellite'])) {
					foreach($matches['satellite'] as &$s) {
						$s = trim($s);	
					}
					$satellites = array_merge($satellites,$matches['satellite']);
					
					if($asObject) {
						if(!isset($cluster["satellite_name"]))
							$cluster["satellite_name"] = array();

						$cluster["satellite_name"] = array_merge($cluster["satellite_name"],$matches['satellite']);
					}
				}
			}
		}
		
		if($asObject)
			return $entries;
		
		return $satellites;
	}

	protected function removeUnchangedSatellites(array $entries) {
		$satellites = array();

		foreach($entries as $val=>$cluster) {
		
			$ts = $cluster["modifytimestamp"][0];
			if(!is_numeric($val))
				continue;

			if(!$this->isStructuralObject($cluster))
				continue;
		
			$client = $this->ldapClient;
			$curDir = ($client->listDN($cluster["dn"],true,true));;
	
			foreach($curDir as $nr=>$dir) {		
				if($this->recursiveTimestampCheck($dir,$ts)) {
					$satellites[] =$cluster; 
					break;
				}
			}
		}
		return $satellites;
	}

	protected function recursiveTimestampCheck($dir,$ts) {
		
		if($dir["modifytimestamp"][0] > $ts) {
		
			return true;
		}
		$subDir = ($this->ldapClient->listDN($dir["dn"],true,true));;
	
		if(!is_array($subDir))
			return false;
		foreach($subDir as $val=>$sD) {
			if(!is_numeric($val))
				continue;
			if($this->recursiveTimestampCheck($sD,$ts))
				return true;
		}
		return false;
	}

	protected function isStructuralObject($cluster) { 
		
		/**
		*	determine if the objectclass is *structuralobject (objectclasses are internally stored 
		*	as numeric identifiers, so wildcarded filters don't work)
		**/	
		if(!isset($cluster['objectclass'])) {
			return false;	
		}
		foreach($cluster['objectclass'] as $key=>$val) {	
			if(!is_numeric($key))
				continue;
				
			if(preg_match('/\w*StructuralObject/i',$val)) {	
				return true;	
			}
		}
		return false;
	}



}
