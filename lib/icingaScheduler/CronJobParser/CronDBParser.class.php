<?php

class CronJobParser_CronDBParser extends CronJobParser {
	protected $model = null;
	protected $agavi = null;
	protected $dbResult = array();
	protected $fields = array('*');
	
	public function getModel()	{
		return $this->model;
	}
	public function getAgavi() {
		return $this->agavi;
	}
	public function getDbResult() {
		return $this->dbResult;
	}
	public function getFields() {
		return $this->fields;
	}
	
	public function setModel($model) {
		$this->model = $model;
	}
	public function setAgavi(CronAgaviAdapter $agavi)  {
		$this->agavi = $agavi;
	}
	public function setDbResult(array $result) {
		$this->dbResult = $result;
	}
	public function setFields(array $fields) {
		$this->fields = $fields;
	}
	
	public function __construct($model = null, $verbose = false, $logFile = null) {
		if($model) 
			$this->setModel($model);			
		$this->setAgavi(CronAgaviAdapter::getInstance());

		parent::__construct($model,$verbose,$logFile);
	}	
		
	protected function parse() {
		$agavi = $this->getAgavi();
		$query = Doctrine_Query::create()
			->select(implode(",",$this->getFields()))
			->from($this->getModel());
		$result = $query->fetchArray();
		if($result)
			$this->setDbResult($result);
	}
	
	protected function extractCronJobs() {
		$result = $this->getDbResult();
		foreach($result as $index=>$jobArray) {
			$job = $this->reformatJobArray($jobArray,$index);
			$this->addCronJob(CronJobDefinition::fromArray($job,$this));
		}
	}	
	
	public function onExecute(CronJobDefinition $job) {
		$index = explode("_",$job->getName());
		$jobEntry = $this->getDbResult($index[count($index)-1]);
		$modelName = $this->getModel();
		$model = new $modelName();
		foreach($jobEntry[0] as $field=>$value) {
			$model->set($field,$value);
		}
	
		$model->set("lastrun",time());
		$model->replace();
	}
	
	protected function reformatJobArray(array $job,$index) {
		$job["name"] = $job["name"]."_".$index;
		$interval = $job["interval_s"];
		$weekdays = $job["days"];
		$job["interval"] = array();
		$job["interval"]["days"] = Math.floor($interval/(24*3600));
		$interval %= 24*3600;
		$job["interval"]["hours"] =  Math.floor($interval/3600);
		$interval %= 3600;
		$job["interval"]["minutes"] =  Math.floor($interval/60);
		$job["weekdays"] = array();
		// extract days from binary representation
		for($i=0;$i<7;$i++) {
			if($weekdays & pow(2,$i))	
				$job["weekdays"][] = $i+1;
		}		
		$args = explode(";",$job["args"]);
		$job["args"] = array();
		foreach($args as $argument) {
			$argSplit = explode("=",$argument);
			$job["args"][$argSplit[0]] = $argSplit[1];
		}

		return $job;
	}
}