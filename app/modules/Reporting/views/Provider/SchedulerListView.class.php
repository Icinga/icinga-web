<?php

class Reporting_Provider_SchedulerListView extends ReportingBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);
        $this->setAttribute('_title', 'Provider.Scheduler');
    }

    public function executeJson(AgaviRequestDataHolder $rd) {
        $factory = $this->getContext()->getModel('JasperSoapFactory', 'Reporting', array(
                       'jasperconfig' => $rd->getParameter('jasperconfig')
                   ));

        $client = $factory->getSoapClientForWSDL(Reporting_JasperSoapFactoryModel::SERVICE_SCHEDULER);

        $scheduler = $this->getContext()->getModel('JasperScheduler', 'Reporting', array(
                         'client' => $client,
                         'jasperconfig' => $rd->getParameter('jasperconfig'),
                         'uri' => $rd->getParameter('uri')
                     ));

        $data = $scheduler->getScheduledJobs();

        $response = new AppKitExtJsonDocument();
        $response->hasField('id');
        $response->hasField('version');
        $response->hasField('reportUnitURI');
        $response->hasField('username');
        $response->hasField('label');
        $response->hasField('state');
        $response->hasField('previousFireTime');
        $response->hasField('nextFireTime');
        $response->setData($data);

        $response->setSuccess();

        return $response->getJson();
    }
}

?>