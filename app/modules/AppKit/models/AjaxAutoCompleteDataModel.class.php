<?php

class AppKit_AjaxAutoCompleteDataModel extends NETWAYSAppKitBaseModel
{
	
	private $component 		= null;
	private $key_field		= null;
	private $value_field	= null;
	private $search_fields	= null;
	private $search_value	= null;
	
	private $parents		= array ();
	private $parents_config	= array ();
	
	private $static_values	= array ();
	
	private $static_where	= array ();
	
	public function setStaticValues(array $static_values) {
		$this->static_values = $static_values;
	}
	
	public function setComponentName($name) {
		$this->component = $name;
	}
	
	public function setKeyField($field) {
		$this->key_field = $field;
	}
	
	public function setValueField($field) {
		$this->value_field = $field;
	}
	
	public function setSearchFields(array $fields) {
		$this->search_fields = $fields;
	}
	
	public function setSearchValue($query) {
		$this->search_value = $query;
	}
	
	private function getSearchValue() {
		return sprintf('%s%%', $this->search_value);
	}
	
	public function setParents(array $parents) {
		$this->parents = $parents;
	}
	
	public function setParentsConfig(array $config) {
		$this->parents_config = $config;
	}
	
	public function addStaticWhereCondition($fieldname, $value) {
		$this->static_where[] = array(
			'field'	=> $fieldname,
			'value'	=> $value,
		);
		
		return true;
	}
	
	
	public function getResults() {
		
		$results = array ();
		$out = array ();
		
		// Adding static values
		if (count($this->static_values)>0) {
			foreach ($this->static_values as $key=>$val) {
				$out[] = array (
					'key'	=> $key,
					'value'	=> $val,
				);
			}
		}
		
		// Generating doctrine results
		if ($this->component && $this->value_field && $this->key_field) {
			$query = Doctrine_Query::create()
			->from($this->component)
			->select($this->key_field. ', '. $this->value_field)
			->orderBy($this->value_field. ' ASC')
			->limit(1000);
	
			if ($this->getSearchValue()) {
				foreach ($this->search_fields as $field) {
					$query->orWhere($field. ' LIKE ?', array($this->getSearchValue()));
				}
			}
			
			// Adding parent queries
			foreach ($this->parents_config as $pid=>$config) {
				if (($pvalue = $this->parents[$pid])) {
					$query->innerJoin(sprintf('%s.%s %s', $this->component, $config['component'], $config['alias']));
					$query->andWhere(sprintf('%s.%s=?', $config['alias'], $config['field']), array($pvalue));
				}
			}
			
			foreach ($this->static_where as $fd) {
				$query->andWhere(sprintf('%s=?', $fd['field']), array($fd['value']));
			}
			
			$results = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
		
			foreach ($results as $result) {
				$out[] = array(
					'key'		=> $result[$this->key_field],
					'value'		=> $result[$this->value_field]
				);
			}
		}
		
		return $out;
	}
}

?>