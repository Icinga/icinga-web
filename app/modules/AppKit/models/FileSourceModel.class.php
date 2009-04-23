<?php

class AppKit_FileSourceModel extends ICINGAAppKitBaseModel
{

	public function fileExists($filename) {
		return file_exists($filename);
	}
	
	public function readFileContent($filename) {
		if ($this->fileExists($filename)) {
			return file_get_contents($filename);
		}
		
		throw new AppKitModelException('File not found!');
	}
	
}

?>