<?php

class AppKit_AjaxLoginSuccessView extends ICINGAAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);
		// $this->setAttribute('title', 'Login');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {
		
		$authenticated = false;
		$errors = array();
		$user = $this->getContext()->getUser();
		
		if ($this->getAttribute('authenticated', false) === true && $user->isAuthenticated() && $this->getAttribute('executed', false) === true) {
			$authenticated = true;
		}
		else {
			$errors['username'] = 'Login failed!';
		}
		
		return json_encode(array(
			'success'		=> $authenticated,
			'errors'		=> $errors
		));
		
	}
}

?>