<?php
require_once("xmlMergerTask.php");
/**
 * Task for calling multiple xmlMerges for a complete xml folder
 * 
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class xmlMergerChainTask extends Task {
	protected $folder = null;
	
	public function setFolder($folder) {
		$this->folder = $folder;
	}
	public function getFolder() {
		return $this->folder;
	}
	
	public function init() {
		
	}
	public function main() {
		// open folder
		$icingaPath = $this->getProject()->getUserProperty("PATH_Icinga");
		$recovery = actionQueueTask::getInstance();
		$files = scandir($this->getFolder());
		foreach($files as $file) {
			if(substr($file,-4,4) != '.xml')
				continue;
			$path = str_replace("_","/",str_replace("%PATH_Icinga%",$icingaPath."/",$file));
			$recovery->registerFile($path);
			echo "\nMerging ".$path; 
			$xmlMerger = new xmlMergerTask();
			$xmlMerger->setSource($this->getFolder()."/".$file);
			$xmlMerger->setTarget($path);
			$xmlMerger->setOverwrite(false);
			$xmlMerger->setProject($this->getProject());
			$xmlMerger->setLocation($this->getLocation());
			$xmlMerger->perform();
		}
	}
}