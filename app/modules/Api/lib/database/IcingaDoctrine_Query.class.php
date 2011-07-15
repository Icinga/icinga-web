<?php
class IcingaDoctrine_Query extends Doctrine_Query {
    public static function create($conn = NULL, $class = NULL) {
         
        return new IcingaDoctrine_Query($conn);
    }
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

        if ( ! isset($this->_pendingFields[$componentAlias])) {
            if ($this->_hydrator->getHydrationMode() != Doctrine_Core::HYDRATE_NONE) {
                if ( ! $this->_isSubquery && $componentAlias == $this->getRootAlias()) {
                    
                    $ids = $table->getIdentifierColumnNames();
                    $this->_pendingFields[$componentAlias][] = $ids[0];  
                }
            }
            return;
        }

        // At this point we know the component is FETCHED (either it's the base class of
        // the query (FROM xyz) or its a "fetch join").

        // Check that the parent join (if there is one), is a "fetch join", too.
        if ( ! $this->isSubquery() && isset($this->_queryComponents[$componentAlias]['parent'])) {
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
            if ( ! $this->_isSubquery && is_subclass_of($driverClassName, 'Doctrine_Hydrator_Graph')) {
                $fields = array_unique(array_merge((array) $table->getIdentifier(), $fields));
            }
        }

        $sql = array();
        foreach ($fields as $fieldName) {
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
            if(preg_match("/(COUNT\(|AVG\(|MIN\(|MAX\()/i",$select)) {
                $hasAggregates= true;
                break;
            }
        }
        if(!$hasAggregates)
            return;
        
        foreach($sql as $i)
            $this->_sqlParts["groupby"][] = preg_replace("/(.*?) *AS.*$/","$1",$i);
        
        $groups = array_unique($groups);
    }
/*
    public function execute($attr,$hyd) {
        ApiDataRequestBaseModel::applySecurityCredentials($this);
        return parent::execute($attr,$hyd);
    }
**/
  
}
?>
