<?php

class Api_ApiCommandSuccessView extends IcingaApiBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Icinga.ApiCommand');
    }

    public function executeJson(AgaviRequestDataHolder $rd) {
        return json_encode(array("success"=>true));

    }
}

?>
