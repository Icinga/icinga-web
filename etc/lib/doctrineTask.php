<?php
/**
 * Abstract base class for all tasks accessing the icinga-web doctrine implementation
 * 
 */

class doctrineTask extends Task {

	const DB_NAME = 'icinga_web';

	protected $icingaPath = "";
	protected $modelPath = "app/modules/AppKit/lib/database/models/";
	protected $dsn;
	protected $action;
	protected $targetTable;
	protected $dropOnFinish = false;
	public function setAction($action) {
		$this->action = $action;
	}
	
	public function setIcingapath($path) {
		$this->icingaPath = $path;
	}
	public function setModelpath($path) {
		$this->modelPath = $path;
	}
	public function setTargettable($table) {
		$this->targetTable = $table;
	}
	
	public function setDsn($conn) {
		$this->dsn = $conn;
	}
	
	public function init() {
		// include doctrine
		require_once($this->icingaPath."lib/doctrine/lib/Doctrine.php");
		spl_autoload_register("Doctrine::autoload");
	}
	public function dropOnFinish($bool = false) {
		$this->dropOnFinish = true;
	}
	public function main() {
		Doctrine_Manager::connection($this->dsn, self::DB_NAME);
		Doctrine::loadModels($this->modelPath."/generated/");
		Doctrine::setModelsDirectory($this->modelPath."/");
		if($this->action == 'dropDB') {
			$this->dropDB();
		} else if($this->action="truncateTable" && $this->targetTable) {
			Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh()->query(
				"truncate table ".$this->targetTable.";"
			);
		} 
		
	}
	
	public function dropDB() {
		Doctrine::dropDatabases(self::DB_NAME);
	}
	
}
