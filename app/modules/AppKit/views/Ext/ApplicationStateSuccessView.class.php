<?php
/**
 * View to create a javascript with all our application state information
 * @author mhein
 *
 */
class AppKit_Ext_ApplicationStateSuccessView extends ICINGAAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Icinga.Ext.ApplicationState');
	}
	
	public function executeJavascript(AgaviRequestDataHolder $rd) {
		
		$data = array ();
		
		if ($this->getContext()->getUser()->isAuthenticated()) {
			$data = $this->getContext()->getUser()->getPrefVal(AppKitExtApplicationStateFilter::DATA_NAMESPACE, null, true);
			
			if ($data !== null) {
				$data = unserialize(base64_decode($data));
			}
			else {
				$data = array ();
			}
		}
		
		return sprintf(
			'Ext.onReady(function() {'. "\n"
			. "\t". 'AppKit.Ext.setAppState(%s);'. "\n"
			. '});'. "\n", json_encode($data)
		);
		
	}
}

?>