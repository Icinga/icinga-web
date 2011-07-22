<?php


class Api_Store_LegacyLayer_LegacyApiResultModel extends IcingaApiDataStoreModel implements Iterator {
    protected $searchObject = false;
	protected $resultType = IcingaApiConstants::RESULT_OBJECT;
	protected $resultArray = false;
	
    protected $resultRow = false;
	protected $numResults = false;
	protected $offset = false;

    public function getAll () {
        return is_array($this->searchObject) ? $this->searchObject : $this->searchObject->toArray();
    }
    public function getRow () {
        $result = $this->resultRow;   

    
        return $this->resultRow;
	}
    public function setResultType ($type) {
		$this->resultType = $type;
	}
    public function current() {
        
        return $this;
    }    
    public function valid() { 
        return($this->offset-1 < $this->numResults); 
    }
    public function key () {
 		return $this->offset;
 	}
    public function getResultCount() {
        return $this->numResults;
    } 
    public function initialize(AgaviContext $ctx,array $params = array()) {
        $result = $params["result"];
        
        $this->searchObject = $result;
        $this->offset = 0;
        if(is_array($result))
            $this->numResults = count($result);
        else
            $this->numResults = $result->count();
        
        $this->next(); 
     }
    public function next() {
        if($this->offset >= $this->numResults)
            $this->resultRow = false; 
        else if(is_object($this->searchObject))
            $this->resultRow = $this->searchObject->get($this->offset);
        else if (is_array($this->searchObject))
            $this->resultRow = $this->searchObject[($this->offset)];
        
        $this->offset++; 
    }

    public function rewind() {
    //    $this->offset = 0;
       
    }

    public function get ($searchField = false) {

		$returnData = false;

		if ($searchField === false) {
		throw new AppKitException('get(): No search field defined!');
			return false;
		}

		if ($this->resultRow !== false) {
			$returnData = $this->__get($searchField);
		}

		return $returnData;

	}
    public function __call ($name, $arguments = array()) {
		return $this->__get($name);
	}


    public function __get ($name) {
		$returnValue = false;
	    if(is_object($this->resultRow)) {		
			if ($this->resultRow->{$name} === null) {
				throw new AppKitException('Search field "' . $name . '" not available!');
			} else {
				$returnValue = $this->resultRow->{$name};
			}
		} else if(is_array($this->resultRow)) {
    		if ($this->resultRow !== false) {
				if (!array_key_exists($name, $this->resultRow)) {
					throw new AppKitException('Search field "' . $name . '" not available!');
				} else {
					$returnValue = $this->resultRow[$name];
				}
			}
		}
		return $returnValue;
	}
} 
