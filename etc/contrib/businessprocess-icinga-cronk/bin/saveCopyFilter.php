<?php 
/**
 * Filter that stores overwritten files in an actionQueue task
 * 
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class saveCopyFilter extends FilterReader {
    /*
     * Set the project to work with
    */
    function setProject(Project $project) {
        $this->project = $project;
    }

    /*
     * Get the project
    */
    function getProject() {
        return $this->project;
    }
	public function read($len=null) {
		$p = new Project();
		
		$toFile = Register::getSlot("task.copy.currentFromFile")->getValue();	
		if($toFile) {
			if(file_exists($toFile)) {
				actionQueueTask::getInstance()->registerFile($toFile);
			}	
		}
		return parent::read();
	}
}

?>