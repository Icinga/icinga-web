<?php

class InvalidApiQueryException extends Exception {};

interface IcingaApiRequestModifier {
	public function apply(Doctrine_Query $q,Api_ApiDataRequestDescriptorModel $request); 
}

interface I_IcingaApiFilter {
	public function getFilterString();
}

class IcingaApiFilter implements IcingaApiRequestModifier implements I_IcingaApiFilter {
	public const EQUAL 	= "=";
	public const NOT_EQUAL 	= "!=";
	public const GREATER	= ">";
	public const GREATER_EQ	= ">=";
	public const LOWER	= "<";
	public const LOWER_EQ	= "<=";
	public const LIKE 	= "LIKE";
	public const NOT_LIKE 	= "NOT LIKE";
	
	protected $field;
	protected $value;
	protected $type;
	protected $table;

	public function setField($field) {
		$this->field = $field;
	}
	public function getField() {
		return $this->field;
	}
	public function setValue($val) {
		$this->value = $val;
	}
	public function getValue() {
		return $this->value;
	}
	public function setType($type) {
		$this->type = $type;
	}
	public function getType() {
		return $this->type;
	}
	public function setTable($table) {
		$this->table = $table;
	}
	public function getTable() {
		return $this->table;
	}
	
	public function __construct($field,$value,$type = EQUAL,$table = NULL) {
		$this->setValue($value);
		$this->setField($field);
		$this->setType($type);
		$this->setTable($table);
	}
	
	
	public function apply(Doctrine_Query $q,Api_ApiDataRequestDescriptorModel $request); 
		throw InvalidApiQueryException("IcingaApiFilter must be encapsulated in a IcingaApiFilterGroup");
	} 
	
	public function getFilterString() {
		return ($this->table ? "_".$this->table : "origin").$this->field." ".$this->method." ".$this->value;
	} 
}

class IcingaApiFilterGroup implements I_IcingaApiFilter {
	protected $logicOperator;

	public function apply(Doctrine_Query $q,Api_ApiDataRequestDescriptorModel $request); 
		$request->setFilterString($this->getFilterString);		
	}

	public function getFilterString() {
		$str = "(";
		foreach($this->filters as $filter) {
			if($str != "(")
				$str .=  $this->logicOperator;
			$str .= filter->getFilterString();	
		}
		return $str.")";
	}
}
class IcingaApiANDFilterGroup extends IcingaApiFilterGroup {
	protected $logicOperator = "AND";
}

class IcingaApiORFilterGroup extends IcingaApiFilterGroup {
	protected $logicOperator = "OR";
}

class IcingaApiLimit implements ApiRequestModifier {
	protected $limit = 0;
	protected $offset = 0;
	public function __construct($limit, $offset = 0) {
		$this->limit = $limit;
		$this->offset = $offset;
	}
	
	public function apply(Doctrine_Query $q,Api_ApiDataRequestDescriptorModel $request); 
		$request->setLimit($this->limit);
		$request->setOffset($this->offset);
	}
}

class IcingaApiGrouping implements ApiRequestModifier {
	protected $groups = array();
	public function __construct($group) {
		if(!is_array($group))
			$group = array($group);
		$this->groups = $group;
	}

	public function apply(Api_ApiDataRequestModel $request) {
		$request->setGroups($this->groups);
	}

}

?>
