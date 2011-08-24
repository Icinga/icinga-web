<?php
/**
* Thrown when an datastore actions that isn't defined will be called
*
* @package Icinga_Api
* @category DataStore
*
* @author Jannis Moßhammer <jannis.mosshammer@netways.de>
**/
class IcingaApiActionNotAvailableException extends AppKitException {};

/**
* Handles access to ido2db icinga objects via doctrine and is the main interface
* herefore.
*
* @package Icinga_Api
* @category DataStore
*
* @author Jannis Moßhammer <jannis.mosshammer@netways.de>
**/
class IcingaApiDataStoreModel extends AbstractDataStoreModel {

    protected $connectionName = 'icinga';
    protected $database = "icinga";
    /**
    * The possible result types
    * @access private
    * @var Array
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected $resultTypes = array(
                                 "ARRAY" => Doctrine_Core::HYDRATE_ARRAY,
                                 "RECORD" => Doctrine_Core::HYDRATE_RECORD,
                                 "SCALAR" => Doctrine_Core::HYDRATE_SCALAR
                             );
    protected $resultType =  Doctrine_Core::HYDRATE_RECORD;

    /**
    * @access private
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function execUpdate($v) {
        throw new IcingaApiActionNotAvailableException("Can't update in api");
    }

    /**
    * @access private
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function execDelete($d) {
        throw new IcingaApiActionNotAvailableException("Can't delete from api");
    }

    /**
    * @access private
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function execInsert($v) {
        throw new IcingaApiActionNotAvailableException("Can't insert in api store");
    }

    /**
    * Reads data from the database and returns an array with
    * array(
    *   "data" => The resultset defined by @see resultType
    *   "count" => The total count w/o limit and offsets
    * )
    *
    * @return Array
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function execRead() {
        $request = $this->createRequestDescriptor();
        $this->applyModifiers($request);

        $request->autoResolveAliases();
        $this->lastQuery = $request;
        $result = $request->execute(NULL,$this->resultType);

        return array("data"=>$result,"count"=>$request->count());
    }
    /**
    * Contains the last executed query
    * @access private
    * @var IcingaDoctrine_Query
    **/
    protected $lastQuery = null;

    public function getSqlQuery() {
        if (!$this->lastQuery) {
            return "";
        }

        return $this->lastQuery->getSqlQuery();
    }

    /**
    *  Sets the database to read from
    *
    *  @param String    The database alias
    *
    *  @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function setConnection($connection) {
        $this->connectionName = $connection;
    }

    /**
    *   Register modifiers, the StoreClass itself can do nothing else than creating
    *   a Query object which will be parsed through the Modifiers
    *
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function setupModifiers() {
        $this->registerStoreModifier('Store.Modifiers.StorePaginationModifier','Api');
        $this->registerStoreModifier('Store.Modifiers.StoreSortModifier','Api');
        /*
        *   This should always be called last, because the AbstractDataModelStore
        *   passes the request arguments to the modifiers, allowing them to setup
        */
        parent::setupModifiers();
    }

    /**
    * Sets the result type of this descriptor (ARRAY or OBJECT)
    * @param String Sets the type of the query
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function setResultType($type) {
        if (!isset($this->resultTypes[$type])) {
            throw new InvalidArgumentException("Unknown doctrine hydrator ".$type);
        }

        $this->resultType = $this->resultTypes[$type];
    }

    /**
    * Agavi Modelinitializer. Allowed parameters are
    *   - "connectionName" (default: icinga) String with the connection name (must be defined in databases.(site).xml
    *   - "resultType" (default: object): ARRAY/OBJECT
    *
    * @param AgaviContext    The current AgaviContext
    * @param Array           Initialisation arrays
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function defaultInitialize(AgaviContext $c, array $parameters = array()) {
        if (isset($parameters["connectionName"])) {
            $this->connectionName = $parameters["connectionName"];
        }

        if (isset($parameters["resultType"])) {
            $this->setResultType($parameters["resultType"]);
        }

        parent::defaultInitialize($c,$parameters);
    }

    /**
    * Creates an @see IcingaDoctrine_Query object and returns it
    * @return IcingaDoctrineQuery
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function createRequestDescriptor() {
        $DBALMetaManager = AgaviContext::getInstance()->getModel("DBALMetaManager","Api");
        $DBALMetaManager->switchIcingaDatabase($this->connectionName);
      //  $connection = Doctrine_Manager::getInstance()->getConnection($this->connectionName);
        return IcingaDoctrine_Query::create(/*$connection*/);
    }

}
