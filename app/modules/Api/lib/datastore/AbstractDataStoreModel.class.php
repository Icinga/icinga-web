<?php

class DataStoreValidationException extends AppKitException {}; 
class DataStorePermissionException extends AppKitException {}; 

abstract class AbstractDataStoreModel extends IcingaBaseModel  
{
    
    public function doRead() {
        if(!$this->hasPermission($this->canRead()))
            throw new DataStorePermissionException("Store ".get_class($this)." is not readable");
        return $this->execRead(); 
    }
    
    public function doWrite($valueDescriptor) {
        if(!$this->hasPermission($this->canWrite()))
            throw new DataStorePermissionException("Store ".get_class($this)." is not writeable");
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
    
    protected function execWrite($valueDescriptor) {}
    protected function execRead() {}
    protected function execDelete($idDescriptor) {}
    protected function execUpdate($updateDescriptor) {}

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
    public function canWrite() {
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
