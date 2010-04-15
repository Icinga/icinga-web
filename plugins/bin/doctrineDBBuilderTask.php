<?php
require_once "phing/Task.php";

class doctrineDBBuilderTask extends Task {
	protected $models;
	protected $action;
	protected $ini;
	
	public function init() {
		
	}
	
	public function main() {
		$this->checkForDoctrine();
		$action = $this->action;
		switch($action) {
			case "delete":
				$this->removeTablesForModels();
				break;
			case "create":
				$this->buildDBFromModels();
				break;
			default:
				throw new BuildException("Unknown db action ".$action."!");
		}	
			 
	}
	
	protected function checkForDoctrine() {
		$icinga = $this->project->getUserProperty("PATH_Icinga");
		$doctrinePath = $icinga."/".$this->project->getUserProperty("PATH_Doctrine");
		if(!file_exists($doctrinePath."/Doctrine.php"))
			throw new BuildException("Doctrine.php not found at ".$doctrinePath."Doctrine.php");
		
		require_once($doctrinePath."/Doctrine.php");
		spl_autoload_register("Doctrine::autoload");
//		spl_autoload_register(array('Doctrine_Core', 'modelsAutoload'));
		
		$iniData = parse_ini_file($this->ini);
		if(empty($iniData))
			throw new BuildException("Couldn't read db.ini");
		$dsn = $iniData["dbtype"]."://".$iniData["dbuser"].":".$iniData["dbpass"]."@".$iniData["host"].":".$iniData["port"]."/".$iniData["dbname"];
		Doctrine_Manager::connection($dsn);
	}
	
	protected function removeTablesForModels() {
		$tablesToDelete = file_get_contents($this->models);
		Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh()->query(
			"SET FOREIGN_KEY_CHECKS=0;
			 DROP TABLE ".$tablesToDelete.";
			 SET FOREIGN_KEY_CHECKS=1;
			 ");
		echo "\n Dropping tables $tablesToDelete \n";
	}
	
	public function buildDBFromModels() {	
		$icinga = $this->project->getUserProperty("PATH_Icinga");
		$modelPath = $icinga."/app/modules/".$this->project->getUserProperty("PLUGIN_Name")."/lib/";
		Doctrine::createTablesFromModels(array($this->models.'/generated',$this->models));
		Doctrine::loadModels($this->models);
		$tables = Doctrine::getLoadedModels();
		$tableList = array();
		foreach($tables as $table) {
			$tableList[] = Doctrine::getTable($table)->getTableName();	
		}

		file_put_contents($modelPath."/.models.cfg",implode(",",$tableList));
	}
	
	public function setAction($action) {
		$this->action = $action;
	}
	
	public function setModels($models) {
		$this->models = $models;
	}
	
	public function setIni($ini)	{
		$this->ini = $ini;
	}
}

?>
