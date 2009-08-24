<?php

class AppKit_SquishFileContainerModel extends ICINGAAppKitBaseModel
implements AgaviISingletonModel
{
	const TYPE_JAVASCRIPT	= 'js';
	const TYPE_STYLESHEET	= 'css';
	
	private $files			= array();
	private $type			= null;
	
	public function addFile($type, $file) {
		if (file_exists($file)) {
			$this->files[$type][] = $file;
			return true;
		}
		
		throw new AppKitModelException('File not found: '. $file);
	}
	
	public function setType($type) {
		$this->type = $type;
		return true;
	}
	
	public function squishContents() {
		
		if (is_array($this->files[$this->type])) {
			$loader = new AppKitBulkLoader();
			$loader->setCompress(false);
			
			array_walk($this->files[$this->type], array(&$loader, 'setFile'));
			
			return $loader->getContent();
		}
		
		return null;
		
		
	}
}

?>