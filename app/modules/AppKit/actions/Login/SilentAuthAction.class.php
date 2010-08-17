<?php

class AppKit_Login_SilentAuthAction extends AppKitBaseAction
{
	/**
	 * Returns the default view if the action does not serve the request
	 * method used.
	 *
	 * @return     mixed <ul>
	 *                     <li>A string containing the view name associated
	 *                     with this action; or</li>
	 *                     <li>An array with two indices: the parent module
	 *                     of the view to be executed and the view to be
	 *                     executed.</li>
	 *                   </ul>
	 */
	public function getDefaultViewName() {
		return 'Success';
	}

	public function execute(AgaviRequestDataHolder $rd) {
		$dispatch = $this->getContext()->getModel('Auth.Dispatch', 'AppKit');

		$username = $rd->getParameter('username', false);
		
		if ($username == false) {
			$username = $dispatch->guessUsername();
		}

		if ($username !== false) {

			$user = $this->getContext()->getUser();

			try {
				$user->doLogin($username, null, false);
				$this->setAttribute('authenticated', true);
			}
			catch (AgaviSecurityException $e) {
				$this->setAttribute('authenticated', false);
			}

		}

		return $this->getDefaultViewName();
	}

	public function handleError(AgaviRequestDataHolder $rd) {
		return $this->getDefaultViewName();
	}
}

?>