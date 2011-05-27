<?php

class AppKitNavContainer extends AppKitArrayContainer
    implements RecursiveIterator {

    private $items = array();
    private $keys = array();
    private $key = 0;
    private $valid = false;

    public function __construct() {
        $this->initArrayContainer($this->items);
    }

    public function addItem(AppKitNavItem $item) {
        $this->offsetSet($item->getName(), $item);
        return $item;
    }

    public function addItemBefore($name, AppKitNavItem $item) {
        if(array_key_exists($name, $this->items)) {
            $replace = array($item->getName() => &$item);
            $this->items = AppKitArrayUtil::arrayKeyInsertBefore($this->items, $name, $replace);
            return $item;
        }

        throw new AppKitNavContainerException('Name nicht gefunden!');

        return false;

    }

    /**
     *
     * @param string $parent_name
     * @param AppKitNavItem $item
     * @return AppKitNavItem
     */
    public function addSubItem($parent_name, AppKitNavItem $item) {
        if($this->offsetExists($parent_name)) {
            $container = $this->offsetGet($parent_name)->getContainer();
            return $container->addItem($item);
        }

        return false;

    }

    public function hasChildren() {
        if($this->current() && $this->current()->getContainer()->count()) {
            return true;
        }

        elseif($this->count() > 0) {
            return true;
        }
        return false;
    }

    public function getChildren() {
        return $this->current()->getContainer();
    }

    public function current() {

        if(isset($this->keys[ $this->key ])) {
            return $this->offsetGet($this->keys[ $this->key ]);
        }

        return null;

    }

    public function next() {
        $this->key++;
        $this->valid = $this->count() > $this->key ? true : false;
    }

    public function key() {
        return $this->key;
    }

    public function valid() {
        return $this->valid;
    }

    public function rewind() {
        $this->keys = array_keys($this->items);
        $this->key = -1;
        $this->next();
    }

    public function getIterator() {
        return new RecursiveIteratorIterator($this, RecursiveIteratorIterator::SELF_FIRST);
    }

}

class AppKitNavContainerException extends AppKitException { }



?>