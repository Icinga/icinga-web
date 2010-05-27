<?php
/**
 * Task that sets up a database via doctrine Models
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */

class dbInitializeTask extends Task {
	private $icingaPath = "/usr/local/icinga-web/";
	private $modelPath = "app/modules/AppKit/lib/database/models/";
	private $dsn;

	
	
	public function setIcingapath($path) {
		$this->icingaPath = $path;
	}
	public function setModelpath($path) {
		$this->modelPath = $path;
	}
	public function setDsn($conn) {
		$this->dsn = $conn;
	}
	
	public function init() {
		// include doctrine
		require_once($this->icingaPath."lib/doctrine/lib/Doctrine.php");
		spl_autoload_register("Doctrine::autoload");
	}
	
	public function main() {
		Doctrine_Manager::connection($this->dsn);
		try {
			Doctrine::createDatabases();
		} catch(Exception $e) {
			//..ignore, the db already exists
		} 
		Doctrine::setModelsDirectory($this->modelPath."generated/");
		Doctrine::setModelsDirectory($this->modelPath."/");
		try {
			Doctrine::createTablesFromModels(array(
				$this->modelPath."generated/",
				$this->modelPath."/"
				)
			);
		} catch(Doctrine_Exception $e) {
			throw new BuildException("Couldn't create schema - are you sure the table doesn't already exist? If so, use the update command.");
		}
		/**
		 * hehe, it's bruteforce inserting - try inserting the records 10 times or 
		 * until everything is inserted. 
		 **/ 
		$insertedData = array();
		$tries = 0;
		do {	
			$allsaved = true;
	        foreach(Doctrine::getLoadedModels() as $model) {
				$refCl = new ReflectionClass($model);
				if(!$refCl->hasMethod("getInitialData")) 
					continue;
					
				foreach($model::getInitialData() as $initData) {
					if(in_array($initData,$insertedData))
						continue;
					$record = new $model();
					
					foreach($initData as $field=>$value) {
						$record->set($field,$value);
					}
					$result = false;
					try { 
						$result = $record->trySave();
					} catch(Exception $e) {/*..ignore..*/}
					
					if($result)
						$insertedData[] &= $initData;	
					$allsaved = $allsaved && $result;
				}
			}		
			$tries++;
		} while($tries < 10 && !$allsaved);
	}
}

