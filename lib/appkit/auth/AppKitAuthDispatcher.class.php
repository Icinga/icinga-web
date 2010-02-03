<?php
/**
 * AuthDispatcher, globally ask one instance to get answer from
 * many providers
 * 
 * @author mhein
 *
 */
class AppKitAuthDispatcher extends AppKitFactory implements AppKitEventHandlerInterface {
	
	/**
	 * Array of all provider instances
	 * @var array
	 */
	private $provider		= array ();
	
	/**
	 * Order of the providers
	 * @var array
	 */
	private $order			= array (); 
	
	private $s_step			= 0;
	
	private $s_match		= array ();
	
	private $s_user			= null;
	
	
	/**
	 * (non-PHPdoc)
	 * @see lib/appkit/class/AppKitFactory#initializeFactory($parameters)
	 */
	public function initializeFactory(array $parameters=array()) {
		parent::initializeFactory($parameters);
		AppKitEventDispatcher::getInstance()->addListener('appkit.bootstrap', $this);
		return true;
	}
	
	private function initAuthProviders() {
		$carr = $this->getParameter('provider', array ());
		
		foreach ($carr as $key=>$cdata) {
			
			if (isset($cdata['class']) && isset($cdata['enabled']) && $cdata['enabled'] == true) {
				$instance = AppKit::getInstance($cdata['class']);
				
				if ($instance instanceof AppKitAuthProvider) {
					$instance->initializeAuthProvider($cdata);
					
					$this->provider[$key] = $instance;
					$this->order[] = $key;
				}
				
			}
		}
		
		return true;
	}
	
	public function handleEvent(AppKitEvent &$event) {
		return $this->initAuthProviders();
	}
	
	private function checkStep($num, $throw=true) {
		if ($this->s_step == $num) {
			return true;
		}
		
		if ($throw == true) {
			throw new AppKitAuthDispatcherException('Wrong auth step, sould=%d, is=%d. Call resetAll() to start a new auth!', $num, $this->s_step);
		}
		
	}
	
	public function isUserAvailable($username) {
		// Check if we're on the beginning
		$this->checkStep(0);
		
		// Count on
		$this->s_step++;
		
		// Check string
		if (!$username || !strlen($username)>0) return null;
		
		// Try to load from DB
		$res = Doctrine_Query::create()
		->from('NsmUser')
		->andWhere('user_disabled=? and user_name=?', array(false, $username))
		->execute();
		
		if ($res->count() == 1 && ($user = $res->getFirst()) instanceof NsmUser) {
			$this->s_step++;
			$this->s_user =& $user;
			return $this->s_user; 
		}
		
		/*
		 * @todo Check the other providers and import maybe!
		 */
		
		
//		foreach ($this->provider as $provider) {
//			if ($provider->isUserAvailable($user)) {
//					
//			}
//		}
		
		return false;
	}
	
	public function isAuthenticated($username, $password) {
		
		$this->checkStep(2);
		$this->s_step++; // Should 3 now 
		if ($this->s_user instanceof NsmUser) {
			foreach ($this->order as $pkey) {

				if ($this->provider[$pkey]->isAuthenticated($this->s_user, $password) === true) {
					$this->s_step++; // now 4?!					
					return true;
				}
				
			}
		}
		
		return false;
	}
	
	public function resetAll() {
		$this->s_step	= 0;
		$this->s_match	= array ();
		$this->s_user	= null;
		
		return true;
	}
	
}

class AppKitAuthDispatcherException extends AppKitException {}

?>