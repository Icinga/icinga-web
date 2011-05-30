<?php

class Web_Icinga_TestPageSuccessView extends IcingaWebBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Icinga.TestPage');
    }
}

?>