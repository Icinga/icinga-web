<?php

class Api_ApiDataStoreProviderSuccessView extends IcingaApiBaseView {
    public function executeJson(AgaviRequestDataHolder $rd) {
        $r = $this->getAttribute("result");
        $result = $r->getStoreResultForGrid();
        $result["success"] = true;
        return json_encode($result);
    }

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'ApiDataStoreProvider');
    }
}

?>
