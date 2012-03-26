<?php
class filterGroupException extends AppKitException {};

class LConf_LDAPFilterGroupModel extends IcingaLConfBaseModel 
									implements Iterator
{
	static public $allowedTypes = array("&","|","!","AND","OR","NOT");
	protected $filters = array();
	protected $filterType = "&";
	protected $negated = false;
	private $currentIteratorPos = 0;
	
	public function setFilters(array $filter) {
		$this->filters = $filter;
	}
	
	public function setFilterType($type) {
		if(!in_array($type,self::$allowedTypes))
			throw new filterGroupException("Invalid FilterGroup connection :".$type);
		// Format string filter to boolean operator
		switch($type) {
			case "AND":
				$type = '&';
				break;
			case "OR":
				$type = '|';
				break;
			case "NOT":
				$type = '!';
				break;
		}
		$this->filterType = $type;
	}
	
	public function setFilterString($str) {
		$this->filterString = $str;
	}
	public function setNegated($negated) {
		$this->negated = $negated;	
	}
	
	public function getFilters() {
		return $this->filters;
	}
	
	public function getFilterString() 	{
		return $this->filterString();
	}
	
	public function getFilterType() {
		return $this->filterType;
	}
	public function isNegated() {
		return $this->negated;
	}
	public function addFilter($filter) {
		// Typecheck of the filters
		$this->filters[] = $filter;
		$this->rewind();
	}
	
	public function removeFilter($filter) {
		$pos = in_array($filter,$this->filters);
		if($pos)
			unset($this->filters[$pos]);
		$this->rewind();
	}
	
	
	public function __construct($filterType = "&",$negated = false) {
		$this->setFilterType($filterType);
		$this->negated = $negated;
	}
	
	/**
	 * Iterates through all filters and builds a string like
	 * (|(o=Netways)(ou=Developement))
	 * 
	 * @return string 
	 */
	public function buildFilterString() {
		
			
		$string = "(".$this->getFilterType();
		foreach($this as $filter) {
			$string .= $filter->buildFilterString();
		}
		$string .= ")";	
		if($this->isNegated())		
			$string = "(!".$string.")";
		$this->setFilterString($string);
		return $string;
	}
	
	/**
	 * Iterator implementation 
	 */	
	public function current() {
		return $this->filters[$this->currentIteratorPos];
	}
	public function valid() {
		return (array_key_exists($this->currentIteratorPos,$this->filters));
	}
	public function next() {
		++$this->currentIteratorPos;
	}
	public function rewind() {
		$this->currentIteratorPos = 0;
	}
	public function key() {
		return $this->currentIteratorPos;
	}
	
	static public function __fromArray(array $filterGroup) {
		$context = $this->getContext();
		$filterGroup = new LConf_LDAPFilterGroupModel($filterGroup["type"]);
		foreach($filterGroup["filter"] as $filter)	{
			if(is_array($filter)) {
				$filterGroup->addFilter(self::__fromArray($filter));	
			} else {
				$filterGroup->addFilter($context->getModel("LDAPFilter","LDAP",array_values($filter)));	
			}
		}
		return $filterGroup;
	}
	
	public function __toArray() {
		$filterGroup = array(
			"type" => $this->getFilterType(),
			"filter" => array()
		);
		foreach($this as $filter) {
			$filterGroup["filter"][] = $filter->__toArray();
		}
		return $filterGroup;
	}

}

?>
