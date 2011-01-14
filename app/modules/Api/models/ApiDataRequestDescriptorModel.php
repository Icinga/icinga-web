<?php
class InvalidAliasException extends AppKitException {}

class ApiRequestDescriptor {
	public $table = NULL;
	public $fields = array();
	public $grouped = array();
	private $tables = array();
	private $modifiers = array();

	public function addField($field, $relation = "") {
		if($relation) {	
			$table = $this->table->getTable(); 
			$alias = $this->table->getRelation($relation);
			if(!$alias)
				throw InvalidAliasException($relation);
				
			$field = "origin.".$field." "."_".$relation;
		}
		$this->fields[] = $field;
	}

	public function __construct(Doctrine_Record $table,array $fields = array()) {
		$this->setTable($table);
		
		if(!empty($fields))
			$this->addFields($fields);
	}

	public function addFields(array $fields) {
		foreach($fields as $field)
			$this->addfield($fields);
	}

	public function addModifier(IcingaApiRequestModifier $mod) {
		$this->modifiers[] = $mod;
	}

	public function buildQuery() {
		$base = Doctrine_Query::create()->select($this->getSelectQuery());
		foreach($this->modifiers as $mod)
			$mod->apply(base,$this);
	}
	
	}
}
