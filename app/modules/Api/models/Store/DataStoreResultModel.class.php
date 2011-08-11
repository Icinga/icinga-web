<?php
/**
* Model that can be used to transform raw DataStoreModel results for
* ExtJS Grids/Trees, etc
* Currently supported:
* Input:
*   - Objects
*
* Output:
*   - Grid
* @package Icinga_Api
* @category DataStore
*
*
* @author Jannis Moßhammer <jannis.mosshammer@netways.de>
**/
class API_Store_DataStoreResultModel extends IcingaBaseModel {
    protected $root = "result";

    public $data = array();
    protected $model = null;
    protected $aliasMap;

    /**
    * Binds a model for this result (needed for recreation of aliases)
    *
    * @param IcingaApiDataStoreModel The model to bind to this result
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function setModel(IcingaApiDataStoreModel $model) {
        $this->model = $model;
        $this->createAliasMap();
    }

    /**
    * Parses this result for export
    *
    * @param Array  The result array returned by @see IcingaApiDataStoreModel
    * @param Array  The fields that will be defined in the resultset
    *
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function parseResult($rawResult,array $fields) {
        if (is_array($rawResult)) {
            if (isset($rawResult["count"])) {
                $this->count = $rawResult["count"];
                $rawResult = $rawResult["data"];
            } else {
                $this->count = count($rawResult);
            }
        } else {
            $this->count = count($rawResult);
        }

        if (is_array($rawResult)) {

            $this->parseInputFromArray($rawResult,$fields);
        } else  {
            $this->parseInputFromRecord($rawResult,$fields);
        }
    }


    /**
    * Internal section that sets all fields \w alias from the
    * record defined here
    *
    * @param Doctrine_Record    The record to get the result for
    * @param Array              The fields to set in the result
    *
    * @result Array             The result with the defined fields
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function fetchFieldsFromRecord($record, array $fields) {
        $result = array();

        foreach($fields as $field) {
            $value;
            $aliasFieldSplit = explode(".",$field,2);

            if (count($aliasFieldSplit) > 1) {
                $map = $this->aliasMap[$aliasFieldSplit[0]];
                $nodeInRecord = $record;
                foreach($map as $mapNode) {
                    $nodeInRecord = $nodeInRecord-> {$mapNode};
                }
                $value = $nodeInRecord;

                if (count($value) == 1) {
                    $value = $value->getFirst()-> {$aliasFieldSplit[1]};
                } else {
                    $valueArr = array();
                    foreach($value as $element) {
                        $valueArr[] = $element-> {$aliasFieldSplit[1]};
                    }
                    $value = $valueArr;
                }

                if (!isset($result[$aliasFieldSplit[0]])) {
                    $result[$aliasFieldSplit[0]] = array();
                }

                $result[$aliasFieldSplit[0]][$aliasFieldSplit[1]] = $value;
            } else {
                $result[$field] = $record-> {$field};
            }
        }
        return $result;
    }
    /**
    * Initializes @see aliasMap with the relation names and orders to
    * resolve aliases in the record tree
    * @access private
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function createAliasMap() {
        $this->aliasMap = array();
        $aliases = $this->model->getAliasDefs();
        foreach($aliases as $alias=>$relation) {
            $this->aliasMap[$alias] = $this->resolveRelation($relation,$aliases);
        }
    }

    /**
    * Resolves a relation to a complete path (which joins are needed to get from table a to table c
    * @access private
    * @param Array The relation to resolve
    * @param Array All defined aliases
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function resolveRelation(array $relation,array $aliases) {
        if ($relation["src"] == "my") {
            return array($relation["relation"]);
        } else {
            $path = $this->resolveRelation($aliases[$relation["src"]],$aliases);
            array_push($path,$relation["relation"]);
            return $path;
        }
    }

    /**
    * @TODO: IMPLEMENT
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function parseInputFromArray(array $rawResult,array $fields) {
        foreach($rawResult as $key=>$value) {
            if (is_string($value)) {
                $data[$key] = $value;
            } else {
                $data[$key] = json_encode($value);
            }
        }
    }

    /**
    * Parses a record for use in this class
    * @param Doctrine_Collection    The records to parse
    * @oaram Array  The fields to set in the record
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
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
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function getStoreResultForGrid() {
        $result = array();
        $root = $this->root;
        $result[$root] = $this->data;
        $result["totalCount"] = $this->count;
        return $result;

    }

    /**
    * The default initialize
    * $parameters can contain "model" to set the model on creation
    *
    * @param    AgaviContext    The current context
    * @param    Array           Parameters for this model
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function initialize(AgaviContext $ctx, array $parameters = array()) {
        if (isset($parameters["model"])) {
            $this->setModel($parameters["model"]);
        }
    }
}
