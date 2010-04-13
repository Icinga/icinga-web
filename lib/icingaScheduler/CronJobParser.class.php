<?php


class cronXMLNotFoundException extends Exception {};
class invalidXMLException extends Exception {};
class invalidCronException extends Exception {};

abstract class CronJobParser {
	protected $verbose = false;
	protected $cronJobs = array();
	
	public function isVerbose() {
		return $this->verbose;
	}
	public function getCronJobs() {
		return $this->cronJobs;
	}
	
	public function setVerbose($bool) {
		$this->verbose = $bool;
	}

	public function setCronJobs(array $cronJobs) {
		$this->cronJobs = $cronJobs;
	}
	public function addCronJob(CronJobDefinition $cron) {
		$this->cronJobs[] = $cron;
	}
	
	public static function createParser($type,$resource,$verbose = false) {
		$className = "CronJobParser_Cron".$type."Parser";
		$parser = new $className($resource,$verbose);
		return $parser;
	}
	
	public function __construct($source = null, $verbose = false) {		
		if($verbose)
			$this->setVerbose($verbose);
		
		$this->parse();
		$this->extractCronJobs();

	}	


	abstract public function onExecute(CronJobDefinition $job);
	abstract protected function parse();
	abstract protected function extractCronJobs();
}