<?php

class AppKit_Tasks_ClearCacheModel extends AppKitBaseModel {

    public function clearCache() {
        AgaviToolkit::clearCache();
        $this->log('Cache cleared through webinterface', AgaviLogger::INFO);
        return true;
    }
    
}

?>