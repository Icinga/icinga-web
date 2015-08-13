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

            case 'edit':
                try {
                    $scheduler->editJob($rd->getParameter('job_data'));
                    $this->setAttribute('success', true);
                } catch (SoapFault $e) {
                    $this->setAttribute('error', $e->getMessage());
                } catch (JasperSchedulerJobException $e) {
                    $this->setAttribute('error', $e->getMessage());
                }
                return $this->getDefaultViewName();
                break;

            case 'delete':
                try {
                    $scheduler->deleteJob($rd->getParameter('job'));
                    $this->setAttribute('success', true);
                } catch (SoapFault $e) {
                    $this->setAttribute('error', $e->getMessage());
                }
                return $this->getDefaultViewName();
                break;

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
