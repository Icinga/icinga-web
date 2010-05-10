<?php
/**
 * Class that registers files to be changed and is able to revert any changes
 * when something fails
 *
 */

class actionQueueTask extends Task {
	protected $registeredFiles = array();
	protected $path = '';		
	protected $saveState = true;
	protected $restore = false;
	
	public function setRestore($bool) {
		$this->restore = (boolean) $bool;
	}
	public function isRestore() {
		return $this->restore;
	}
	
	public function setPath($path) {
		$this->path = $path;
	}
	public function setSavestate($bool) {
		$this->saveState = (boolean) $bool;
	}
	public function getPath() {
		return $this->path;
	}
	
	public function getRegisteredFiles() {
		return $this->registeredFiles;
	}

	public function registerFile($path) {
		if($this->isRegistered($path))
			return true;
		$this->registeredFiles[$path] = array(
			path => $path,
			data => gzdeflate(file_get_contents($path),9)
		);
	}
	
	static public function getInstance() {
		return self::$instance;
	}
	static protected $instance = null;
	
	public function main() {
		self::$instance = $this;
		if($this->isRestore())
			$this->restoreState();
	}
	
	public function getRegisteredFileByPath($path) {
		if(!$this->isRegistered($path))
			return null;
		return $this->registeredFiles[$path];
	}
	

	public function isRegistered($path) {
		return isset($this->registeredFiles[$path]);
	}
	// writes the original content back to the files
	public function rollback() {
		$files = $this->getRegisteredFiles() ;
		foreach($files as $path=>$fileInfo) {
			$write = gzinflate($fileInfo["data"]);
			if($write)
				file_put_contents($path,$write);
		}
	}
	
	public function restoreState() {
		if(!file_exists($this->getPath().".backup.dat")) 
			throw new BuildException("Could not find a backup file");
		$data = file_get_contents($this->getPath().".backup.dat");
		$files = unserialize($data);
		$this->registeredFiles = $files;
		$this->rollback();
	}
	
	public function saveState() {
		file_put_contents($this->getPath().".backup.dat",serialize(($this->getRegisteredFiles())));
	}
	
	public function __destruct() {
		if($this->saveState)
			$this->saveState();
	}
}