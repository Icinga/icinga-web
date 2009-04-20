<?php

abstract class AppKitArrayContainer extends AppKitBaseClass
implements ArrayAccess, Countable
{
	
	private $arrayContainer = null;
	
	protected function initArrayContainer(array &$array) {
		$this->arrayContainer =& $array;
	}
	
	public function offsetGet($offset) {
		if ($this->offsetExists($offset))
			return $this->arrayContainer[$offset];
		return null;
	}
	
	public function offsetSet($offset, $value) {
		if ($offset === null) {
			$offset = $this->count();
		}
		$this->arrayContainer[$offset] =& $value;
	}

	public function insertItem ($key, $data, $afterItem) {
		$tmpArray = array();
		foreach ($this->arrayContainer as $srcKey => $srcData) {
			$tmpArray[$srcKey] = $srcData;
			if ($srcKey == $afterItem) {
				$tmpArray[$key] = $data;
			}
		}
		$this->arrayContainer = $tmpArray;
	}
	
	public function offsetExists($offset) {
		return array_key_exists($offset, $this->arrayContainer);
	}
	
	public function offsetUnset($offset) {
		if ($this->offsetExists($offset)) {
			unset($this->arrayContainer[$offset]);
			return true;
		}
		return false;
	}
	
	public function toArray() {
		return $this->arrayContainer;
	}
	
	public function count() {
		return count($this->arrayContainer);
	}
}

?>