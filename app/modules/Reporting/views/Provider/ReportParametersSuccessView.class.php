<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
//
// Copyright (c) 2009-2015 Icinga Developer Team.
// All rights reserved.
//
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}


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
                          'filter'  => 'inputControl'
                      ));

        return json_encode($parameters->getJsonStructure());
    }
}

?>
