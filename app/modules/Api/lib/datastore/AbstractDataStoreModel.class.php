<?php

class DataStoreValidationException extends AppKitException {}; 
class DataStorePermissionException extends AppKitException {}; 

abstract class AbstractDataStoreModel extends IcingaBaseModel  
{
    protected $modifiers = array();
    protected $requestParameters = array();
    public function doRead() {
        
        if(!$this->hasPermission($this->canRead()))
            throw new DataStorePermissionException("Store ".get_class($this)." is not readable");
        return $this->execRead(); 
    }
    
    public function doInsert($valueDescriptor) {
        if(!$this->hasPermission($this->canInsert()))
            throw new DataStorePermissionException("Store ".get_class($this)." is not insertable");
        return $this->execWrite($valueDescriptor);
    }
    
    public function doUpdate($idDescriptor) {
        if(!$this->hasPermission($this->canUpdate()))
            throw new DataStorePermissionException("Store ".get_class($this)." is not updateable");
        return $this->execUpdate($idDescriptor);
    }
    
    public function doDelete($valueDescriptor) {
        if(!$this->hasPermission($this->canDelete()))
            throw new DataStorePermissionException("Store ".get_class($this)." is not deleteable");
        return $this->execDelete($valueDescriptor);
    }
    
    protected function execInsert($valueDescriptor) {}
    protected function execRead() {}
    protected function execDelete($idDescriptor) {}
    protected function execUpdate($updateDescriptor) {}
    
    /**
    * Register a new modifier for this store. Should be done statically on @see setupModifier
    * @param    String  The modifier to add (module path)
    * @param    String  The module where the modifier lies
    **/
    protected function registerStoreModifier($modifier, $module= null) {  
        if(is_a($modifier,"IDataStoreModifier"))
            $this->modifiers[] = $modifier;
        else 
            $this->modifiers[] = $this->context->getModel($modifier,$module);
    }

    /**
    * Store modifiers must be defined and added here
    * via the @see registerStoreModifier method.
    *  
    **/
    protected function setupModifiers() {
        
        foreach($this->requestParameters as $parameter=>$value) {
            foreach($this->modifiers as $modifier) { 
               $modifier->handleArgument($parameter,$value); 
            }
        }
    }
    
    public function getModifiers() {
        return $this->modifiers;
    }
    /**
    * Initializes the module and calls setupModifers if parameters doesn't contain "noStoreModifierSetup"
    * @param    AgaviContext    The context of the agavi instance
    * @param    Array           An array of submitted parameters
    *                               - AgaviRequestDataHolder request : the request information
    *                               - noStoreModifierSetup : Don't setup modifiers (add when callin parent::initialize)
    **/ 
    public function initialize(AgaviContext $context,array $parameters = array()) {
        parent::initialize($context,$parameters);
        $this->defaultInitialize($context,$parameters);
    }
    
    protected function defaultInitialize(AgaviContext $context,array $parameters = array()) {   
        if(!isset($parameters["request"]))
            throw new InvalidArgumentException("DataStoreModel must be called with the 'request' parameter");
        
        $this->requestParameters = $parameters["request"]->getParameters();
        if(!isset($parameters["noStoreModifierSetup"]))
            $this->setupModifiers(); 

    }

    protected function hasPermission($perm) {
        if($perm === true)
            return true;
        if($perm === false)
            return false;

        $ctx = AgaviContext::getInstance();
        $user = $ctx->getUser();
        if(!$user)
            return false;
        if(!is_array($perm))
            $perm = array($perm);
        foreach($perm as $p) {
            if($user->hasCredential($p))
                return true;
        }
        return false;
    }    

    public function canRead() {
        return true;
    }
    public function canInsert() {
        return false;
    }
    public function canUpdate() {
        return false;
    }
    public function canDelete() {
        return false;
    }
} 
?>
