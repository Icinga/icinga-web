<?php



class LConf_LDAPFilterModel extends IcingaLConfBaseModel 
{
	protected $key = null;
	protected $value = null;
	protected $filterString = "";
	protected $negated = false;
	protected $type = "exact";

	
	public function setKey($key) {
		$this->key = $key;
	}
	public function getKey()	{
		return $this->key;
	}
	
	public function setValue($value) {
		$this->value = $value;	
	}
	public function getValue()	{
		return $this->value;
	}
	
	public function setFilterString($str) {
		$this->filterString = $str;
	}
	public function getFilterString() 	{
		return $this->filterString();
	}
	
	public function setNegated($bool) {
		$this->negated = (boolean) $bool;	
	}
	public function isNegated()	{
		return $this->negated;
	}
	
	public function setType($type) {
		$this->type = $type;
	} 
	public function getType() {
		return $this->type;
	}
	
	public function __construct($key, $value,$negated = false,$type="exact") {
		$this->setKey($key);
		$this->setValue($value);		
		$this->setNegated($negated);
		$this->setType($type);
	}
	
	public function __toArray() {
		return array($this->getKey(),$this->getValue(),$this->isNegated());
	}
	
	public function buildFilterString() {
		$value = $this->getValue();
		switch($this->getType()) {
			case "endswith":
				$value = $this->getKey()."="."*".$value;
				break;
			case "startswith":
				$value = $this->getKey()."=".$value."*";
				break;
			case "contains":

				$value = "|(".$this->getKey()."=*".$value."*)(".$this->getKey()."=".$value."*)(".$this->getKey()."=*".$value.")(".$this->getKey()."=".$value.")";		
				break;		
			default:
				$value = $this->getKey()."=".$value;
		}
		
		$filterString = ($this->isNegated() ? '(!(' : '(').
							$value.
						($this->isNegated() ? '))' : ')');
		$this->setFilterString($filterString);

		return $filterString;	
	}

}

?>
