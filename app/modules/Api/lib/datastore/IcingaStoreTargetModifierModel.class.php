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
    * Define aliases and their target relations depending on the target ('my') 
    * <code>
    *    protected $aliasDefs = array( 
    *       "i"     => array("src" => "my", "relation" => "instance"),
    *       "hs"    => array("src" => "my", "relation" => "status"),
    *       "chco"  => array("src" => "my", "relation" => "checkCommand"),
    *       "s"     => array("src" => "my", "relation" => "services"),
    *       "ss"    => array("src" => "s", "relation" => "status")
    *   ); 
    *
    * </code>
    * @var Array
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected $aliasDefs = array();
    
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
            
            if(isset($join["type"])) {
                if($join["type"] == "inner") { 
                    $o->innerJoin($join["src"].".".$join["relation"]." ".$join["alias"]);
                    continue;
                }
            }
            $o->leftJoin($join["src"].".".$join["relation"]." ".$join["alias"]);
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
