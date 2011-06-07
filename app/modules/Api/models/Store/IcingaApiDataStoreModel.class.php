<?php
class IcingaApiActionNotAvailableException extends AppKitException {};

class Api_Store_IcingaApiDataStoreModel extends AbstractDataStoreModel {
    protected $connectionName;
    protected $database = "icinga";
    public $requiredParams = array("connectionName");    

    public function execUpdate($v) {
        throw new IcingaApiActionNotAvailableException("Can't update from api");
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
        return $request->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    protected function setupModifiers() {
        $this->registerStoreModifier('Store.Modifiers.StoreTargetModifier','Api');
        $this->registerStoreModifier('Store.Modifiers.StoreFilterModifier','Api');
        parent::setupModifiers();
    }

    public function defaultInitialize(AgaviContext $c, array $parameters = array()) {  
        if(isset($parameters["connectionName"]))
            $this->connectionName = $parameters["connectionName"];
        parent::defaultInitialize($c,$parameters); 
    }
   
    protected function createRequestDescriptor() {
        $DBALMetaManager = AgaviContext::getInstance()->getModel("DBALMetaManager","Api");
        $DBALMetaManager->switchIcingaDatabase($this->connectionName); 
        
        return Doctrine_Query::create();
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
