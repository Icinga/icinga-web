<?php
/**
 * View to create a javascript with all our application state information
 * @author mhein
 *
 */
class Web_Icinga_Ext_ApplicationStateSuccessView extends ICINGAWebBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Icinga.Ext.ApplicationState');
	}
	
	public function executeJavascript(AgaviRequestDataHolder $rd) {
		
		$data = array ();
		
		if ($this->getContext()->getUser()->isAuthenticated()) {
			$data = $this->getContext()->getUser()->getPrefVal(IcingaExtApplicationState::DATA_NAMESPACE, null, true);
			if ($data !== null) {
				$data = unserialize(base64_decode($data));
			}
			else {
				$data = array ();
			}
		}
		
		return 'var AppKitData = {};'
			. chr(13)
			. 'AppKitData.applicationState = '
			. json_encode($data)
			. ';';
	}
}

?>