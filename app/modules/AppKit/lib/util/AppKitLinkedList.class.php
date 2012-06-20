<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}


interface IAppKitLinkedListItem {
    public function __construct($value,$id=null,IAppKitLinkedListItem $previous = null,  IAppKitLinkedListItem $next = null);
}

class AppKitLinkedListItem implements IAppKitLinkedListItem {
    public $previous = null;
    public $next = null;
    public $id = null;
    public $value;
    private static $idCounter =  0;
    public function __construct($value,$id=null,IAppKitLinkedListItem $previous = null,  IAppKitLinkedListItem $next = null) {
        if (!$id) {
            $id = $this->genId($value);
        }

        $this->id = $id;
        $this->value = $value;
        $this->previous = $previous;
        $this->next = $next;
    }
    private function genId($value) {
        if (is_array($value) && isset($value['id'])) {
            return $value['id'];
        } else if (is_object($value) && method_exists($value,"getId")) {
            return $value->getId();
        } else if (is_object($value) && property_exists($value,"id")) {
            return $value->id;
        }

        return md5(self::$idCounter++);
    }
}

/**
* Interface for Linked list, same style like SplDoublyLinkedList, which isn't supported
* in PHP < 5.3
*
**/
interface IAppKitLinkedList {

    public function bottom();


    public function getIteratorMode();
    public function isEmpty();
    public function pop();
    public function prev();
    public function push($value);

    public function setIteratorMode($mode);
    public function shift();
    public function top();
    public function unshift($value);

}

class AppKitLinkedList implements IAppKitLinkedList, Iterator , ArrayAccess , Countable {
    private $item = null;
    private $count = 0;
    private $class = "";
    // flags for position validation
    private static $FLAG_POS_END = 1;
    private static $FLAG_POS_START = 2;
    private static $FLAG_NONE = 0;

    private $flag = 0;

    /**
    * Creates a new linked list with $itemClass as elements
    *
    * @param String Class to use for elements (injected dependency)
    * @param Array  Additional arguments that will be redirected to @see internalConstruct
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    final public function __construct($itemClass = "AppKitLinkedListItem",array $arguments = array()) {
        $this->class = "AppKitLinkedListItem";
        $this->internalConstruct();
    }

    /**
    * Overridable constructor method
    *
    * @param Array
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function internalConstruct(array $arguments = array()) {}

    /**
    * Internal method that Returns the last item of the linked list as an AppKitLinkedListItem
    * list is empty
    *
    * @return null | AppKitLinkedListItem
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    private function _bottom() {
        if ($this->isEmpty()) {
            return null;
        }

        $item = $this->item;

        while ($item->next != null) {
            $item = $item->next;
        }

        return $item;
    }

    /**
    * Returns the last item of the linked list or null if
    * list is empty
    *
    * @return null | mixed
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function bottom() {
        $val = $this->_bottom();

        if ($val) {
            return $val->value;
        } else {
            return null;
        }
    }

    /**
    * Returns the current item count
    *
    * @return integer
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    */
    public function count() {
        return $this->count;
    }

    /**
    * Internal function that returns the current item as an AppKitLinkedListItem
    *
    * @return AppKitLinkedListItem | null
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    private function _current() {
        if ($this->valid()) {
            return $this->item;
        }

        return null;
    }


    /**
    * Returns the current item or null if not valid
    *
    * @return mixed | null
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function current() {
        if ($this->valid()) {
            return $this->item->value;
        }

        return null;
    }
    /**
    * Always returns  SplDoublyLinkedList::IT_MODE_FIFO | SplDoublyLinkedList::IT_MODE_KEEP;
    * @return integer
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function getIteratorMode() {
        return SplDoublyLinkedList::IT_MODE_FIFO | SplDoublyLinkedList::IT_MODE_KEEP;
    }

    /**
    * Returns true if this list is empty
    *
    * @return boolean
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    */
    public function isEmpty() {
        return $this->count == 0;
    }

    /**
    * Returns the id of the current list item or null
    *
    * @return boolean
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function key() {
        if (!$this->valid()) {
            return null;
        }

        $current = $this->_current();
        return $current->id;
    }

    /**
    * Moves to the next list item if exists
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function next() {
        if ($this->isEmpty()) {
            $this->flag = self::$FLAG_POS_END;
        } else if ($this->flag == self::$FLAG_POS_START) {
            $this->flag = self::$FLAG_NONE;
        } else if ($this->item->next) {
            $this->item = $this->item->next;
            $this->flag = self::$FLAG_NONE;
        } else {
            $this->flag = self::$FLAG_POS_END;
        }
    }
    public function getFlag() {
        return $this->flag;
    }
    /**
    * Returns true if an element at the position of index exists if $index is a numeric value
    * otherwise it returns true if an AppKitLinkedListItem with the $id == $index exists
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function offsetExists($index) {
        if (is_numeric($index)) {
            if ($index <0) {
                return false;
            }

            return $index < $this->count();
        }

        $start = $this->_top();

        do {
            if ($start->id == $index) {
                return true;
            }

            $start = $start->next;
        } while ($start);

        return false;
    }
    /**
     * Internal method that returns either the element at the current position $index (if $index is numeric), otherwise
     * returns the AppKitLinkedListItem with the id $index - or null if the offset doesn't exist
     *
     * @param    String|Numeric The offset or id to return
     * @return   AppKitLinkedListItem|Null
     * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
     **/
    public function _offsetGet($index) {
        if (!$this->offsetExists($index)) {
            return null;
        }

        $start = $this->_top();

        if (is_numeric($index)) {
            while ($index--) {
                $start = $start->next;
            }

            return $start;
        }

        do {
            if ($start->id == $index) {
                return $start;
            }

            $start = $start->next;
        } while ($start);

        return null;
    }

    /**
    * Returns either the element at the current position $index (if $index is numeric), otherwise
    * returns the content at the id $index - or null if the offset doesn't exist
    *
    * @param    String|Numeric The offset or id to return
    * @return   mixed|Null
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function offsetGet($index) {
        if (!$this->offsetExists($index)) {
            return null;
        }

        $start = $this->_top();

        if (is_numeric($index)) {
            while ($index--) {
                $start = $start->next;
            }

            return $start->value;
        }

        do {
            if ($start->id == $index) {
                return $start->value;
            }

            $start = $start->next;
        } while ($start);

        return null;
    }

    /**
    * Replaces the value at $index (id or offset, @see offsetExists) with $newval
    * Side effects: Forces rewind
    *
    * @param    String|Numeric  The offset or id to replace
    * @param    mixed           The value to replace with
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function offsetSet($index,$newval) {
        if (!($newval instanceof IAppKitLinkedListItem)) {
            $newval = new $this->class($newval);
        }

        $original = $this->_offsetGet($index);

        if (!$original) {
            return false;
        }

        $prev = $original->previous;
        $prev->next = $newval;
        $newval->next = $original->next;


        $original->next = null;
        $this->rewind();
    }

    /**
    * Removes the value at $index (id or offset, @see offsetExists)
    * Side effects: Forces rewind
    *
    * @param String|Numeric The offset to remove
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function offsetUnset($index) {
        $original = $this->_offsetGet($index);

        if (!$original) {
            return false;
        }

        $prev = $original->previous;

        if ($original->next) {
            $original->next->previous = $prev;
        }

        if ($prev) {
            $prev->next = $original->next;
        } else { // set prev to next for the case that index is the current item
            $prev = $original->next;
        }

        if ($this->item == $original) {
            $this->item = $prev;
        }

        $original->next = null;
        $this->count--;
        $this->rewind();
    }

    /**
    * Same like @offsetSet, but appends newval after the element at $index (if found)
    *
    * @param    String|Numeric  The offset or id to replace
    * @param    mixed           The value to replace with
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function offsetPush($index,$newval) {
        if (!($newval instanceof IAppKitLinkedListItem)) {
            $newval = new $this->class($newval);
        }

        $original = $this->_offsetGet($index);

        if (!$original) {
            return false;
        }

        $next = $original->next;;
        $newval->previous = $original;
        $original->next = $newval;

        if ($next) {
            $next->previous = $newval;
            $newval->next = $next;
        }
    }

    /**
    * Same like @offsetSet, but appends newval before the element at $index (if found)
    *
    * @param    String|Numeric  The offset or id to replace
    * @param    mixed           The value to replace with
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function offsetUnshift($index,$newval) {
        if (!($newval instanceof IAppKitLinkedListItem)) {
            $newval = new $this->class($newval);
        }

        $original = $this->_offsetGet($index);

        if (!$original) {
            return false;
        }

        $prev = $original->previous;
        $newval->next = $original;
        $original->previous = $newval;

        if ($prev) {
            $prev->next = $newval;
            $newval->previous = $prev;
        }
    }


    /**
    * Removes the last item in the linked list ad returns it. If the list is empty, null will be returned
    *
    * @return Mixed The last item of the list or null
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function pop() {
        $last = $this->_bottom();

        if (!$last) {
            return null;
        }

        if ($last->previous) {
            $last->previous->next = null;

            if ($this->item == $last) {
                $this->item = $last->previous;
            }
        } else {
            $this->item = null;
        }

        $this->count--;
        $this->rewind();
        return $last->value;
    }

    /**
    * Moves current one item forward in the list
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function prev() {
        if ($this->isEmpty()) {
            $this->flag = self::$FLAG_POS_START;
        } else if ($this->flag == self::$FLAG_POS_END) {
            $this->flag = self::$FLAG_NONE;
        } else if ($this->item->previous) {
            $this->item = $this->item->previous;
        } else {
            $this->flag = self::$FLAG_POS_START;
        }
    }

    /**
    * Pushes $value at the end of the list
    *
    * @param Mixed The object/value to push at the end
    **/
    public function push($value) {
        if (!($value instanceof IAppKitLinkedListItem)) {
            $value = new $this->class($value);
        }

        if ($this->isEmpty()) {
            $this->item = $value;
            $this->flag = self::$FLAG_NONE;
        } else {
            $end = $this->_bottom();
            $end->next = $value;
            $value->previous = $end;

            if ($this->flag == self::$FLAG_POS_END) {
                $this->flag = self::$FLAG_NONE;
            }
        }

        $this->count++;
    }

    /**
    * Rewinds the list by clearing all flags and putting current to the top
    * of the list
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function rewind() {
        $this->item = $this->_top();
        $this->flag = self::$FLAG_NONE;
    }

    /**
    * Not use, we only support list mode with FIFO
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function setIteratorMode($mode) {
        /* ignore */
    }

    /**
    * Removes the first item (if list is not empty) and returns it
    * Side efects: Forces rewind
    *
    * @return Mixed|Null    The value that was formerly on the top
    * @author: Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function shift() {
        if ($this->isEmpty()) {
            return null;
        }

        $top = $this->_top();

        if ($top->next) {
            $top->next->previous = null;
        }

        if ($top == $this->item) {
            $this->item = $top->next;
        }

        $top->next = null;
        $this->count--;
        $this->rewind();
        return $top->value;
    }

    /**
    * Internal function to retrieve raw AppKitLinkedListItem at top
    *
    * @return AppKitLinkedListItem|Null
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    private function _top() {
        if ($this->isEmpty()) {
            return null;
        }

        $cur = $this->item;

        while ($cur->previous) {
            $cur = $cur->previous;
        }

        return $cur;
    }


    /**
    * Returns the first item of the list or null if list is empty
    *
    * @return Mixed|Null    First item of the list or null
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function top() {
        $cur = $this->_top();

        if ($cur) {
            return $cur->value;
        }

        return null;
    }

    /**
    * Prepends $value to the list
    *
    * @param Mixed  The value to prepend
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function unshift($value) {
        if (!($value instanceof IAppKitLinkedListItem)) {
            $value = new $this->class($value);
        }

        if ($this->isEmpty()) {
            $this->item = $value;
            $this->flag = self::$FLAG_NONE;
        } else {
            $this->item->previous = $value;

            $value->next = $this->item;
        }

        if ($this->flag == self::$FLAG_POS_START) {
            $this->flag = self::$FLAG_NONE;
        }

        $this->count++;
    }
    /**
    * Returns true if the list is in a valid state (i.e. not before the first item,
    * not after the last item and with at least one item)
    *
    * @return Boolean
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function valid() {
        return $this->item != NULL && $this->flag == self::$FLAG_NONE;
    }
    public function toString() {
        $str = "START=>";
        foreach($this as $id=>$val) {
            $str .= "{".$id."=".print_r($val,true)."}=>";
        }
        $str .= "END";
        return $str;
    }

    public function __toString() {
        return $this->toString();
    }

    public function toArray() {
        $arr = array();
        foreach($this as $id=>$val) {
            $arr[] = $val;
        }
        return $arr;
    }
}


