<?php

class IcingaCommand extends AppKitFactory {
	
	private $dispatcher = array ();
	private $errors = array ();
	
	/**
	 * (non-PHPdoc)
	 * @see lib/appkit/class/AppKitFactory#initializeFactory()
	 */
	public function initializeFactory(array $parameters=array()) {
		parent::initializeFactory($parameters);
		
		if (!$this->getParameter('api_file')) throw new AppKitFactoryException('api_file does not exist!');
		
		if (!class_exists('IcingaApi')) {
			$this->includeApiFile($this->getParameter('api_file'));
		}

		$this->initializeDispatcher();
	}
	
	private function initializeDispatcher() {
		$t = $this->getParameter('interfaces');
		if (isset($t) && is_array($t)) {
			foreach ($t as $key=>$interface) {
				if (array_key_exists('enabled', $interface) && $interface['enabled'] === true) {
					
					$config = $interface;
					$type = AppKit::getConstant($interface['type']);
					unset($config['type']);
					unset($config['enabled']);
					
					$this->dispatcher[$key] = IcingaApi::getCommandDispatcher();
					$this->dispatcher[$key]->setInterface($type, $config);
					
				}
			}
		}
		
		if (count($this->dispatcher)) {
			return true;
		}
		
		// Some notice
		// AgaviContext::getInstance()->getLoggerManager()->logWarning('No command dispatcher configured!');
		// throw new AppKitFactoryException('No command dispatcher was configured');
	}
	
	public function checkDispatcher() {
		return (count($this->dispatcher) > 0) ? true : false;
	}
	
	public function dispatchCommand(IcingaApiCommand &$cmd) {
		return $this->dispatchCommandArray(array($cmd));
	}
	
	public function dispatchCommandArray(array $arry) {
		$error = false;
		
		foreach ($this->dispatcher as $d) {
			
			try {
				$d->setCommand($arry);
				$d->send();
			}
			catch (IcingaApiCommandSendException $e) {
				$this->errors[] = $e;
				$error = true;
				
				AgaviContext::getInstance()->getLoggerManager()
				->logError('Command dispatch failed: '.  str_replace("\n", " ", print_r($d->getCallStack(), true)) );
			}
			
			// Reset into ready-state
			$d->clearCommands();
		}
		
		if ($error === true) {
			throw new IcingaCommandException('Errors occured try getLastError to fetch a exception stack!');
		}
		
		return true;
		
	}
	
	/**
	 * Returns an array of exceptions if any
	 * @return array
	 */
	public function getLastErrors() {
		$o = $this->errors;
		$this->errors = array ();
		return $o;
	}
	
	/**
	 * Include the api file
	 * @param string $file
	 * @return boolean
	 */
	private function includeApiFile($file) {
		$re = require_once($file);
		if (!$re) throw new AppKitFactoryException('Could not include the api file!');
		return true;
	}
	
}

class IcingaCommandException extends AppKitFactoryException {}
