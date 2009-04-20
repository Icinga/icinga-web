<?php 

class AppKitFactories extends AppKitSingleton {
	
	private $factories = array ();
	
	public static function loadFactoriesFromConfig($namespace) {
		$config = AgaviConfig::get($namespace);
		foreach ($config as $name=>$params) {
			$params['name'] = $name;
			self::getInstance()->addFactory($params['name'], $params['class'], $params);
		}
	}
	
	/**
	 * 
	 * @return AppKitFactories
	 */
	public static function getInstance() {
		return parent::getInstance('AppKitFactories');
	}
	
	public function __destruct() {
		foreach ($this->factories as $name => $factory) {
			
			$ref = $this->getFactoryReflection($name);
			
			if ($ref->hasMethod('shutdownFactory')) {
				$method = $ref->getMethod('shutdownFactory');
				$method->invoke($this->getFactory($name));
			}
			
			unset($this->factories[$name]);
		}
	}
	
	public function addObject($name, AppKitFactory &$factory) {
		if ($this->factoryExists($name)) {
			throw new AppKitFactoryException('This factory exists, remove it first!');
		}
		
		$this->factories[$name] =& $factory;
		
		return $this->factories[$name];
	}
	
	public function removeFactory($name) {
		if ($this->factoryExists($name)) {
			$ref = $this->getFactoryReflection($name);
			if ($ref->hasMethod('shutdownFactory')) {
				$method = $ref->getMethod('shutdownFactory');
				$method->invoke($this->getFactory($name));
			}
			
			unset($this->factories[$name]);
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * 
	 * @param string $name
	 * @return ReflectionObject
	 */
	public function getFactoryReflection($name) {
		if ($this->factoryExists($name)) {
			return new ReflectionObject($this->getFactory($name));
		}
		
		return null;
	}
	
	public function getFactory($name) {
		if ($this->factoryExists($name)) {
			return $this->factories[$name];
		}
		
		return null;
	}
	
	public function addFactory($name, $className, array $parameters) {
		$ref = new ReflectionClass($className);
		
		if ($this->factoryExists($name)) {
			throw new AppKitFactoryException('This factory exists, remove it first!');
		}
		elseif (!$ref->implementsInterface('AppKitFactoryInterface')) {
			throw new AppKitFactoryException('A factory class must extend AppKitFactory');
		}
		elseif (!$ref->hasMethod('initializeFactory')) {
			throw new AppKitFactoryException('A factory class must implement the initializeFactory method');
		}
		elseif (!$ref->isInstantiable()) {
			throw new AppKitFactoryException('Factoryclass is not instantiable');
		}
		
		$factory = $ref->newInstance();
		$method = $ref->getMethod('initializeFactory');
		$method->invoke($factory, $parameters);
		
		$this->factories[$name] =& $factory;
		
		return $this->factories[$name];
	}
	
	public function factoryExists($name) {
		if (array_key_exists($name, $this->factories) && $this->factories[$name] instanceof AppKitFactoryInterface) {
			return true;
		}
		
		return false;
	}
	
}

class AppKitFactoryException extends AppKitException {}

?>