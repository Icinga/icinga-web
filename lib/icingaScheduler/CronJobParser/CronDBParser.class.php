<?php

/**
 * Reads Jobs from a database
 * Uses the connection described in agavis database.xml
 * 
 * A database must have the following fields to be parseable
 * | Field            | Type           | Description
 * | interval_s       | int(11)        | The interval of the job (in seconds)
 * | name             | varchar(255)   | The name of the job
 * | lastrun          | int(11)        | Timestamp of the last execution
 * | suspended        | tinyint(4)     | Boolean indicating if this entry is suspended
 * | days             | tinyint(4)     | Binary representation of the days (1 : Mo, 2: Tue, 4: Wed, ..)
 * | starttime        | varchar(5)     | hh:mm String describing the start time
 * | endtime          | varchar(5)     | hh:mm String describing the end time
 * | result           | varchar(255)   | The result of the last exection, not used currently
 * | path             | text           | The path where class is found
 * | class            | varchar(255)   | The class to execute
 * | args             | text           | List of arguments to provide (arg1=value1;arg2=value2..=
 * | forkable         | tinyint(4)     | Should an own process should be started? (Not supported yet)

 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class CronJobParser_CronDBParser extends CronJobParser {
	protected $model = null;
	protected $agavi = null;
	protected $dbResult = array();
	protected $fields = array('*');
	
	/**
	 * Returns the doctrine database modelname 
	 * @return String 
	 */
	public function getModel()	{
		return $this->model;
	}
	
	/**
	 * Returns the CronAgaviAdapter
	 * @return CronAgaviAdapter
	 */
	public function getAgavi() {
		return $this->agavi;
	}
	
	/**
	 * Returns the result of the Database
	 * @return array 
	 */
	public function getDbResult() {
		return $this->dbResult;
	}
	
	/**
	 * Returns the fields to request from the db (usually *)
	 * @return array
	 */
	public function getFields() {
		return $this->fields;
	}
	
	/**
	 *  Sets the name of the doctrine model class
	 *  @param String $model
	 */
	public function setModel($model) {
		$this->model = $model;
	}
	
	/**
	 * Sets the Agavi adapter
	 * @param CronAgaviAdapter $agavi
	 */
	public function setAgavi(CronAgaviAdapter $agavi)  {
		$this->agavi = $agavi;
	}
	
	/**
	 * Sets the database result array
	 * @param array $result
	 */
	public function setDbResult(array $result) {
		$this->dbResult = $result;
	}
	
	/**
	 * Sets the fields to request from the db
	 * @param array $fields
	 */
	public function setFields(array $fields) {
		$this->fields = $fields;
	}
	
	/**
	 * @see CronJobParser
	 */
	public function __construct($model = null, $verbose = false, $logFile = null) {
		if($model) 
			$this->setModel($model);			
		$this->setAgavi(CronAgaviAdapter::getInstance());

		parent::__construct($model,$verbose,$logFile);
	}	
		
	/**
	 * Fetches scheduler entries from the database
	 * 
	 */
	protected function parse() {
		$agavi = $this->getAgavi();
		$query = Doctrine_Query::create()
			->select(implode(",",$this->getFields()))
			->from($this->getModel());
		$result = $query->fetchArray();
		if($result)
			$this->setDbResult($result);
	}
	
	/**
	 * Creates CronJobDefinitions from the db result
	 * 
	 */
	protected function extractCronJobs() {
		$result = $this->getDbResult();
		foreach($result as $index=>$jobArray) {
			$job = $this->reformatJobArray($jobArray,$index);
			$this->addCronJob(CronJobDefinition::fromArray($job,$this));
		}
	}	
	
	/**
	 * Ćallback for exections, set lastExec to the database
	 * @param CronJobDefinition $job
	 */
	public function onExecute(CronJobDefinition $job) {
		$index = explode("_",$job->getName());
		$jobEntry = $this->getDbResult($index[count($index)-1]);
		$modelName = $this->getModel();
		$model = new $modelName();
		foreach($jobEntry[0] as $field=>$value) {
			$model->set($field,$value);
		}
		
		$model->set("lastrun",time());
		echo "result of replace() ".$model->replace()."\n";
	}
	
	/**
	 * Reformats the result array to fit to CronJobDefinitions fromArray function
	 * @param array $job
	 * @param number $index
	 */
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