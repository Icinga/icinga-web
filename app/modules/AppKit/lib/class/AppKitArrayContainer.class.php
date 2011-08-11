<?php
/**
 * PHP implementation of an simple array container
 * implementing all common SPL array interfaces
 * @author mhein
 *
 */
abstract class AppKitArrayContainer
    implements ArrayAccess, Countable {

    /**
     * The array itself
     * @var array
     */
    private $arrayContainer = null;

    /**
     * Connect any array to the container methods
     * @param array $array
     */
    protected function initArrayContainer(array &$array) {
        $this->arrayContainer =& $array;
    }

    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset) {
        if ($this->offsetExists($offset)) {
            return $this->arrayContainer[$offset];
        }

        return null;
    }

    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value) {
        if ($offset === null) {
            $offset = $this->count();
        }

        $this->arrayContainer[$offset] =& $value;
    }

    /**
     * Insert item at the position you want
     * @param mixed $key
     * @param mixed $data
     * @param mixed $afterItem
     */
    public function insertItem($key, $data, $afterItem) {
        $tmpArray = array();
        foreach($this->arrayContainer as $srcKey => $srcData) {
            $tmpArray[$srcKey] = $srcData;

            if ($srcKey == $afterItem) {
                $tmpArray[$key] = $data;
            }
        }
        $this->arrayContainer = $tmpArray;
    }

    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset) {
        return array_key_exists($offset, $this->arrayContainer);
    }

    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset) {
        if ($this->offsetExists($offset)) {
            unset($this->arrayContainer[$offset]);
            return true;
        }

        return false;
    }

    /**
     * Returns the whole container
     * @return mixed
     */
    public function toArray() {
        return $this->arrayContainer;
    }

    /**
     * (non-PHPdoc)
     * @see Countable::count()
     */
    public function count() {
        return count($this->arrayContainer);
    }

    /**
     * Return all keys used in the array
     * @return array
     */
    public function getKeys() {
        return array_keys($this->arrayContainer);
    }

    /**
     * Return all values in the array
     * @return array
     */
    public function getValues() {
        return array_values($this->arrayContainer);
    }
}

?>
