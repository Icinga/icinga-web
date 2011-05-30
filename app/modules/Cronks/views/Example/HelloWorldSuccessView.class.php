<?php

class Cronks_Example_HelloWorldSuccessView extends CronksBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Example.HelloWorld');
    }
}

?>