<?php

class BPAddon_bpCfgInterpreterModel extends BPAddonBaseModel implements Iterator
{
	const LINEBUFFER = 4096;

	private $cfg = "";
	private $curLineNr = "";
	private $curLine = "";
	private $hdl = null;
	private $processes = array();
	
	public function __construct($filename) {
		if(!file_exists($filename))	
			throw new AppKitException("Interpreting failed, file does not exist");
		if(!is_readable($filename))	
			throw new AppKitException("Interpreting failed, file is not readable");
		
		
		$this->hdl = fopen($filename,"r");
		if(!is_resource($this->hdl))
			throw new AppKitException("Couldn't open file!");
	}
	
	private function parseLine() {
		
		if($this->parseNewBP())
			return true;
		else if($this->parseBPDisplayInfo())
			return true;
		else if($this->parseExternalInfo())
			return true;
		else if($this->parseTemplate())
			return true;
		else if($this->parseInfoURL())
			return true;
		else 
			return false;
		
	}
	
	private function parseNewBP() {
		$matches = array();
		$bpTargets = array();
		$line = $this->curLine;
		if(preg_match_all("/^(?P<BP_NAME>[^=]*?) = ". // 1. Business process name
						  "(?>(?P<OF_OPERATOR>\d+) *?of: *?)?". // 2. option of: operator
						  " *?/",$line,$matches)) {
			$bp = $this->getContext()->getModel('businessProcess','BPAddon');

			$bp->setName(trim($matches["BP_NAME"][0]));
			if($matches["OF_OPERATOR"][0]) 
				$bp->setMinCount($matches["OF_OPERATOR"][0]);
			
			$offset = strlen($matches[0][0])-1;
			
			preg_match_all("/(?>(?>(: *?)?(?P<HOST>[^;\&\|\+]{2,}[^ ]);(?P<SERVICE>[^\&\|\+]{2,}[^  &\+\|]))|(?P<BP>[^\&\|+]{2,}))/",
								$line,
								$bpTargets,
								PREG_PATTERN_ORDER,
								$offset
							);

			foreach($bpTargets["SERVICE"] as $nr=>$subElement) {
				if($subElement) {
					$service = $this->getContext()->getModel('service','BPAddon');
					$service->setServiceName(trim($subElement));
					$service->setHostName(trim($bpTargets["HOST"][$nr]));
					$bp->addService($service);
				} else if($bpTargets["BP"][$nr]) {
					$bp->addSubProcess($bpTargets["BP"][$nr]);
				}
			}
			// Determine the bp/service relationship 
			preg_match_all("/.*([\|\+&])/",$line,$bpTargets,PREG_SET_ORDER,$offset);
			
			if(empty($bpTargets)) { 
				$bp->setType('AND');	
			} else {
				switch($bpTargets[0][1])	{
					case '|':
						$bp->setType('OR');
						break;
					case '&':
						$bp->setType('AND');
						break;
					case '+':
						$bp->setType('MIN');
						break;
				}
			}
			$this->processes[$bp->getName()] = $bp;
			return true;
		} else 
			return false;		
	}
	
	private function parseBPDisplayInfo() {
		$matches = array();
		$line = $this->curLine;
		if(preg_match_all("/^display (?P<DISPLAY>\d*);(?P<PROCESS>[\-_\.\w]+);(?P<DISPLAYNAME>.*)$/",$line,$matches,PREG_SET_ORDER)) {
			if(!isset($this->processes[$matches[0]["PROCESS"]]))
				return true;
			$bp = $this->processes[$matches[0]["PROCESS"]];
			
			$bp->setPriority($matches[0]["DISPLAY"]);
			$bp->setLongName($matches[0]["DISPLAYNAME"]);
			return true;
		}
		return false;
	}
	
	private function parseTemplate() {
		$matches = array();
		$line = $this->curLine;
		if(preg_match_all("/^template (?P<PROCESS>[-_\w]+);(?P<TEMPLATE>.*)$/",$line,$matches,PREG_SET_ORDER)) {
			if(!isset($this->processes[$matches[0]["PROCESS"]]))
				return true;
			$bp = $this->processes[$matches[0]["PROCESS"]];
			$bp->setTemplate($matches[0]["TEMPLATE"]);
			return true;
		}
		return false;
	}
	
	private function parseExternalInfo() {
		$matches = array();
		$line = $this->curLine;
		if(preg_match_all("/^external_info (?P<PROCESS>[-_\w]+);(?P<STATUS>.*)$/",$line,$matches,PREG_SET_ORDER)) {
			if(!isset($this->processes[$matches[0]["PROCESS"]]))
				return true;
			$bp = $this->processes[$matches[0]["PROCESS"]];
			$bp->setStatus($matches[0]["STATUS"]);
			return true;
		}
		return false;
	}
	private function parseInfoURL() {
		return false; // These will be ignored
	}
	public function rewind() {
		$this->curLineNr = 0;
		fseek($this->hdl,0);
	}
	
	public function next() {
		do {
			$this->curLineNr++;
			$this->curLine = fgets($this->hdl,self::LINEBUFFER);	
		// ignore comments and empty lines
		} while(preg_match("/[#\W ]/",$this->curLine[0]) && $this->valid());
		
	}
	
	public function current() {
		return $this->curLine;	
	}

	public function key() {
		return $this->curLine;
	}
	
	public function valid() {
		return !feof($this->hdl);
	}
	
	public function parse($asArray = false) {
		foreach($this as $line) {
			$this->parseLine();
		}
		if(!$asArray)
			return $this->processes;
			
		$returnArray = array();
		foreach($this->processes as $process)
			$returnArray[] = $process->__toArray();
		return $returnArray;
	}
	
	public function __destruct() {
		if(is_resource($this->hdl))
			fclose($this->hdl);
	}
}

?>