<?php
class IcingaDoctrine_Query extends Doctrine_Query {
    
    /**
     * Enter description here ...
     * @param mixed $conn
     * @param mixed $class
     * @return IcingaDoctrine_Query
     */
    public static function create($conn = NULL, $class = NULL) {
        $manager = Doctrine_Manager::getInstance();
        
        if (!($conn instanceof Doctrine_Connection) && $conn) {
            $conn = $manager->getConnection($conn);
        } else {
            $conn = $manager->getConnection('icinga');
        }
        
        $conn_name = $manager->getConnectionName($conn);
        
        AgaviContext::getInstance()->getLoggerManager()->log('QUERY::CREATE Obtain doctrine connection: '. $conn_name, AgaviLogger::DEBUG);
        
        return parent::create($conn, 'IcingaDoctrine_Query');
    }
    
    /**
     * To disable the hydrator fixing feature. In some cases it is needed
     * to produce 'real' distinct queries
     * @var boolean
     */
    protected $_disableAutoIdentifiedFields = false;
    
    /**
    * @see Doctrine_Query::processPendingFields
    *
    * extended to automatically add grouping fields
    *
    * @access private
    **/
    public function processPendingFields($componentAlias) {
        $tableAlias = $this->getSqlTableAlias($componentAlias);
        $table = $this->_queryComponents[$componentAlias]['table'];

        if (! isset($this->_pendingFields[$componentAlias])) {
            if ($this->_hydrator->getHydrationMode() != Doctrine_Core::HYDRATE_NONE) {
                if (! $this->_isSubquery && $componentAlias == $this->getRootAlias()) {
                    $ids = $table->getIdentifierColumnNames();
                    $this->_pendingFields[$componentAlias][] = $ids[0];
                }
            }

            return;
        }

        // At this point we know the component is FETCHED (either it's the base class of
        // the query (FROM xyz) or its a "fetch join").
        // Check that the parent join (if there is one), is a "fetch join", too.
        if (! $this->isSubquery() && isset($this->_queryComponents[$componentAlias]['parent'])) {
            $parentAlias = $this->_queryComponents[$componentAlias]['parent'];

            if (is_string($parentAlias) && ! isset($this->_pendingFields[$parentAlias])
                && $this->_hydrator->getHydrationMode() != Doctrine_Core::HYDRATE_NONE
                && $this->_hydrator->getHydrationMode() != Doctrine_Core::HYDRATE_SCALAR
                && $this->_hydrator->getHydrationMode() != Doctrine_Core::HYDRATE_SINGLE_SCALAR) {
                $ids = $this->_queryComponents[$parentAlias]['table']->getIdentifierColumnNames();
                $this->_pendingFields[$parentAlias][] = $ids[0];
            }
        }

        $fields = $this->_pendingFields[$componentAlias];

        // check for wildcards
        if (in_array('*', $fields)) {
            $fields = $table->getFieldNames();
        } else {
            $driverClassName = $this->_hydrator->getHydratorDriverClassName();

            // only auto-add the primary key fields if this query object is not
            // a subquery of another query object or we're using a child of the Object Graph
            // hydrator
            if (! $this->_isSubquery && is_subclass_of($driverClassName, 'Doctrine_Hydrator_Graph') && $this->_disableAutoIdentifiedFields == false) {
                $fields = array_unique(array_merge((array) $table->getIdentifier(), $fields));
            }
        }

        $sql = array();
        foreach($fields as $fieldName) {
            $columnName = $table->getColumnName($fieldName);

            if (($owner = $table->getColumnOwner($columnName)) !== null &&
                    $owner !== $table->getComponentName()) {

                $parent = $this->_conn->getTable($owner);
                $columnName = $parent->getColumnName($fieldName);
                $parentAlias = $this->getSqlTableAlias($componentAlias . '.' . $parent->getComponentName());
                $sql[] = $this->_conn->quoteIdentifier($parentAlias) . '.' . $this->_conn->quoteIdentifier($columnName)
                         . ' AS '
                         . $this->_conn->quoteIdentifier($tableAlias . '__' . $columnName);
            } else {
                $columnName = $table->getColumnName($fieldName);
                $sql[] = $this->_conn->quoteIdentifier($tableAlias) . '.' . $this->_conn->quoteIdentifier($columnName)
                         . ' AS '
                         . $this->_conn->quoteIdentifier($tableAlias . '__' . $columnName);
            }
        }

        $this->_neededTables[] = $tableAlias;
        $this->updateGroupFields($sql);
        return implode(', ', $sql);

    }

    protected function updateGroupFields($sql) {
        $groups = &$this->_sqlParts["groupby"];
        $hasAggregates = false;
        foreach($this->_sqlParts['select'] as $select) {
            if (preg_match("/(COUNT\(|AVG\(|MIN\(|MAX\()/i",$select)) {
                $hasAggregates= true;
                break;
            }
        }

        if (!$hasAggregates) {
            return;
        }

        foreach($sql as $i)
        $this->_sqlParts["groupby"][] = preg_replace("/(.*?) *AS.*$/","$1",$i);

        $groups = array_unique($groups);
    }

    protected $defaultJoinType = "left";
    protected $aliasDefs = array();
    protected $mainAlias = "my";
    private $aliasJoins = array();

    public function setAliasDefs($mainAlias,array $defs) {
        $this->aliasDefs = $defs;
        $this->mainAlias = $mainAlias;
    }

    public function setDefaultJoinType($type) {
        $this->defaultJoinType = $type;
    }

    public function getAliasDefs() {
        return $this->aliasDefs;
    }

    public function getDefaultJoinType() {
        return $this->defaultJoinType;
    }

    public function addAliasJoin($join) {
        if (isset($join["alwaysSelect"])) {
            $this->addSelect($join["alias"].".".$join["alwaysSelect"]);
        }

        $joinTxt = "";

        if (isset($join["relation"])) {
            $joinTxt = $join["src"].".".$join["relation"]." ".$join["alias"];
        }

        if (isset($join["with"])) {
            $joinTxt .= " WITH ".$join["with"];
        }

        if (isset($join["on"])) {
            $joinTxt .= " ON ".$join["on"];
        }


        if (!isset($join["type"])) {
            $join["type"] = $this->defaultJoinType;
        }

        if ($join["type"] == "inner") {
            $this->innerJoin($joinTxt);
        } else {
            $this->leftJoin($joinTxt);
        }

    }

    protected function checkForAlias(&$statement,$ignore = array()) {
        $regExp = "/(?<alias>\w+)\.(?<field>[\*A-Za-z]+)/";
        $matches = array();
        preg_match_all($regExp,$statement,$matches);

        for ($i=0; $i<count($matches["alias"]); $i++) {
            if($matches["alias"] == $this->mainAlias) {
                $resolved = explode(".",$statement,2);
                $statement = $resolved[1];
            }
                
            if (in_array($matches["alias"][$i],$ignore)) {
                continue;
            }

            $this->addAlias($matches["alias"][$i]);
        }
    }

    /**
    * Adds a join definition to this TargetModifier defined by an alias
    * in @see aliasDefs
    *
    * @param String The alias to register a join for
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function addAlias($alias) {

        if ($alias == $this->mainAlias) {
            return true;    //ignore base table alias
        }

        if (!isset($this->aliasDefs[$alias])) {
            return true; //throw new AppKitException("Tried to access hoststore field with invalid alias $alias (Mainalias: $this->mainAlias)");
        }

        $join = $this->aliasDefs[$alias];
        $join["alias"] = $alias;

        if (isset($this->aliasJoins[$alias])) {
            return true;    // already added
        }

        // Add source alias
        if (isset($join["src"]) && $join["src"] != $this->mainAlias) {
            $this->addAlias($join["src"]);
        }

        $this->aliasJoins[$alias] = true;

        $this->addAliasJoin($join);
    }

    protected function addDefaultJoins() {
        foreach($this->aliasDefs as $key=>$alias) {
            if (isset($alias["alwaysJoin"])) {
                $this->addAlias($key);
            }
        }
    }

    public function autoResolveAliases() {
        $this->addDefaultJoins();
        foreach($this->_dqlParts as &$dqlGroup) {
            if (is_array($dqlGroup))
                foreach($dqlGroup as &$dql) {
                    $this->checkForAlias($dql,array($this->mainAlias));
            } else {
                $this->checkForAlias($dql,array($this->mainAlias));
            }
        }
    }
    
    /**
     * Tries to find the alias name used in the query
     * @todo Search over joins too
     * @param string $componentName
     * @return string
     */
    protected function findAliasByComponent($componentName) {
        $alias = null;
        
        foreach ($this->_dqlParts['from'] as $from) {
            if (!(strstr($from, $componentName)!==false)) {
                $alias = $from;
            }
        }
        
        if ($alias) {
            $arry = explode(' ', $alias);
            return $arry[1];
        }
        
        return null;
    }
    
    /**
     * Appends a custom variable filter to doctrine query
     * @param string $alias
     * @return IcingaDoctrine_Query
     */
    public function appendCustomvarFilter($alias=null) {
        if ($alias === null) {
            $alias = $this->findAliasByComponent('IcingaCustomVars');
        }
        
        if ($alias) {
            $exclude = AgaviConfig::get('modules.api.exclude_customvars');
            if (is_array($exclude) && count($exclude)) {
                $this->andWhereNotIn($alias. '.varname', $exclude);
            }
        }
        return $this;
    }
    
    /**
     * To produce real distinct query this function
     * can disable to autoid feature
     * @param boolean $flag
     * @return IcingaDoctrine_Query
     */
    public function disableAutoIdentifierFields($flag) {
        $this->_disableAutoIdentifiedFields = (boolean)$flag;
        return $this;
    }


}
?>
