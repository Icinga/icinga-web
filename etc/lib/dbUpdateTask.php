<?php

require_once(dirname(__FILE__)."/doctrineTask.php");

class dbUpdateTask extends doctrineTask {
	
	
	public function main() {
		parent::main();
		$this->checkIfDBExists();
		$this->checkVersion();
		$this->updateStructure();
	}
	
	public function checkVersion() {
		$vers = Doctrine_Core::getTable("NsmDbVersion")->findBy("vers_id",1)->getFirst();
		if(!$vers instanceof NsmDbVersion) {
			echo "No version number found, proceeding anyway.\n";
			return true;
		}
		$thisVersion = NsmDbVersion::getInitialData();
		$newVersion = $thisVersion[0]["version"];
		if($vers->get("version") >= $newVersion) {
			echo "Current version is equal or higher than the version you provided, no update needed";
			throw new BuildException("No update performed");
		}
		return true;
	}
	
	public function checkIfDBExists() {
		try {
			Doctrine_Manager::connection()->connect();
		} catch(Exception $e) {
			echo "\nCritical error: DB connection failed: ".$e."\n";
			throw new BuildException("Unable to connect to database");
		}
	}
	
	public function updateStructure() {
		echo "\nUpdating DB Structure";
		$models = Doctrine::getLoadedModels();
		print_r($models);
		
	}
	
}