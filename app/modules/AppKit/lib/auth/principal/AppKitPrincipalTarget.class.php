<?php

/**
 * Base class for writing principals
 * @author mhein
 *
 */
abstract class AppKitPrincipalTarget {

    protected $fields		= array();
    protected $type			= null;
    protected $description	= null;

    public function __construct() {

    }

    public function getFields() {
        return $this->fields;
    }

    protected function setFields(array $a) {
        $this->fields = $a;
    }

    protected function setType($t) {
        $this->type = $t;
    }

    protected function setDescription($d) {
        $this->description = $d;
    }

}

class AppKitPrincipalTargetException extends AppKitException {}
