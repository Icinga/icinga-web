<?php

class Reporting_Provider_SchedulerAction extends ReportingBaseAction {
    public function getDefaultViewName() {
        return 'Success';
    }

    public function execute(AgaviParameterHolder $rd) {

        $factory = $this->getContext()->getModel('JasperSoapFactory', 'Reporting', array(
                       'jasperconfig' => $rd->getParameter('jasperconfig')
                   ));

        $scheduler = $this->getContext()->getModel('JasperScheduler', 'Reporting', array(
                         'client' => $factory->getSoapClientForWSDL(Reporting_JasperSoapFactoryModel::SERVICE_SCHEDULER),
                         'jasperconfig' => $rd->getParameter('jasperconfig'),
                         'uri' => $rd->getParameter('uri')
                     ));

        switch ($rd->getParameter('action')) {
            case 'list':
                return 'List';
                break;

            case 'job':
                return 'Job';

            case 'delete':
                try {
                    $scheduler->deleteJob($rd->getParameter('job'));
                    $this->setAttribute('success', true);
                } catch (SoapFault $e) {
                    $this->setAttribute('error', $e->getMessage());
                }

            default:
                return $this->getDefaultViewName();
                break;
        }

    }

    public function isSecure() {
        return true;
    }

    public function getCredentials() {
        return array('icinga.user');
    }

    public function handleError(AgaviRequestDataHolder $rd) {
        return $this->getDefaultViewName();
    }
}

?>