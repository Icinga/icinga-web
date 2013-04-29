<?php

/**
 * Parser for the agavi schedules.xml file
 * 
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class CronJobParser_CronAgaviParser extends CronJobParser {
	protected $agavi = null;
	protected $resource = null;	
	
	public function getAgavi() {
		return $this->agavi;
	}
	public function getResource() {
		return $this->resource;
	}
	public function setResource(array $res) {
		$this->resource = $res;
	}
	public function setAgavi(CronAgaviAdapter $agavi)  {
		$this->agavi = $agavi;
	}
	
	public function __construct($model = null, $verbose = false, $logFile = null) {
		$this->setAgavi(CronAgaviAdapter::getInstance());

		parent::__construct($model,$verbose,$logFile);
	}

	/**
	 * reads the schedule.xml from the CronAgaviParser
	 */
	public function parse() {
		$agavi = $this->getAgavi();
		$cfg = $agavi->getConfigVar("org.icinga.schedules");
        if(!is_array($cfg))
            $cfg = array();
		$this->setResource($cfg);
	}
	public function onExecute(CronJobDefinition $job) {}
	
	/**
	 * Creates CronJobDefinition classes from registered schedules
	 * 
	 */
	public function extractCronJobs() {
		$resources = $this->getResource();
		if(empty($resources))
			return true;
			
		foreach($resources as $name=>$resource) {
			$resource["name"] = $name;
			$resource["interval"] = array(
				"days" => $resource["days"],
				"hours" => $resource["hours"],
				"minutes" => $resource["minutes"]			
			);
			$resource["weekdays"] = str_split($resource["weekdays"]);
			$this->addCronJob(CronJobDefinition::fromArray($resource,$this));
		}
		
	}
}