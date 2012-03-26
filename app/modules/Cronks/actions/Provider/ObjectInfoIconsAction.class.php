<?php

class Cronks_Provider_ObjectInfoIconsAction extends CronksBaseAction implements IAppKitDispatchableAction {
	public function getDefaultViewName() {
		return 'Success';
	}
    
    public function executeRead(AgaviRequestDataHolder $rd) {
        return $this->executeWrite($rd);
    }
    
    public function executeWrite(AgaviRequestDataHolder $rd) {
        
        $model = $this->getContext()->getModel('Provider.ObjectInfoIcons', 'Cronks', array (
            'type' => $rd->getParameter('type'),
            'oids' => $rd->getParameter('oids'),
            'connection' => $rd->getParameter('connection','icinga')
        ));
        
        $this->setAttribute('info_data', $model->getData());
        
        return $this->getDefaultViewName();
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
