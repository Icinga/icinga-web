<?php

class AppKitEvent extends AppKitBaseClass {

    const UNTOUCHED	= 0;
    const TOUCHED	= 1;
    const CANCELLED	= 2;
    const RESUMED	= 4;

    /**
     * The name of the event
     * @var string
     */
    private $name	= null;

    /**
     * Some information about why the event was fired
     * @var string
     */
    private $info	= null;

    /**
     * Some additional mixed data
     * @var array
     */
    private $data	= array();

    /**
     * A context object within the event was fired
     * @var object
     */
    private $object	= null;

    /**
     * Binary flag indicates the status
     * @var integer
     */
    private $status	= self::UNTOUCHED;

    /**
     * A run counter, how much the event was chained
     * @var integer
     */
    private $crun	= 0;


    /**
     * Constructor of the event
     * @param string $name
     * @param 0object $obj
     * @param string $info
     * @author Marius Hein
     */
    public function __construct($name, &$obj=null, $info=null) {
        $this->setName($name);

        if($obj !== null && is_object($obj)) {
            $this->setObject($object);
        }

        if($info !== null) {
            $this->setInfo($info);
        }
    }

    /**
     * Sets the name of the event
     * @param string $name
     * @author Marius Hein
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Returns the name
     * @return string
     * @author Marius Hein
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the info string
     * @param string $info
     * @author Marius Hein
     */
    public function setInfo($info) {
        $this->info = $info;
    }

    /**
     * Returns the info
     * @return string
     * @author Marius Hein
     */
    public function getInfo() {
        return $this->info;
    }

    /**
     * Sets a context object
     * @param object $object
     * @throws AppKitEventException
     * @author Marius Hein
     */
    public function setObject($object) {
        if(!is_object($object)) {
            throw new AppKitEventException('$object have to be an object!');
        }

        $this->object = $object;
    }

    /**
     * Returns the object
     * @return object
     * @author Marius Hein
     */
    public function getObject() {
        return $this->object;
    }

    /**
     * Adds data to the data stack
     * @param mixed $data
     * @param string|null $key
     * @author Marius Hein
     */
    public function addData($data, $key=null) {
        if($key !== null) {
            $this->data[$key] = $data;
        } else {
            $this->data[] = $data;
        }
    }

    /**
     * Fills the complete array, overwrite everything
     * @param array $data
     * @return boolean always true
     */
    public function setData(array &$data) {
        $this->data =& $data;
        return true;
    }

    /**
     * Returns the data as array
     * @return array
     * @author Marius Hein
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Returns true if a status flag is set
     * @param integer $status_flag
     * @return boolean
     * @author Marius Hein
     */
    public function issetStatus($status_flag) {
        if($this->status & $status_flag) {
            return true;
        }

        return false;
    }

    /**
     * Sets a status flag
     * @param integer $status_flag
     * @author Marius Hein
     */
    public function setStatus($status_flag) {
        $this->status |= $status_flag;
    }

    /**
     * Unsets a status flag
     * @param integer $status_flag
     * @author Marius Hein
     */
    public function unsetStatus($status_flag) {
        if($this->status & $status_flag) {
            $this->status = ($this->status & ~ $status_flag);
        }
    }

    /**
     * Returns the decimal representation of the status
     * @return integer
     * @author Marius Hein
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Touch the event
     * @return boolean
     * @author Marius Hein
     */
    public function touch() {
        $this->crun++;
        $this->setStatus(self::TOUCHED);
        return true;
    }

    /**
     * Cancel the event (prevent from chaining)
     * @return boolean
     * @author Marius Hein
     */
    public function cancel() {
        if($this->issetStatus(self::RESUMED)) {
            $this->unsetStatus(self::RESUMED);
        }

        $this->setStatus(self::CANCELLED);
        return true;
    }
}

class AppKitEventException extends AppKitException {}

?>