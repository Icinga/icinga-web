<?php

class Cronks_System_MonitorPerformanceSuccessView extends CronksBaseView {

    public function executeHtml(AgaviRequestDataHolder $rd) {
   
        $this->setupHtml($rd);
        $this->setAttribute('_title', 'System.MonitorPerformance');
    }

    public function executeJson(AgaviRequestDataHolder $rd) {

        $model = $this->getContext()->getModel('Provider.SystemPerformance', 'Cronks');

        $json_doc = $model->getJson();

        return (string)$json_doc;

    }


}

?>