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