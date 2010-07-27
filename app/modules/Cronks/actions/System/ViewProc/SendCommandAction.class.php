<?php

class Cronks_System_ViewProc_SendCommandAction extends CronksBaseAction
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
	public function getDefaultViewName()
	{
		return 'Success';
	}
	
	public function executeRead(AgaviParameterHolder $rd) {
		return $this->getDefaultViewName();
	}
	
	public function executeWrite(AgaviParameterHolder $rd) {
		$data = json_decode( $rd->getParameter('data') );
		$selection = json_decode( $rd->getParameter('selection') );
		$command = $rd->getParameter('command');
		$auth = $rd->getParameter('auth');
		
		$IcingaApiCommand = $this->getContext()->getModel('Icinga.ApiContainer', 'Web');
		
		// The model
		$sender = $this->getContext()->getModel('System.CommandSender', 'Cronks');
		
		if ($sender->checkAuth($rd->getParameter('command'), $rd->getParameter('selection'), $rd->getParameter('data'), $auth) === true) {
			
			// Prepare the data
			$sender->setCommandName($command);
			$sender->setSelection($selection);
			$sender->setData($data);
			
			// Prepare the data structures
			$coa = $sender->buildCommandObjects();
			
			if ($IcingaApiCommand->checkDispatcher() !== true) {
				$this->setAttribute('ok', false);
				$this->setAttribute('error', 'No command dispatchers configured!');
				
				return $this->getDefaultViewName();
			}
			
			// Send the bundle
			try {
				$IcingaApiCommand->dispatchCommandArray($coa);
			}
			catch (IcingaApiCommandException $e) {
				$errors = $IcingaApiCommand->getLastErrors();
				$error = array();
				foreach ($errors as $err) {
					$error[] = sprintf('%s: %s', get_class($err), $err->getMessage());
				}
				
				$this->setAttribute('ok', false);
				$this->setAttribute('error', join(', ', $error));
				
				return $this->getDefaultViewName();
			}
		
			$this->setAttribute('ok', true);
			$this->setAttribute('error', null);
		}
		else {
			$this->setAttribute('ok', false);
			$this->setAttribute('error', 'Authentification failed');
			$this->getContext()->getLoggerManager()
			->logError('Command auth failed!');
		}
		
		return $this->getDefaultViewName();
	}
	
	public function isSecure() {
		return true;
	}
	
	public function getCredentials() {
		return array ('icinga.user');
	}
	
	public function handleError(AgaviParameterHolder $rd) {
		$this->setAttribute('ok', false);
		$this->setAttribute('error', 'Validation failed');
		return $this->getDefaultViewName();
	}
}

?>