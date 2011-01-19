<?php

class AppKit_IconFilesModel extends AppKitBaseModel implements Countable {

	private $real_path = null;
	
	private $web_path = null;
	
	private $files = array ();
	
	private $part = null;
	
	private $count = 0;

	public function initialize(AgaviContext $context, array $parameters = array()) {
		parent::initialize($context, $parameters);
		$this->real_path = AgaviConfig::get('org.icinga.appkit.image_absolute_path');
		$this->web_path = AgaviConfig::get('org.icinga.appkit.image_path');
		
		if ($this->hasParameter('path')) {
			$this->setDirectoryPart($this->getParameter('path'));
		}
		
		if ($this->hasParameter('query')) {
			$this->globFiles($this->getParameter('query'));
		}
	}
	
	public function setDirectoryPart($path) {
		$this->real_path .= DIRECTORY_SEPARATOR. $path;
		$this->web_path .= DIRECTORY_SEPARATOR. $path;
		$this->part = $path;
	}
	
	public function globFiles($query) {
		$s = '.'. $this->getParameter('extension', 'png');
		
		$q = $this->real_path
			. DIRECTORY_SEPARATOR
			. '*'
			. $query
			. '*'
			. $s;
		
		$files = glob($q, GLOB_NOSORT);
		
		foreach ($files as $file) {
			$name = basename($file, $s);
			
			$this->files[] = array (
				'web_path' => $this->web_path. DIRECTORY_SEPARATOR. rawurlencode(basename($file)),
				'name' => $name,
				'short' => $this->part . '.'. $name 
			);
		}
		
		$this->count = count($files);
		
		return true;
	}
	
	public function Count() {
		return $this->count;
	}
	
	public function Files() {
		return $this->files;
	}
}

?>