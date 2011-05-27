<?php

class AppKit_Admin_ViewLogsSuccessView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {


        $this->setAttribute('title', 'AppKit logviewer');
        $parser = $this->getContext()->getModel("LogParser","AppKit");
        $this->setAttribute("availableLogs",json_encode($parser->getLogListing()));
        $this->setupHtml($rd);

    }
}

?>