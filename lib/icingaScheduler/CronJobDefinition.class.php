<?php
/**
 * 
 * Definition of a job to perform 
 * Checks if a job should be performed and calls the execute() method of the jobs class
 * This should ne the result of all CronJobParsers 
 * 
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class CronJobDefinition {
	/**
	 * The name of the cronJob
	 * @var String
	 */
	protected $name = "";
	
	/**
	 * The interval of the cronjon 
	 * @var Array
	 */
	protected $interval = array(
		"days"		=> 0,
		"hours" 	=> 0,
		"minutes"	=> 0,
		"times"		=> null
	);
	
	/**
	 * The starttime of the jobs timerange
	 * @var String
	 */
	protected $startTime = "00:00";
	
	/**
	 * The endtime of the jobs timerange
	 * @var String
	 */
	protected $endTime = "24:00";
	
	/**
	 * The weekdays on which the job should be performed 
	 * @var array
	 */
	protected $weekDays = array();
	
	/**
	 * The path where the jobs class lies
	 * @var String
	 */
	protected $path = "";
	
	/**
	 * The classname of the job
	 * @var String
	 */
	protected $class = "";
	
	/**
	 * Arguments to call for the job
	 * @var String 
	 */
	protected $arguments = array();
	
	/**
	 * The last execution time of the job
	 * @var String
	 */
	protected $lastExecution = "";
	
	/**
	 * Is the job forkable? 
	 * Is currently ignored, Doctrine get's confused on forks
	 * 
	 * @var Bool
	 */
	protected $forkable = false;
	
	/**
	 * Is the job suspended? 
	 * Suspended jobs are not executed
	 * @var Bool
	 */
	protected $suspended = false;
	
	/**
	 * The timestamp of the last execution
	 * @var unknown_type
	 */
	protected $lastExec = 0;
	
	/**
	 * The parser that called this job. 
	 * Some parsers have a onExecute callback function
	 * @var CronJobParser
	 */
	protected $parser = null;
	
	public function getName() {
		return $this->name;
	}
	public function getInterval() {
		return $this->interval;
	}
	public function getPath() {
		return $this->path;
	}
	public function getClass() {
		return $this->class;
	}
	public function getLastExection() {
		return $this->lastExection;
	}
	public function isForkable() {
		return $this->forkable;
	}
	public function getStartTime() {
		return $this->startTime;
	}
	public function getEndTime() {
		return $this->endTime;
	}
	public function getWeekDays() {
		return $this->weekDays;
	}
	public function getArguments() {
		return $this->arguments;
	}
	public function isSuspended()  {
		return $this->suspended;
	}
	public function getParser() {
		return $this->parser;
	}
	public function getLastExec() {
		return $this->lastExec;
	}
	
	
	public function setName($name) {
		$this->name = $name;
	}
	public function setInterval(array $arr) {
		$this->interval = $arr;
	}
	public function setPath($path)	{
		$this->path = $path;
	}
	public function setClass($class)	{
		$this->class = $class;
	}
	public function setLastExection($ts) {
		$this->lastExecution = $ts;
	}
	public function setForkable($bool)	{
		$this->forkable = $bool;
	}
	public function setStartTime($time) {
		$this->startTime = $time;
	}
	public function setEndTime($time) {
		$this->endTime = $time;
	}
	public function setWeekDays(array $days) {
		$this->weekDays = $days;
	}
	public function addWeekDay($day) {
		$this->weekDays[] = $day;
	}
	public function setArguments(array $args) {
		$this->arguments = $args;
	}	
	public function addArgument($name,$value) {
		$this->arguments[$name] = $value;	
	}
	public function setSuspended($bool) {
		$this->suspended = $bool;
	}
	public function setParser(CronJobParser $parser) {
		$this->parser = $parser;
	}
	public function setLastExec($time) {
		$this->lastExec = $time;
	}
	
	public function __construct() {}

	/**
	 * Starts time validation and executes the job if it's meant to be
	 * executed
	 * 
	 * @throws RuntimeException if the class is not found
	 */
	public function execute() {

		if($this->isSuspended())
			return true;
			
		//try to load class			
		if(!include_once($this->getPath().$this->getClass().".class.php"))
			throw new RuntimeException("Class ".$this->getClass()." not found");
	
		// The job is currently not in the interval -> stop
		if(!$this->checkSchedule())
			return true;	
			
		$this->saveLastExec(true);
		$this->getParser()->onExecute($this);
		/**
		 * Doctrine doesn't seem to like forking
		 * 
		 */
		// if process control is enabled, fork it!
		/*if(function_exists("pcntl_fork") && $this->isForkable())
			$this->executeAsProcess();
		else*/ 
			$this->callJob();
	}
	
	/**
	 * Calls the job in a seperate process.
	 * The parent will return immediately, the child process will be terminated after execution
	 * 
	 */
	protected function executeAsProcess() {
		CronJobMetaProvider::getInstance()->processStarted();
		$pid = pcntl_fork();
		if($pid != -1 && $pid) 	{
			$this->callJob();
			exit();
		}
		CronJobMetaProvider::getInstance()->processFinished();
	}
	
	/**
	 * Checks whether the process should be executed or not
	 * 
	 * @return Boolean True if the process should be executed, false if not
	 */
	protected function checkSchedule() {
		$name = $this->getName();
		$lastExec = $this->getLastExec();
		if(!$lastExec) {
			$storage = CronJobMetaProvider::getInstance()->getStorage();
			if(array_key_exists($name,$storage)) {
				$cronInfo = $storage[$name];	
				$lastExec = $cronInfo["lastExec"];			
			}
		}
		if(!$this->isInRange())
			return false;
		
		return($this->isInInterval($lastExec) || $this->isInFixedTime($lastExec));
	}
	
	/**
	 * Checks the timerange and the weekdays to execute the job
	 * 
	 * @return Boolean Whether the job is in range or not
	 */
	protected function isInRange() {
		$today = time();
		$y = date("Y",$today);
		$m = date("n",$today);
		$d = date("j",$today);
		
		$startTimeArr = explode(":",$this->getStartTime());
		$startTime = mktime(intval($startTimeArr[0]),intval($startTimeArr[1]),0,$m,$d,$y);
		
		$endTimeArr = explode(":",$this->getEndTime());
		$endTime = mktime(intval($endTimeArr[0]),intval($endTimeArr[1]),0,$m,$d,$y);
		// Check the weekday
		$day = Date("w");
		if(!$day) //Sunday should be 7 instead of 0
			$day = 7;
			
		if(!in_array($day,$this->getWeekDays()))
			return false;

		//Check the timerange
		if($startTime < $endTime)
			return($today > $startTime && $today < $endTime);
		else 
			return ($today > $startTime || $today < $endTime);

		return false;
	}

	/**
	 * Checks if the Job is in the defined interval (d,h,m)
	 * 
	 * @param Numeric $lastExec The timestamp of the last execution
	 * @return Boolean 
	 */
	protected function isInInterval($lastExec = 0) {
		$interval = $this->getTimeIntervalAsNumber(); 
		$now = time();

		$timeGap = $now-$lastExec;
		return ($timeGap>=$interval);
	}
	
	/**
	 * Checks if the job is in the fixed time definitions 
	 * 
	 * @param Numeric $lastExec the timestamp of the last execution
	 * @return Boolean
	 */
	protected function isInFixedTime($lastExec) {
		$interval = $this->getInterval();
		$times = @$interval["times"];
		if(!$times) 
			return false;
			
		$today = time();
		$y = date("Y",$today);
		$m = date("n",$today);
		$d = date("j",$today);
		
		foreach($times as $time) {
			$timeArr = explode(":",$time);
			$utime = mktime(intval($timeArr[0]),intval($timeArr[1]),0,$m,$d,$y);
			// check if utime is earlier than now
			
			if($utime-time() < 0 && $lastExec<$utime)
				return true;
		}
 		return false;
	}
 	
	/**
	 * Returns interval definitions in seconds
	 * 
	 * @return Numeric
	 */
	protected function getTimeIntervalAsNumber() {
		$interval = $this->getInterval();
		$hours = $interval["hours"];
		$minutes = $interval["minutes"];
		$days = $interval["days"];
		$time = $minutes*60;
		$time += $hours*3600;
		$time += $days*24*3600;
		return $time;
	}
	
	/**
	 * Constructs the class defined in this job and calls it's execute method
	 */
	protected function callJob() {
		$class = $this->getClass();
		$arguments = $this->getArguments();
		$execProgram = new $class($arguments,true);
		$result = $execProgram->execute();	
	}
	
	/**
	 * Thells the CronMetaJobProvider that the job is executed and to save it's time
	 * 
	 * @param String $result Did the job succeed?
	 */
	protected function saveLastExec($result) {
		$storage = CronJobMetaProvider::getInstance()->getStorage();
		$name = $this->getName();
		$storage[$name]= array(
			"lastExec" => time(),
			"result" => $result
		);
		CronJobMetaProvider::getInstance()->setStorage($storage);
		CronJobMetaProvider::getInstance()->saveStorage();
	}
	
	/**
	 * Creates a CronJobDefinition from a SimpleXMLElement description $xml
	 * provided by the parser $parser 
	 * 
	 * @param SimpleXMLElement $xml
	 * @param CronJobParser $parser
	 * 
	 * @return CronJobDefinition
	 */
	static public function fromXML(SimpleXMLElement $xml,CronJobParser $parser) {
		$interval = array();
		$interval["days"] = (String) $xml->Days;
		$interval["hours"] = (String) $xml->Hours;
		$interval["minutes"] = (String) $xml->Minutes;
		$interval["times"] = array();
		foreach($xml->Times->children() as $time) {
			$interval["times"][] = (String) $time;
		}
		$args = array();
		foreach($xml->Args->children() as $argument) {
			$args[] = (String) $argument;
		}
		$days = str_split((String)$xml->Weekdays);
	
		// create and return object
		$obj = new self();
		$obj->setName((String) $xml->Name);
		$obj->setParser($parser);
		$obj->setForkable($xml->Fork == "True");
		$obj->setInterval($interval);
		$obj->setPath((String) $xml->Path);
		$obj->setClass((String) $xml->Class);
		$obj->setStartTime((String) $xml->From);
		$obj->setEndTime((String) $xml->To);
		$obj->setArguments($args);
		$obj->setWeekDays($days);
		$obj->setSuspended($xml->Suspended == "True");

		return $obj;
	}
	
	/**
	 * Creates a CronJobDefinition from a Array $job
	 * provided by the parser $parser 
	 * 
	 * @param Array $xml
	 * @param CronJobParser $parser
	 * 
	 * @return CronJobDefinition
	 */
	static public function fromArray(array $job,CronJobParser $parser) {
		// create and return object
		$obj = new self();
		$obj->setParser($parser);
		$obj->setName($job["name"]);
		$obj->setForkable((Boolean) $job["forkable"]);
		$obj->setInterval($job["interval"]);
		$obj->setPath($job["path"]);
		$obj->setClass($job["class"]);
		$obj->setStartTime($job["starttime"]);	
		$obj->setEndTime($job["endtime"]);
		$obj->setArguments($job["args"]);
		$obj->setWeekDays($job["weekdays"]);
		$obj->setSuspended((Boolean) $job["suspended"]);
		if(isset($job["lastrun"]))
			$obj->setLastExec($job["lastrun"]);
		else $obj->setLastExec(0);
		return $obj;
	}
}
