<?php

class AppKitMessageQueueItem extends AppKitQueueItem {
	
	const ERROR		= 1;
	const INFO		= 2;
	const LOG		= 4;
	
	/**
	 * @param $message
	 * @return AppKitMessageQueueItem
	 * @author Marius Hein
	 */
	public static function Error($message) {
		return new AppKitMessageQueueItem($message, self::ERROR);
	}
	
	/**
	 * 
	 * @param $message
	 * @return AppKitMessageQueueItem
	 * @author Marius Hein
	 */
	public static function Info($message) {
		return new AppKitMessageQueueItem($message, self::INFO);
	}
	
	/**
	 * 
	 * @param $message
	 * @return AppKitMessageQueueItem
	 * @author Marius Hein
	 */
	public static function Log($message) {
		return new AppKitMessageQueueItem($message, self::LOG);
	}
	
	private $type = null;
	
	public function __construct($message, $type) {
		$this->setType($type);
		parent::__construct($message);
	}
	
	public function setType($type) {
		$this->type = $type;
		return true;
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function toString() {
		return (string)$this->getData();
	}
	
}

?>