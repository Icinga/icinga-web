<?php

class AppKitJsonResponse extends AppKitBaseClass {

    const NS_ERROR		= 'errors';
    const NS_MESSAGE	= 'messages';
    const NS_DATA		= 'data';
    const NS_STATUS		= 'status';

    const STATUS_OK		= 0;
    const STATUS_NOK	= 1;

    private $data	= array();

    public function __construct($status = null) {
        if($status !== null) {
            $this->setStatus($status);
        }
    }

    public function addToResponse($namespace, $value, $key=null) {
        if(!is_array($this->data[$namespace])) {
            $this->data[$namespace] = array();
        }

        if($key === false) {
            $this->data[$namespace] = $value;
        }

        elseif($key !== null) {
            $this->data[$namespace][$key] = $value;
        }
        else {
            $this->data[$namespace][] = $value;
        }

        return true;
    }

    public function setStatus($status) {
        return $this->addToResponse(self::NS_STATUS, $status, false);
    }

    public function addError($msg) {
        return $this->addToResponse(self::NS_ERROR, $msg);
    }

    public function addMessage($msg) {
        return $this->addToResponse(self::NS_MESSAGE, $msg);
    }

    public function addData($key, $val) {
        return $this->addToResponse(self::NS_DATA, $val, $key);
    }

    public function toArray() {
        return $this->data;
    }

    public function toJson() {
        return json_encode($this->toArray());
    }

    public function toString() {
        return $this->toJson();
    }


}

?>