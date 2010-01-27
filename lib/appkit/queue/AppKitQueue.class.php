<?php

class AppKitQueue extends AppKitArrayContainer implements IteratorAggregate {
	
	private $queue_objects = array ();
	
	public function __construct() {
		$this->initArrayContainer($this->queue_objects);
	}
	
	public function enqueue(AppKitQueueItem $item) {
		array_push($this->queue_objects, $item);
	}
	
	public function dequeue() {
		return array_shift($this->queue_objects);
	}
	
	public function getIterator() {
		return new ArrayIterator($this->queue_objects);
	}
	
	// Disabled methods
	public function offsetGet($offset) {
		throw new AppKitQueueException('This method is disabled!');
	}
	
	public function offsetSet($offset, $value) {
		throw new AppKitQueueException('This method is disabled!');
	}
	
	public function offsetExists($offset) {
		throw new AppKitQueueException('This method is disabled!');
	}
	
	public function offsetUnset($offset) {
		throw new AppKitQueueException('This method is disabled!');
	}
}

class AppKitQueueException extends AppKitException {}

?>