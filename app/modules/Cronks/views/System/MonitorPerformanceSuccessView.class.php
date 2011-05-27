<?php

class Cronks_System_MonitorPerformanceSuccessView extends CronksBaseView {

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);
        $this->setAttribute('_title', 'System.MonitorPerformance');
    }

    public function executeJson(AgaviRequestDataHolder $rd) {

        $model = $this->getContext()->getModel('System.MonitorPerformanceData', 'Cronks');

        $json_doc = $model->getJson();
        $json_doc->setSuccess(true);

        return (string)$json_doc;

    }


}

?>