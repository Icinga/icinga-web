<?php
require_once "phing/Task.php";

class sqlRunnerTask extends Task {
	protected $files;
	protected $ini;
	
	public function init() {
		
	}	
	
	public function main() {
		$this->checkForDoctrine();
		$this->processSQL();
	}
	  
	protected function checkForDoctrine() {
		$icinga = $this->project->getUserProperty("PATH_Icinga");
		$doctrinePath = $icinga."/".$this->project->getUserProperty("PATH_Doctrine");
		if(!file_exists($doctrinePath."/Doctrine.php"))
			throw new BuildException("Doctrine.php not found at ".$doctrinePath."Doctrine.php");
		
		require_once($doctrinePath."/Doctrine.php");
		spl_autoload_register("Doctrine::autoload");
	}
	
	protected function processSQL() {
		$iniData = parse_ini_file($this->ini);
		if(empty($iniData))
			throw new BuildException("Couldn't read db.ini");
		$dsn = $iniData["dbtype"]."://".$iniData["dbuser"].":".$iniData["dbpass"]."@".$iniData["host"].":".$iniData["port"]."/".$iniData["dbname"];
		Doctrine_Manager::connection($dsn);
		Doctrine_Manager::getInstance()->getCurrentConnection()->beginTransaction();
		$doctrine = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
		$files = scandir($this->files);
		foreach($files as $filename) {
			if(substr($fileName,-3,3) == 'sql') {
				$sql = file_get_contents($filename);
				$doctrine->query($sql);
			}
		}
		Doctrine_Manager::getInstance()->getCurrentConnection()->commit();
	}
	
	public function setFiles($files) {
		$this->files = $files;
	}
	
	public function setIni($ini)	{
		$this->ini = $ini;
	}
}
