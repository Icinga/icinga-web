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
		
		$out = array (
			'data'	=> ''
		);
		
		$cmd = $rd->getParameter('cmd', 'read');
		$provider = $this->getContext()->getModel('Ext.ApplicationState', 'AppKit');
		switch ($cmd) {
			
			case 'write':
			case 'read':
			default:
				$out['data'] = (json_decode( $provider->readState() ));
			break;
		}
		
		$out['success'] = true;
		
		return json_encode($out);
		
//		$data = array ();
//		$cdata = '';
//		
//		if ($this->getContext()->getUser()->isAuthenticated()) {
//			
//			$user = $this->getContext()->getUser();
//			
//			// To debug some session/cookie/user problems
//			$cdata .= sprintf('// User: %s (id=%d)', $user->getNsmUser()->user_name, $user->getNsmUser()->user_id). chr(10)
//			. sprintf('// Tstamp: %s', $this->getContext()->getTranslationManager()->_d(time())). chr(10)
//			. chr(10);
//			
//			$data = $this->getContext()->getUser()->getPrefVal(AppKitExtApplicationStateFilter::DATA_NAMESPACE, null, true);
//			
//			if ($data !== null) {
//				$data = unserialize(base64_decode($data));
//			}
//		}
//		
//		return sprintf(
//			'%sExt.onReady(function() {'. "\n"
//			. "\t". 'AppKit.Ext.setAppState(%s);'. "\n"
//			. '});'. "\n", $cdata, json_encode($data)
//		);
		
	}
}

?>