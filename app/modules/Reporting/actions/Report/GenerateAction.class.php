<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-present Icinga Developer Team.
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


class Reporting_Report_GenerateAction extends ReportingBaseAction {
    public function getDefaultViewName() {
        return 'Success';
    }

    public function executeRead(AgaviParameterHolder $rd) {
        return $this->executeWrite($rd);
    }

    public function executeWrite(AgaviParameterHolder $rd) {

        $data = (array)json_decode($rd->getParameter('data', ""));

        $factory = $this->getContext()->getModel('JasperSoapFactory', 'Reporting', array(
                       'jasperconfig' => $rd->getParameter('jasperconfig')
                   ));

        $client = $factory->getSoapClientForWSDL(Reporting_JasperSoapFactoryModel::SERVICE_REPOSITORY);

        try {

            $parameters = $this->getContext()->getModel('JasperParameterStruct', 'Reporting', array(
                              'client'    => $client,
                              'uri'       => $rd->getParameter('uri'),
                              'filter'  => 'reportUnit'
                          ));

            $reports = $parameters->getObjects();

            $converter = $this->getContext()->getModel('FieldValueConverter', 'Reporting', array(
                             'client'      => $client,
                             'uri'         => $rd->getParameter('uri'),
                             'parameters'   => $data
                         ));

            $creator = $this->getContext()->getModel('ReportGenerator', 'Reporting', array(
                           'client' => $client,
                           'report' => $reports[0],
                           'format' => $rd->getParameter('output_type'),
                           'parameters' => $converter->getConvertedParameters()
                       ));

            $data = $creator->getReportData();

            $userFile = $this->getContext()->getModel('ReportUserFile', 'Reporting');

            $userFile->storeFile($data, $rd->getParameter('output_type'), $reports[0]);
            $this->setAttribute('success', true);
        } catch (AppKitModelException $e) {
            $this->setAttribute('success', false);
            $this->setAttribute('error', $e->getMessage());
        }

        return $this->getDefaultViewName();
    }

    public function isSecure() {
        return true;
    }

    public function getCredentials() {
        return array('icinga.user');
    }

    public function handleError(AgaviRequestDataHolder $rd) {
        $this->setAttribute('success', false);
        $this->setAttribute('error', 'Validation failed');
        return $this->getDefaultViewName();
    }
}

?>