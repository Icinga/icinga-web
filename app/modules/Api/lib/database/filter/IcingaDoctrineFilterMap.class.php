<?php 
/**
 * Doctrine filtermap object. Inject principal models (IcingaContactgroup, IcingaHostgroup, ...) into doctrine
 * queries
 * @author mhein
 *
 */
class IcingaDoctrineFilterMap {
    
    /**
     * New alias space to create our own aliases
     * @var string
     */
    const ALIAS_PREFIX = 'res';
    
    /**
     * Dotted notation of sub component path where 
     * the map will operate on
     * @var string
     */
    private $componentPath = null;
    
    /**
     * Start relation to bind the join path
     * @var string
     */
    private $startRelation = null;
    
    /**
     * Hashtable of fields. To translate principal var names into 
     * db model names
     * @var array[string]
     */
    private $fields = null;
    
    /**
     * System wide alias counter to reduce double alias
     * exceptions
     * @var integer
     */
    private static $aliasCounter = 0;
    
    /**
     * Last used alias if we need to work on
     * @var string
     */
    private $lastAlias = null;
    
    /**
     * Do some magic what magic contructors do
     * @param string $componentPath
     * @param string $field1
     */
    public function __construct($componentPath=null, array $fieldMap) {
        $args = func_get_args();
        $this->setComponentPath(array_shift($args));
        $this->fields = new ArrayObject();
        
        $this->fields = $fieldMap + (array)$this->fields;
    }
    
    /**
     * 
     * Setter for component path
     * e.g. 'hostgroups.object'
     * @param string $path
     */
    public function setComponentPath($path) {
        $this->componentPath = $path;
    }
    
    /**
     * Setter for fields
     * @param array $fields
     */
    public function setField(array $fields) {
        $this->fields = $fields;
    }
    
    /**
     * Workhorse: Dispatcher for the private implementations
     * @param string $startRelation
     * @param Doctrine_Collection $values
     * @param Doctrine_Query_Abstract $query
     * @return boolean
     */
    public function appendTargetValuesFilter($startRelation, Doctrine_Collection $values, Doctrine_Query_Abstract $query) {
        
        // Testing BEFORE we add some consumptive joins
        if (!$this->matchFilter($values)) {
            return false;
        }
        
        $alias = $this->createJoins($startRelation, $query);
        $this->createConditions($values, $query, $alias);
    }
    
    /**
     * If our configuration matches the principal values
     * @param Doctrine_Collection $values
     * @return boolean
     */
    private function matchFilter(Doctrine_Collection $values) {
        $retVal = false;
        foreach ($values as $value) {
            if (array_key_exists($value->tv_key, $this->fields)) {
                $retVal = true;
                break;
            }
        }
        
        return $retVal;
    }
    
    /**
     * Second step. Create where conditions in Doctrine style. This 
     * method groups the subfields together:
     * 	e.g.: a[0]=1, b[0]=2, a[1]=2, b[1]=5 => (a=1 and b=2) OR (a=2 and b=5)
     * This is needed for custom vars or any other multifield confitions
     * @param Doctrine_Collection $values
     * @param Doctrine_Query_Abstract $query
     * @param unknown_type $alias
     */
    private function createConditions(Doctrine_Collection $values, Doctrine_Query_Abstract $query, $alias) {
        $arrayStatements = array();
        $arrayValues = array();
        
        foreach ($values as $value) {
            $k = $value->tv_key;
            $v = $value->tv_val;
            
            if (!array_key_exists($k, $this->fields)) {
                continue;
            }
            
            if (!array_key_exists($k, $arrayStatements)) {
                $arrayStatements[$k] = array();
            }
            
            if (!array_key_exists($k, $arrayValues)) {
                $arrayValues[$k] = array();
            }
            
            $arrayStatements[$k][] = sprintf('%s.%s=?', $alias, $this->fields[$k]);
            $arrayValues[$k][] = $v;
        }
        
        
        $stateOut = array();
        $stateVal = array();
        
        while(true) {
            $stmtTmp = array();
            
            foreach ($this->fields as $fid=>$fname) {
                
                if (count($arrayStatements[$fid]) && count($arrayValues[$fid])) {
                
                    $stmtTmp[] = array_shift($arrayStatements[$fid]);
                    $stateVal[] = array_shift($arrayValues[$fid]);
                
                } else {
                    break 2;
                }
            }
            
            $stateOut[] = '('. implode(' and ', $stmtTmp). ')';
            
        }
        
        $statement = implode(' OR ', $stateOut);
        $query->andWhere($statement, $stateVal);
    }
    
    /**
     * Create some unique alias for Doctrine
     * @return string
     */
    private function genAlias() {
        return ($this->lastAlias = self::ALIAS_PREFIX. (++self::$aliasCounter));
    }
    
    /**
     * First step. Step into the component path and create all
     * join relations beginning by the startRelation
     * @param string $startRelation
     * @param Doctrine_Query_Abstract $query
     * @return string The new alias for the where conditions
     */
    private function createJoins($startRelation, Doctrine_Query_Abstract $query) {
        $path = explode('.', $this->componentPath);
        $startComponent = array_shift($path);
        $query->innerJoin($startRelation. '.'. $startComponent. ' '. $this->genAlias());
        foreach ($path as $sub) {
            $query->innerJoin($this->lastAlias. '.'. $sub. ' '. $this->genAlias());
        }
        return $this->lastAlias;
    }
}