<?php
define("XML_CRON",dirname(__FILE__)."/cronDefinitions/crons.xml");
class CronJobParser_CronXMLParser extends CronJobParser {
	protected $cronsXMLPath = XML_CRON;
	protected $xmlResource = null;

	
	public function getCronsXMLPath() {
		return $this->cronsXMLPath;
	}
	public function getXMLResource() {
		return $this->xmlResource;
	}


	public function setCronsXMLPath($path) {
		$this->cronsXMLPath = $path;
	}
	public function setXMLResource(SimpleXMLElement $xml) {
		$this->xmlResource = $xml;
	}
	
	public function __construct($xml = null, $verbose = false, $logFile = null) {
		if($xml) 
			$this->setCronsXMLPath($xml);			
		parent::__construct($xml,$verbose,$logFile);
	}	
	
	protected function parse() {
		$xmlFile = $this->getCronsXMLPath();
		if(!file_exists($xmlFile))
			throw new cronXMLNotFoundException("Cron file doesn't exist at ".$xmlFile);
		
		libxml_use_internal_errors(true);
		$sXML = simplexml_load_file($xmlFile);
		if(!$sXML)
			throw new invalidXMLException("Invalid Cron XML - ".$xmlFile);
		$this->setXMLResource($sXML);
		
	}
	
	public function onExecute(CronJobDefinition $job) {
	
	}
	
	protected function extractCronJobs() {
		$xml = $this->getXMLResource();
		$crons = $xml->xpath("//Cron");
		$counter = 0;
		foreach($crons as $cronXML) {
			$counter++;
			$cronDefinition = CronJobDefinition::fromXML($cronXML,$this);
			if(!$cronDefinition) {
				$this->log("Cron nr ".$counter." is invalid",true);
				continue;
			}
			$this->addCronJob($cronDefinition);
		}
	}
	

}
