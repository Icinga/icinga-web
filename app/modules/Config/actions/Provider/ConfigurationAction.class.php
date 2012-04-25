<?php

class Config_Provider_ConfigurationAction extends IcingaConfigBaseAction {

    public function getDefaultViewName() {
        return 'Success';
    }

    public function executeRead(AgaviParameterHolder $rd) {
        $model = $this->getContext()->getModel('ConfigValues', 'Config');
        $this->setAttribute('values', $model->getValuesForDisplay());
        return $this->getDefaultViewName();
    }

    public function executeWrite(AgaviParameterHolder $rd) {
        return $this->executeRead($rd);
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