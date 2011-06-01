<?php
class IcingaApiActionNotAvailableException extends AppKitException {};

class IcingaApiDataStoreModel extends AbstractDataStoreModel {
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
        foreach($this->getModifiers as $mod) {
            $mod->modify($request);
        }
        $request->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    protected function setupModifiers() {
        
    } 

    public function defaultInitialize(AgaviContext $c, array $parameters()) { 
        if(isset($parameters["connectionName"]))
            $this->connectionName = $parameters["connectionName"];
        $this->setupModifers();
    }
   
    protected function createRequestDescriptor() {
        $DBALMetaManager = $this->getContext()->getModel("DBALMetaManager","Api");
        $DBALMetaManager->switchIcingaDatabase($this->connectionName); 

        return IcingaDoctrine_Query::create();
    }


}
