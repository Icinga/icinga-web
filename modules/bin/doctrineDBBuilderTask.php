<?php
require_once "phing/Task.php";
/**
 * Task to build or remvoe doctrine Models
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class doctrineDBBuilderTask extends Task {
	protected $models;
	protected $action;
	protected $ini;
	static protected $AppKitPath = null;
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
	/**
	 * Loads Doctrine and sets up the autoloader
	 * 
	 * @throws BuildException on error
	 */
	protected function checkForDoctrine() {
		$icinga = $this->project->getUserProperty("PATH_Icinga");
		$doctrinePath = $icinga."/".$this->project->getUserProperty("PATH_Doctrine");
		if(!file_exists($doctrinePath."/Doctrine.php"))
			throw new BuildException("Doctrine.php not found at ".$doctrinePath."Doctrine.php");
		// setup autoloader
		require_once($doctrinePath."/Doctrine.php");
		spl_autoload_register("Doctrine::autoload");
		
		$iniData = parse_ini_file($this->ini);
		if(empty($iniData))
			throw new BuildException("Couldn't read db.ini");
		$dsn = $iniData["dbtype"]."://".$iniData["dbuser"].":".$iniData["dbpass"]."@".$iniData["host"].":".$iniData["port"]."/".$iniData["dbname"];
		Doctrine_Manager::connection($dsn);
	}
	
	/**
	 * Drops all tables described by the loaded models
	 * 
	 */
	protected function removeTablesForModels() {
		$tablesToDelete = file_get_contents($this->models);
		Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh()->query(
			"SET FOREIGN_KEY_CHECKS=0;
			 DROP TABLE ".$tablesToDelete.";
			 SET FOREIGN_KEY_CHECKS=1;
			 ");
		echo "\n Dropping tables $tablesToDelete \n";
	}
	
	/**
	 * Rebuilds a db as described by the doctrine models
	 *
	 */
	public function buildDBFromModels() {	
		$icinga = $this->project->getUserProperty("PATH_Icinga");
		$modelPath = $icinga."/app/modules/".$this->project->getUserProperty("MODULE_Name")."/lib/";
		
		Doctrine::loadModels($this->models);
		$tables = Doctrine::getLoadedModels();
		$tableList = array();
		foreach($tables as $table) {
			$tableList[] = Doctrine::getTable($table)->getTableName();	
		}

		$appKitPath = $this->project->getUserProperty("PATH_AppKit");

		Doctrine::loadModels($icinga."/".$appKitPath."database/models/generated");
		Doctrine::loadModels($icinga."/".$appKitPath."database/models");
		Doctrine::createTablesFromModels(array($this->models.'/generated',$this->models));
	
		file_put_contents($modelPath."/.models.cfg",implode(",",$tableList));
	}
	
	/**
	 * Sets the action for this task
	 * 
	 * @param String $action The action to perform (create or delete)
	 */
	public function setAction($action) {
		$this->action = $action;
	}
	
	/**
	 * Sets where to search for doctrine models
	 * 
	 * @param String $models the path where the db-models are
	 */
	public function setModels($models) {
		$this->models = $models;
	}
	
	/**
	 * Sets the ini file that describes the database settings
	 * @param String $ini The db.ini to load
	 */
	public function setIni($ini)	{
		$this->ini = $ini;
	}
}

?>
