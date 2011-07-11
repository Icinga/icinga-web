<?php
/**
* Modifier that sets result columns in select, the table and joins in the 
* IcingaDoctrine_Query
*
*
* Exports the getFilter() and setFilter functions to the DataStore 
* @package Icinga_Api
* @category DataStoreModifier
*
* @author Jannis Moßhammer <jannis.mosshammer@netways.de>
**/
class IcingaStoreTargetModifierModel extends IcingaBaseModel implements IDataStoreModifier
{
    /**
    * @see IDataStoreModifier::getMappedArguments
    * @var Array
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected $mappedParameters = array(
        "target" => "target", 
        "fields" => "fields"
    );
    
    /**
    * Define fields that can be requested (only important for client side export) 
    * @var Array
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected $allowedFields = array();
    
    /**
    * Defines the name of the alias given to the table in the 'from' clause
    * @var String
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/ 
    protected $mainAlias = "my";
    
    /**
    * Define fields that will be requested by default (only important for client side export)
    * @var Array
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected $defaultFields = array();
    
    
    /**
    * Define fields that can be sorted by (only important for client side export)
    * @var Array
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected $sortFields = array();
    
    /**
    * Define fields that can be grouped by (only important for client side export)
    * @var Array
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected $groupFields = array();
    
    /**
    * Defines fields that will statically appended in the Where clause 
    * @var Array An array containing arrays with clause,value
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected $staticWhereConditions = array();

    /**
    * Define aliases and their target relations depending on the target ('my') 
    * <code>
    *    protected $aliasDefs = array( 
    *       "i"     => array("src" => "my", "relation" => "instance", "type" => "inner", "OR" => 'hs.state = 0', "AND" => "my.alias = 'test' "),
    *       "hs"    => array("src" => "my", "relation" => "status"),
    *       "chco"  => array("src" => "my", "relation" => "checkCommand"),
    *       "s"     => array("src" => "my", "relation" => "services"),
    *       "ss"    => array("src" => "s", "relation" => "status")
    *   ); 
    *   
    * </code>
    * @var Array With the aliases, which can contain the following fields:
    *   - src: (Required)           The origin alias name of the relation
    *   - relation: (Required)      The relation name as defined in the doctrine model
    *   - type: (Optional)          Either "inner" or "left", defines the join type. If none is set, @see defaultJoinType will be used
    *   - AND:  (Optional)          A string defining a join condition that will be added via (ex.: h.display_name = "PING") AND
    *   - OR:  (Optional)          A string defining a join conditions that will be added via (ex.: h.display_name = "PING") OR
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected $aliasDefs = array();

    /**
    * The default join type to use, either left or inner
    * @var String 
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected $defaultJoinType = "left";

    private $target = array();
    private $fields = array();
    private $joins = array();
   
    /**
    * Sets the target table 
    * @param String The table to request data from
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function setTarget($target) {
        $this->target = $target;
    }
    
    /**
    * Adds a simple 'where' clause to the end of the query in order to  
    * be able to limit the result set statically. 
    *
    * NOTE: Don't use this for dynamic filtering,
    * but the filterModifiers instead. It is intended to limit the base relation which can 
    * be additionally filtered by the filter modifiers
    *
    * @param    String  The condition (my.host_name = ?)
    * @param    mixed   The value to set for the clause
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function addStaticWhereField($condition,$value = null) {
        $this->staticWhereConditions[] = array($condition,$value);
    }
     
    /**
    * Returns the target table 
    * @return String The table this modifier sets
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function getTarget() {
        return $this->target;
    }  
    
    /**
    * Sets the columns to request from, alias definitions set in 
    * @see getAliasDefs will be resolved to joins
    * Example:
    * <code>
    *   obj.setFields(
    *       array(
    *           "display_name", // will be requested from the target column
    *           "my.host_id",   // will also be requested from the target column
    *           "s.service_id"  // will add an left join and request from the aliased column 
    *       )
    *   );
    * </code>
    * @param Array  Columns to request from  
    * @param Boolean Whether to check if an column alias with this name exists
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function setFields($fields, $useColumnAlias = false) {
        if(!is_array($fields))
            $fields = array($fields);
        $regExp = "/^(?<alias>\w+)\.(?<field>\w+)/";
        foreach($fields as $field) { 
            $aliasField = "";
            if($useColumnAlias && isset($this->columns[$field])) {
                $aliasField = $field; 
                $field = $this->columns[$field]; 
            }
            // check for alias 
            $match = array();
            preg_match($regExp,$field,$match);
            if(isset($match["alias"])) {
                $this->addAlias($match["alias"]);  
            } else {
                if($field[0] != '(')
                    $field = $this->mainAlias.".".$field; 
            }   
            $this->fields[] = $field;
            /*
            * workaround for doctrine alias bug 
            * See http://www.doctrine-project.org/jira/browse/DC-601
            * Because of this, all alias fields will be added additionaly to the original fields
            * instead of replacing them in the result set
            */
            if($aliasField) 
                $this->fields[] = $field." AS ".$aliasField;

        } 
    }
  
     
    /**
    * Return the result-columns defined in this modifier
    * @return Array the columns that will be requested
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function getFields() {
        return $this->fields;
    }
   
    /**
    * @see IDataStoreModifier::handleArgument
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/ 
    public function handleArgument($name,$value) {
        switch($name) {
            case 'target':
                $this->setTarget($value); 
                break;
            case 'fields': 
                $this->setFields($value);
                break;
         }
    } 
    
    /**
    * Returns fields that are allowed to be requested
    * @return Array
    * @access private
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function getAllowedFields() {
        return $this->allowedFields;
    }

    /**
    * Returns aliases and relations defined in this class
    * @return Array Alias definition of this class
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function getAliasDefs() {
        return $this->aliasDefs;
    }
    
    /**
    * Sets alias=>relation definitions
    * @param Array  Alias definitions
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function setAliasDefs(array $defs) {
        $this->aliasDefs = $defs;
    }
    
    /**
    * @see IDataStoreModifier::getMappedArguments
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/    
    public function getMappedArguments() {
        return $this->mappedParameters;
    }

    /**
    * @see IDataStoreModifier::modify
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function modify(&$o) {
        // type safe call
        $this->modifyImpl($o);
    }

    /**
    * @access private
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function modifyImpl(Doctrine_Query &$o) { 
        $o->select(implode(",",$this->fields));
         
        $o->from($this->target." ".$this->mainAlias);
        foreach($this->joins as $join) {
            if(isset($join["alwaysSelect"]))
                $o->addSelect($join["alias"].".".$join["alwaysSelect"]);
            $joinTxt = $join["src"].".".$join["relation"]." ".$join["alias"];
            if(isset($join["AND"]))
                $joinTxt .= "AND ".$join["AND"];
            if(isset($join["OR"]))
                $joinTxt .= "OR ".$join["OR"];
 
            if(!isset($join["type"])) 
                $join["type"] = $this->defaultJoinType;
            if($join["type"] == "inner") { 
                $o->innerJoin($joinTxt);
            } else {
                $o->leftJoin($joinTxt);
            }
        }
        foreach($this->staticWhereConditions as $cond) {
            if(isset($cond[1]) && $cond[1] != null)
                $o->addWhere($cond[0],$cond[1]);
            else
                $o->addWhere($cond[0]);
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
        if($alias == $this->mainAlias)
            return true; //ignore base table alias
        if(!isset($this->aliasDefs[$alias]))
            throw new AppKitException("Tried to access hoststore field with invalid alias $alias");
    
        $join = $this->aliasDefs[$alias];
        $join["alias"] = $alias;
        
        if(in_array($join,$this->joins))
            return true; // already added      
        // Add source alias
        if($join["src"] != $this->mainAlias)
            $this->addAlias($join["src"]);
        
        $this->joins[] = $join;
    }        
    
    /**
    * @see IDataStoreModifier::__getJSDescriptor
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function __getJSDescriptor() {
        return array(
            "type" => "fields",
            "allowedFields" => $this->allowedFields,
            "defaultFields" => $this->defaultFields,
            "sortFields" => $this->sortFields,
            "groupFields" => $this->groupFields,
            "params" => $this->getMappedArguments()
        );
    }
}

?>
