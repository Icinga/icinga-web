<?php

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
