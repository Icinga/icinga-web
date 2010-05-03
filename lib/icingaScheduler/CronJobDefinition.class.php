<?php

class CronJobDefinition {
	protected $name = "";
	protected $interval = array(
		"days"		=> 0,
		"hours" 	=> 0,
		"minutes"	=> 0,
		"times"		=> null
	);
	protected $startTime = "00:00";
	protected $endTime = "24:00";
	protected $weekDays = array();
	protected $path = "";
	protected $class = "";
	protected $arguments = array();
	protected $lastExecution = "";
	protected $forkable = false;
	protected $suspended = false;
	protected $lastExec = 0;
	
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

	public function execute() {

		if($this->isSuspended())
			return true;
			
		//try to load class			
		if(!include_once($this->getPath().$this->getClass().".class.php"))
			throw new RuntimeException("Class ".$this->getClass()." not found");
	
		// The job is currently not in the interval
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
	
	protected function executeAsProcess() {
		CronJobMetaProvider::getInstance()->processStarted();
		$pid = pcntl_fork();
		if($pid != -1 && $pid) 	{
			$this->callJob();
			exit();
		}
		CronJobMetaProvider::getInstance()->processFinished();
	}
	
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
	
	protected function isInInterval($lastExec = 0) {
		$interval = $this->getTimeIntervalAsNumber(); 
		$now = time();

		$timeGap = $now-$lastExec;
		return ($timeGap>=$interval);
	}
	
	protected function isInFixedTime($lastExec) {
		$interval = $this->getInterval();
		$times = $interval["times"];
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
	
	protected function callJob() {
		$class = $this->getClass();
		$arguments = $this->getArguments();
		$execProgram = new $class($arguments,true);
		$result = $execProgram->execute();	
	}
	
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
