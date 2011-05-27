<?php

class AppKit_DataProvider_LogProviderSuccessView extends AppKitBaseView {
    public function executeJson(AgaviRequestDataHolder $rd) {
        $start = $rd->getParameter("start");
        $limit = $rd->getParameter("limit");
        $parser = $this->getContext()->getModel("LogParser","AppKit");
        $logEntries = $parser->parseLog($rd->getParameter('logFile'),$start,$limit);
        return json_encode($logEntries);
    }
}

?>