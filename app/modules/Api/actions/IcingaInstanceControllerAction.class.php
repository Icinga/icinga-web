<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2013 Icinga Developer Team.
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


class Api_IcingaInstanceControllerAction extends IcingaApiBaseAction {
    public function isSecure() {
        return true;
    }

    public function getCredentials() {
        return array("icinga.control.admin");
    }

    public function executeRead(AgaviRequestDataHolder $rd) {
        // return host status
        $hosts = AccessConfig::getAvailableHosts();
        $status = array();
        foreach($hosts as $host) {
            $status[] = $this->getStatusForIcingaOnHost($host);
        }
        $this->setAttributeByRef("status",$status);
        return "Success";
    }

    public function getStatusForIcingaOnHost($host) {
        $status = -1;
        $err = NULL;

        try {
            $icinga = $this->getContext()->getModel("IcingaControlTask","Api", array("host"=>$host));
            $status = $icinga->getIcingaStatus();
        } catch (ApiSSHNotInstalledException $e) {
            $status = -1;
            $err = "SSH_NA_Err";
        } catch (ApiInvalidAuthTypeException $e) {
            $status = -1;
            $err = "IAuthErr";
        } catch (ApiAuthorisationFailedException $e) {
            $status = -1;
            $err = "AuthErr";
        } catch (ApiCommandFailedException $e) {
            $status = -1;
            $err = "CommandErr";
        } catch (Exception $e) {
            $status = -1;
            $err = "Unknown";
        }

        return array("instance"=>$host,"status"=>$status,"error"=>$err);
    }

    public function executeWrite(AgaviRequestDataHolder $rd) {
        $action = $rd->getParameter("action",null);
        $instance = $rd->getParameter("instance",null);
        $this->setAttribute("write",true);

        if ($instance == null) {
            $this->setAttribute("errorMsg","Invalid request");
            return "Success";
        }

        $icinga = $this->getContext()->getModel("IcingaControlTask","Api", array("host"=>$instance));

        try {
            switch ($action) {
                case 'restart':
                    $icinga->restartIcinga();
                    return "Success";
                    break;

                case 'shutdown':
                    $icinga->stopIcinga();
                    return "Success";
                    break;

                default:
                    $this->setAttribute("errorMsg","Invalid action");
                    return "Success";
            }
        } catch (Exception $e) {
            $this->setAttribute("errorMsg",$e->getMessage());
            return "Success";
        }
    }

    public function getDefaultViewName() {
        return "Success";
    }
}
?>
