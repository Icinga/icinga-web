<?php


class cronXMLNotFoundException extends Exception {};
class invalidXMLException extends Exception {};
class invalidCronException extends Exception {};
/**
 * Class that mus be inherited in order to write own Parsers for Cronjobs
 * It also provides a interface for accessing parser via the createParser() function
 * 
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
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
	/**
	 * Returns a Parser for the resource type $type that parses $resource
	 * 
	 * @param String $type The type of the parser (Agavi, DB, XML,...)
	 * @param mixed $resource The data to parse
	 * @param boolean $verbose
	 * 
	 * @return CronJobParser
	 */
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