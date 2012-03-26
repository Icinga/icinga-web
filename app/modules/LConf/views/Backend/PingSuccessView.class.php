<?php

class LConf_Backend_PingSuccessView extends IcingaLConfBaseView {

    public function executeHtml(AgaviRequestDataHolder $rd) {
        return 'Success';
    }

    public function executeJSON(AgaviRequestDataHolder $rd) {
        return '{"success": true}';
    }
}
?>
