<?php
/**
 * Registers files for rollback and stores them in a backup file
 * Must be called before any files are registered.
 * The data will be gzip encoded
 * @example: 
 * <target name="install-module">
 *		<!-- define tasks -->
 *		<taskdef name="actionQueue" classname="bin.actionQueueTask" />
 *		<actionQueue path="./"/>
 *		...
 *
 * @author jmosshammer <jannis.mosshammer@netways.de>
 */
class actionQueueTask extends Task {
	/**
	 * Array of registered file objects, indexed by their name
	 * @var array
	 */
	protected $registeredFiles = array();
	/**
	 * The path where to store the .backup.dat file
	 * @var path to store
	 */
	protected $path = '';		

	/**
	 * Determines whether the state should be saved on destruct
	 * @var boolean
	 */
	protected $saveState = true;
	
	/**
	 * When set to true, a restore will be performed
	 * @var unknown_type
	 */
	protected $restore = false;
	/**
	 * @param boolean $bool
	 */
	public function setRestore($bool) {
		$this->restore = (boolean) $bool;
	}
	/**
	 * @return boolean
	 */
	public function isRestore() {
		return $this->restore;
	}
	/**
	 * @param string $path
	 */
	public function setPath($path) {
		$this->path = $path;
	}
	/**
	 * @param boolean $bool
	 */
	public function setSavestate($bool) {
		$this->saveState = (boolean) $bool;
	}
	/**
	 * @return String
	 */
	public function getPath() {
		return $this->path;
	}
	/**
	 * @return array
	 */
	public function getRegisteredFiles() {
		return $this->registeredFiles;
	}
	
	/**
	 * Registers a file for the backup
	 * @param string $path A valid file path
	 * 
	 * @return boolean True on success, otherwise false
	 */
	public function registerFile($path) {
		if(!file_exists($path))
			return false;
		if($this->isRegistered($path))
			return true;
		$this->registeredFiles[$path] = array(
			path => $path,
			data => gzdeflate(file_get_contents($path),9)
		);
		return true;
	}
	
	/**
	 * Returns the newest instance of this object.
	 * This is not singleton - when creating a new object the instance will point 
	 * to the newest file. 
	 * @return actionQueueTask
	 */
	static public function getInstance() {
		return self::$instance;
	}
	/**
	 * The newest instance or null if none is active
	 * @var actionQueueTask
	 */
	static protected $instance = null;
	
	/**
	 * Creates a new instance and restores the state if the restore flag is set
	 */
	public function main() {
		self::$instance = $this;
		if($this->isRestore())
			$this->restoreState();
	}
	
	/**
	 * Returns the register object for a path, or null if not registered
	 * @param string $path 
	 * @return array
	 */
	public function getRegisteredFileByPath($path) {
		if(!$this->isRegistered($path))
			return null;
		return $this->registeredFiles[$path];
	}
	
	/**
	 * Returns whether a path is registered
	 * @param boolean $path
	 */
	public function isRegistered($path) {
		return isset($this->registeredFiles[$path]);
	}
	
	/**
	 * Recreates the original state of registered files 
	 * by overwriting files
	 */
	public function rollback() {
		$files = $this->getRegisteredFiles() ;
		foreach($files as $path=>$fileInfo) {
			$write = gzinflate($fileInfo["data"]);
			if($write)
				file_put_contents($path,$write);
		}
	}
	
	/**
	 * Loads the .backup.dat file, loads its data into the 
	 * registry and calls a rollback
	 */
	public function restoreState() {
		if(!file_exists($this->getPath().".backup.dat")) 
			throw new BuildException("Could not find a backup file");
		$data = file_get_contents($this->getPath().".backup.dat");
		$files = unserialize($data);
		$this->registeredFiles = $files;
		$this->rollback();
	}
	/**
	 * Saves the state of the registry to .backup.dat 
	 */
	public function saveState() {
		file_put_contents($this->getPath().".backup.dat",serialize(($this->getRegisteredFiles())));
	}
	
	public function __destruct() {
		if($this->saveState)
			$this->saveState();
	}
}