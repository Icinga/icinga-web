<?php

class API_Store_DataStoreResultModel extends IcingaBaseModel 
{
    protected $root = "result";

    public $data = array();    
    protected $model = null;
    
    public function setModel(IcingaApiDataStoreModel $model) {
        $this->model = $model;
        $this->createAliasMap();
    }
    
    public function parseResult($rawResult,array $fields) {
        if(is_array($rawResult)) {
            if(isset($rawResult["count"])) {
                $this->count = $rawResult["count"];
                $rawResult = $rawResult["data"];
            } else {
                $this->count = count($rawResult);
            }
        } else {
            $this->count = count($rawResult);
        }
        if(is_array($rawResult)) {
             
            $this->parseInputFromArray($rawResult,$fields);
        } else  {
            $this->parseInputFromRecord($rawResult,$fields);
        }
    }
    protected $aliasMap; 
    protected function fetchFieldsFromRecord($record, $fields) {
        $result = array();
        
        foreach($fields as $field) {
            $value;
            $aliasFieldSplit = explode(".",$field,2);
            if(count($aliasFieldSplit) > 1) { 
                $map = $this->aliasMap[$aliasFieldSplit[0]];
                $nodeInRecord = $record;
                foreach($map as $mapNode) {
                    $nodeInRecord = $nodeInRecord->{$mapNode};        
                } 
                $value = $nodeInRecord;
                if(count($value) == 1) {
                    $value = $value->getFirst()->{$aliasFieldSplit[1]};
                } else {
                    $valueArr = array();
                    foreach($value as $element) {
                        $valueArr[] = $element->{$aliasFieldSplit[1]};
                    }
                    $value = $valueArr;
                }
                if(!isset($result[$aliasFieldSplit[0]]))
                    $result[$aliasFieldSplit[0]] = array();
                $result[$aliasFieldSplit[0]][$aliasFieldSplit[1]] = $value; 
            } else {
                $result[$field] = $record->{$field};     
            } 
        }
        return $result;
    }
    /**
    *   Initializes aliasMap with the relation names and orders to 
    *   resolve aliases in the record tree
    *
    **/
    protected function createAliasMap() {
        $this->aliasMap = array();
        $aliases = $this->model->getAliasDefs();
        foreach($aliases as $alias=>$relation) {
            $this->aliasMap[$alias] = $this->resolveRelation($relation,$aliases);
        } 
    }
    
    protected function resolveRelation(array $relation,array $aliases) {
        
        if($relation["src"] == "my") {
            return array($relation["relation"]);
        } else {  
            $path = $this->resolveRelation($aliases[$relation["src"]],$aliases);
            array_push($path,$relation["relation"]);
            return $path;
        }
    }
    
    protected function parseInputFromArray(array $rawResult,array $fields) {
        
        foreach($rawResult as $key=>$value) {
            if(is_string($value)) {
                $data[$key] = $value;
            } else {
                $data[$key] = json_encode($value);
            }
        } 
    }

    protected function parseInputFromRecord(Doctrine_Collection $records,array $fields) {  
        
        foreach($records as $current) {
            $iterator = $current->getIterator();
            $record = array();
            $this->data[] = $this->fetchFieldsFromRecord($current,$fields);      
        } 
    }
    
    /**
    *   Returns an array that can be send to the client as a json 
    *   object and is suitable as a result for ExtJS Grids
    *   @param {mixed}  The result returned from doctrine, either an array or Doctrine_Record instance
    *
    *   @result Array   The array that can be send to the client
    */
    public function getStoreResultForGrid() {
        $result = array();
        $root = $this->root;
        $result[$root] = $this->data;
        $result["totalCount"] = $this->count;
        return $result;
         
    }

    public function initialize(AgaviContext $ctx, array $parameters = array()) {
        if(isset($parameters["model"]))
            $this->setModel($parameters["model"]);
    }
}
