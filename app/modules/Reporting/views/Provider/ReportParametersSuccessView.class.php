<?php

class Reporting_Provider_ReportParametersSuccessView extends ReportingBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Provider.ReportParameters');
    }

    public function executeJson(AgaviRequestDataHolder $rd) {
        $factory = $this->getContext()->getModel('JasperSoapFactory', 'Reporting', array(
                       'jasperconfig' => $rd->getParameter('jasperconfig')
                   ));

        $client = $factory->getSoapClientForWSDL(Reporting_JasperSoapFactoryModel::SERVICE_REPOSITORY);

        $parameters = $this->getContext()->getModel('JasperParameterStruct', 'Reporting', array(
                          'client'    => $client,
                          'uri'       => $rd->getParameter('uri') ,
                          'filter'	=> 'inputControl'
                      ));

        return json_encode($parameters->getJsonStructure());
    }
}

?>