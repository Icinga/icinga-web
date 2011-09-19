<?php


class Api_Store_LegacyLayer_LegacyApiResultModel extends IcingaApiDataStoreModel implements Iterator {
    protected $searchObject = false;
    protected $resultType = IcingaApiConstants::RESULT_OBJECT;
    protected $resultArray = false;
    protected $resultColumns = array();
    protected $resultRow = false;
    protected $numResults = false;
    protected $offset = false;

    public function getAll() {
        return is_array($this->searchObject) ? $this->searchObject : $this->searchObject->toArray();
    }
    public function getRow() {
        $result = $this->resultRow;


        return $this->resultRow;
    }
    public function setResultType($type) {
        $this->resultType = $type;
    }
    public function current() {

        return $this;
    }
    public function valid() {
        return($this->offset-1 < $this->numResults);
    }
    public function key() {
        return $this->offset;
    }
    public function getResultCount() {
        return $this->numResults;
    }
    public function initialize(AgaviContext $ctx,array $params = array()) {
        $result = $params["result"];

        $this->searchObject = $this->createSearchObjectFromResult($result,$params["columns"]);
        $this->offset = 0;

        if (is_array($result)) {
            $this->numResults = count($result);
        } else {
            $this->numResults = $result->count();
        }

        $this->next();
    }

    public function createSearchObjectFromResult($resultCollection,array $columns) {
        $r = array();
        foreach($resultCollection as $result) {
           
            if (is_array($result)) {
                $res = $this->remapResult($columns,$result);
                $r[] = $res;
            } else {
                $res = new StdObject();
                foreach($columns as $col) {
                    $res-> {$col} = $result-> {$col};
                    if($col == "HOST_CURRENT_STATE")
                        if($result->{"HOST_IS_PENDING"} == 1)
                            $res->{$col} = 99;
                    if($col == "SERVICE_CURRENT_STATE")
                        if($result->{"SERVICE_IS_PENDING"} == 1)
                            $res->{$col} = 99;                 
                }
                $r[] = $res;
            }
        }
        return $r;
    }
    private $mapping = array();
    public function remapResult(array $columns, array $result) {
        $remapped = array();

        if (empty($this->mapping)) {
            $this->createMappingForResult($columns,$result);
        }

        foreach($this->mapping as $srckey=>$targetkey) {
            if (isset($result[$srckey])) {
                $remapped[$targetkey] = $result[$srckey];
            }
        }
        $this->updatePendingStates($remapped);
        
        return $remapped;
    }

    protected function updatePendingStates(&$remapped) {
        if(isset($remapped["HOST_IS_PENDING"]) && isset($remapped["HOST_CURRENT_STATE"]))
            if($remapped["HOST_IS_PENDING"] > 0)
                $remapped["HOST_CURRENT_STATE"] = 99;
        if(isset($remapped["SERVICE_IS_PENDING"]) && isset($remapped["SERVICE_CURRENT_STATE"]))
            if($remapped["SERVICE_IS_PENDING"] > 0)
                $remapped["SERVICE_CURRENT_STATE"] = 99;
    }
    
    protected function createMappingForResult(array $columns,$result) {
        foreach($result as $key=>$value) {
            if (in_array($key,$columns)) {
                $this->mapping[$key] = $key;
                continue;
            }

            $exploded = explode("_",$key,2);

            if (count($exploded) < 2) {
                continue;
            }

            if (in_array($exploded[1],$columns)) {
                $this->mapping[$key] = $exploded[1];
            }

        }

    }

    public function next() {
        if ($this->offset >= $this->numResults) {
            $this->resultRow = false;
        } else if (is_object($this->searchObject)) {
            $this->resultRow = $this->searchObject->get($this->offset);
        } else if (is_array($this->searchObject)) {
            $this->resultRow = $this->searchObject[($this->offset)];
        }

        $this->offset++;
    }

    public function rewind() {
        //    $this->offset = 0;

    }

    public function get($searchField = false) {

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
    public function __call($method,$argument) {
        return $this->__get($method);
    }


    public function __get($name) {
        $returnValue = false;

        if (is_object($this->resultRow)) {
            if ($this->resultRow-> {$name} === null) {
                throw new AppKitException('Search field "' . $name . '" not available!');
            }
            else {
                $returnValue = $this->resultRow-> {$name};
            }
        } else if (is_array($this->resultRow)) {
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
