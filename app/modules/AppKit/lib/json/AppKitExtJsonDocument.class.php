<?php

/**
 * To handling auto store configuration from agavi view
 * you can use this class to do this
 * @author mhein
 *
 */
class AppKitExtJsonDocument extends AppKitArrayContainer {

    // -- CONSTANTS
    /*
     * EXT JS config
     */
    const PROPERTY_ID				= 'idProperty';
    const PROPERTY_ROOT				= 'root';
    const PROPERTY_TOTAL			= 'totalProperty';
    const PROPERTY_SUCCESS			= 'successProperty';
    const PROPERTY_FIELDS			= 'fields';
    const PROPERTY_META				= 'metaData';
    const PROPERTY_SORTINFO			= 'sortInfo';
    const PROPERTY_START			= 'start';
    const PROPERTY_LIMIT			= 'limit';

    /*
     * Object config
     */
    const ATTR_NOMETA				= 'no-metadata';
    const ATTR_AUTODISCOVER			= 'field-autodiscover';

    protected $meta					= array();
    protected $rows					= array();
    protected $fields				= array();
    protected $doc					= array();
    protected $defaults				= array();
    protected $attributes			= array();
    protected $misc                 = array();

    // -- STATIC --

    /**
     * @var ReflectionClass
     */
    protected static $reflection	= null;

    protected static $meta_values	= array();
    protected static $attr_values	= array();


    public static function initializeStaticData() {
        static $run=false;

        if ($run===false) {

            self::$reflection = new ReflectionClass(__CLASS__);

            foreach(self::$reflection->getConstants() as $cname=>$cval) {

                list($ctype,$ctypeid) = explode('_', $cname, 2);

                switch ($ctype) {
                    case 'ATTR':
                        self::$attr_values[] = $cval;
                        break;

                    case 'PROPERTY':
                        self::$meta_values[] = $cval;
                        break;
                }
            }

            $run=true;
        }
    }

    public static function checkAttributeConstantValue($value) {
        return self::checkConstantValue($value, self::$attr_values);
    }

    public static function checkMetaConstantValue($value) {
        return self::checkConstantValue($value, self::$meta_values);
    }

    public static function checkConstantValue($value, array &$store) {
        if (in_array($value, $store) === true) {
            return true;
        }

        throw new AppKitExtJsonDocumentException('Value not defined by constant: '. $value);
    }

    // -- CLASS --

    public function  __construct() {
        $this->initArrayContainer($this->rows);

        $this->resetDoc();

        $this->docDefaults();
    }

    public function setMeta($key, $value=null) {
        if (self::checkMetaConstantValue($key)) {
            $this->meta[$key] = $value;
            return true;
        }
    }

    public function setAttribute($attr, $value=true) {
        if (self::checkAttributeConstantValue($attr)) {
            $this->attributes[$attr] = $value;
            return true;
        }
    }

    public function unsetAttribute($attr) {
        if (self::checkAttributeConstantValue($attr) && array_key_exists($attr, $this->attributes)) {
            unset($this->attributes[$attr]);
            return true;
        }
    }

    public function hasAttribute($attr) {
        return isset($this->attributes[$attr]) ? true : false;
    }

    public function setDefault($key, $val=null) {
        $this->defaults[$key] = $val;
    }

    public function setSuccess($success=true) {
        $this->setDefault(self::PROPERTY_SUCCESS, $success);
    }

    public function setSortinfo($field, $direction='asc') {
        $this->setMeta(self::PROPERTY_SORTINFO, array(
                           'direction'	=> strtolower($direction),
                           'field'		=> $field
                       ));
    }

    public function hasField($name, array $options=array()) {

        if (isset($options['mapping'])) {
            $name = $options['mapping'];
        }

        $options['name'] = $name;

        if (!array_key_exists('sortType', $options)) {
            $options['sortType'] = AppKitExtDataInterface::EXT_SORT_TEXT;
        }

        $this->fields[$name] = $options;
        return true;
    }

    public function hasFieldBulk(array $field_names, array $options=array(), $autoDetect=true) {

        if (array_key_exists('sortType', $options) && $autoDetect) {
            $autoDetect=false;
        }

        foreach($field_names as $field_name=>$field_value) {
            if ($autoDetect) {
                if (is_bool($field_value)) {
                    $options['sortType'] = AppKitExtDataInterface::EXT_SORT_INT;
                }

                elseif(is_float($field_value)) {
                    $options['sortType'] = AppKitExtDataInterface::EXT_SORT_FLOAT;
                }
                elseif(is_int($field_value)) {
                    $options['sortType'] = AppKitExtDataInterface::EXT_SORT_INT;
                }
                else {
                    $options['sortType'] = AppKitExtDataInterface::EXT_SORT_TEXT;
                }
            }

            $this->hasField($field_name, $options);
        }
    }

    public function applyFieldsFromDoctrineRelation(Doctrine_Table $table) {

        foreach($table->getColumns() as $column=>$meta) {
            $options = array(
                           'sortType' => AppKitExtDataInterface::doctrineColumn2ExtSortType($meta['type'])
                       );

            if (isset($meta['primary']) && $meta['primary'] == true) {
                $this->setMeta(self::PROPERTY_ID, $column);
            }

            $this->hasField($column, $options);
        }

    }

    public function addDataCollection(Doctrine_Collection $collection) {
        foreach($collection as $record) {
            $this->offsetSet(null, $record->toArray());
        }
    }

    public function offsetSet($offset, $value) {

        if ($offset !== null) {
            throw new AppKitExtJsonDocumentException('$offset must be <null> - always!');
        }

        if (!is_array($value)) {
            throw new AppKitExtJsonDocumentException('$value must be an associative array!');
        }

        $diff = array_diff_key($value, $this->fields);



        if (is_array($diff) && count($diff)>0) {
            if ($this->hasAttribute(self::ATTR_AUTODISCOVER)) {
                $this->hasFieldBulk($diff);
            } else {
                //throw new AppKitExtJsonDocumentException('$value keys does not match field data set!');
            }
        }

        // Store needs id maybe
        $this->addIDField($value);

        parent::offsetSet(null, $value);
    }

    private function addIDField(array &$value) {
        $idf = $this->meta[self::PROPERTY_ID];

        if (!array_key_exists($idf, $this->fields)) {
            $this->hasField($idf);
        }

        if (!array_key_exists($idf, $value)) {
            $value[$idf] = (count($this->rows) +1);
        }
    }

    public function setData(array $data) {
        foreach($data as $row) {
            $this->offsetSet(null, $row);
        }
    }
    
    /**
     * Add any data to output structure
     * @param string $name
     * @param mixed $data
     */
    public function addMiscData($name, $data) {
        $this->misc[$name] = $data;
    }

    public function resetDoc() {
        $this->setSuccess(false);
        $this->setMeta(self::PROPERTY_TOTAL, 0);
        $this->doc = array();
        $this->rows = array();
    }

    public function getDoc() {
        if (count($this->doc)<1) {
            $this->buildDoc();
        }

        return $this->doc;
    }

    public function getJson() {
        return json_encode($this->getDoc());
    }

    public function  __toString() {
        return $this->getJson();
    }

    protected function buildDoc() {
        $doc =& $this->doc;

        if ($this->hasAttribute(self::ATTR_NOMETA) == false) {

            $doc[self::PROPERTY_META] = array();

            $meta =& $doc[self::PROPERTY_META];

            foreach($this->meta as $k=>$v) {
                $meta[$k] = $v;
            }

            $meta[self::PROPERTY_FIELDS] = array_values($this->fields);

            if ($this->defaults[self::PROPERTY_TOTAL] == 0) {
                $this->setDefault(self::PROPERTY_TOTAL, count($this->rows));
            }

            foreach($this->defaults as $k=>$v) {
                if (isset($this->meta[$k])) {
                    $doc[$this->meta[$k]] = $v;
                } else {
                    $doc[$k] = $v;
                }
            }

        }

        $doc[$this->meta[self::PROPERTY_ROOT]] = $this->rows;
        
        foreach ($this->misc as $name=>$data) {
            $doc[$name] = $data;
        } 
    }

    protected function docDefaults() {
        $this->setMeta(self::PROPERTY_ID, 'id');
        $this->setMeta(self::PROPERTY_ROOT, 'rows');
        $this->setMeta(self::PROPERTY_SUCCESS, 'success');
        $this->setMeta(self::PROPERTY_TOTAL, 'total');
        // $this->setMeta(self::PROPERTY_SORTINFO, new stdClass());
        $this->setSuccess(false);
        $this->setDefault(self::PROPERTY_TOTAL, 0);
    }

}

// Lazy initialisation
AppKitExtJsonDocument::initializeStaticData();

class AppKitExtJsonDocumentException extends AppKitException {}
