<?php

class AppKitQueueItem extends AppKitBaseClass {
	
	private $data		= null;
	private $timestamp	= null;
	
	public function __construct($data) {
		$this->setData($data);
		$this->flagTimstamp();
	}
	
	public function flagTimstamp() {
		$this->timestamp = microtime(true);
	}
	
	public function getDate() {
		return $this->timestamp;
	}
	
	public function setData($data) {
		$this->data = $data;
		return true;
	}
	
	public function getData() {
		return $this->data;
	}
	
}

?>