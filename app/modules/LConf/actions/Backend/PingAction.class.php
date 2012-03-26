<?php

class LConf_Backend_PingAction extends IcingaLConfBaseAction {
    public function executeRead(AgaviRequestDataHolder $rd) {
        
        return 'Success';
    }
    public function executeWrite(AgaviRequestDataHolder $rd) {
        return 'Success';
    }

    public function isSecure() {
        return true;
    }

}