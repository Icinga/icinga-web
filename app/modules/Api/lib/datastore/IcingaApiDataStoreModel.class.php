<?php
class IcingaApiActionNotAvailableException extends AppKitException {};

class IcingaApiDataStoreModel extends AbstractDataStoreModel {
    public $requiredParams = array("connectionName");    
    
    protected $connectionName;
    protected $database = "icinga"; 
    protected $resultTypes = array(
        "ARRAY" => Doctrine_Core::HYDRATE_ARRAY,
        "RECORD" => Doctrine_Core::HYDRATE_RECORD
    );
    protected $resultType =  Doctrine_Core::HYDRATE_RECORD;

    public function execUpdate($v) {
        throw new IcingaApiActionNotAvailableException("Can't update in api");
    }

    public function execDelete($d) {
        throw new IcingaApiActionNotAvailableException("Can't delete from api");
    }
    
    public function execInsert($v) {
        throw new IcingaApiActionNotAvailableException("Can't insert in api store");
    } 
    
    public function execRead() {
        $request = $this->createRequestDescriptor();
        foreach($this->getModifiers() as $mod) {
            $mod->modify($request);
        } 
        return $request->execute(NULL,$this->resultType);
    }
    /**
    *   Register modifiers, the StoreClass itself can do nothing else than creating 
    *   a Query object which will be parsed through the Modifiers
    *
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
    
    public function setResultType($type) {
        if(!isset($this->resultTypes[$type]))
            throw new InvalidArgumentException("Unknown doctrine hydrator ".$type);
        $this->resultType = $this->resultTypes[$type];
    }
    
    public function defaultInitialize(AgaviContext $c, array $parameters = array()) {   
        if(isset($parameters["connectionName"]))
            $this->connectionName = $parameters["connectionName"];
        if(isset($parameters["resultType"]))
            $this->setResultType($parameters["resultType"]);
        parent::defaultInitialize($c,$parameters); 
    }
   
    protected function createRequestDescriptor() {
        $DBALMetaManager = AgaviContext::getInstance()->getModel("DBALMetaManager","Api");
        $DBALMetaManager->switchIcingaDatabase($this->connectionName); 
        
        return IcingaDoctrine_Query::create();
    }
    /**
    * Delegates unknown method calls to the modifiers and thereby extends the
    * store by the modifiers methods
    **/
    public function __call($method,$argument) {
        $found = false;
        foreach($this->getModifiers() as $mod) {
            if(method_exists($mod,$method)) {
                $found = true;
                call_user_func_array(array($mod,$method),$argument);
            }
        }
        if(!$found)
            throw new BadMethodCallException("Call to unknown method $method");
    }
}
