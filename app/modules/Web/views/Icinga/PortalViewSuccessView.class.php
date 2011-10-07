<?php

class Web_Icinga_PortalViewSuccessView extends IcingaWebBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        return $this->createForwardContainer('Cronks', 'System.CronkPortal');
    }
}

?>