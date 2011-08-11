<?php

class TestDummy_TestSuccessView extends IcingaTestDummyBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Test');
    }
}

?>