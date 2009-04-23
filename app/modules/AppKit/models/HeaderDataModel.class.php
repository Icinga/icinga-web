<?php

class AppKit_HeaderDataModel extends ICINGAAppKitBaseModel
implements AgaviISingletonModel
{
	const TYPE_CSS_RAW		= 1;
	const TYPE_CSS_FILE		= 2;
	const TYPE_META			= 3;
	const TYPE_JS_RAW		= 4;
	const TYPE_JS_FILE		= 5;
	
	private $data = array (
		self::TYPE_CSS_RAW		=> array (),
		self::TYPE_CSS_FILE		=> array (),
		self::TYPE_META			=> array (),
		self::TYPE_JS_RAW		=> array (),
		self::TYPE_JS_FILE		=> array (),
	);
	
	public function addCssData($name, $data) {
		$this->data[self::TYPE_CSS_RAW][$name] = $data;
		return true;
	}
	
	public function getCssData() {
		return $this->data[self::TYPE_CSS_RAW];
	}
	
	public function addCssFile($file) {
		$this->data[self::TYPE_CSS_FILE][$file] = $file;
		return true;
	}
	
	public function getCssFiles() {
		return array_values($this->data[self::TYPE_CSS_FILE]);
	}
	
	public function addJsData($name, $data) {
		$this->data[self::TYPE_JS_RAW][$name] = $data;
	}
	
	public function getJsData() {
		return $this->data[self::TYPE_JS_RAW];
	}
	
	public function addJsFile($file) {
		$this->data[self::TYPE_JS_FILE][$file] = $file; 
	}
	
	public function getJsFiles() {
		return array_values($this->data[self::TYPE_JS_FILE]);
	}
	
	public function addMetaTag($name, $value) {
		$this->data[self::TYPE_META][$name] = $value;
		return true;
	}
	
	public function getMetaTags() {
		return $this->data[self::TYPE_META];
	}
}

?>