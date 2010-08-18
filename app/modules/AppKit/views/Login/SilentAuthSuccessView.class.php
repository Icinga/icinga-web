<?php

class AppKit_Login_SilentAuthSuccessView extends AppKitBaseView {

	public function executeHtml(AgaviRequestDataHolder $rd) {

		if ($this->getAttribute('authenticated') == true) {
			
		}
		else {
			// return $this->createForwardContainer('AppKit', 'Login.AjaxLogin', null, null, 'read');
		}
		
	}
	
}

?>