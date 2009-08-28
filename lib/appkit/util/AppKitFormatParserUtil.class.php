<?php

class AppKitFormatParserUtil {
	const TYPE_DATA			= 1;
	const TYPE_ARRAY		= 2;
	const TYPE_METHOD		= 3;
	const TYPE_CLASS		= 4;
	
	private $namespaces		= array ();
	private $data			= array ();
	private $default		= null;
	
	public function __construct() {
		
	}
	
	public function registerNamespace($name, $type) {
		if (!$this->namespaceExists($name)) {
			$this->namespaces[$name] = $type;
			return true;
		}
		
		throw new AppKitFormatParserUtilException('Namespace exists already!');
	}
	
	public function namespaceExists($name) {
		return array_key_exists($name, $this->namespaces);
	}
	
	public function registerData($namespace, &$data) {
		
		if (!$this->namespaceExists($namespace))
			throw new AppKitFormatParserUtilException('Namespace does not exists');
		
		$params = func_get_args();
		$namespace = array_shift($params);
		$data = array_shift($params);
		$data2 = array_shift($params);
		
		if ($data2) {
			$this->data[$namespace][$data] = $data2;
		}
		elseif ($data) {
			$this->data[$namespace] = $data;
		}
		
		return true;
	}
	
	public function setDefault($val) {
		$this->default = $val;
	}
	
	public function parseData($format) {
		$m = array();
		
		if (preg_match_all('@\$\{([^\}]+)\}@', $format, $m, PREG_SET_ORDER)) {
			foreach ($m as $match) {
				$parts = split('\.', $match[1]);
				$namespace = array_shift($parts);
				
				$replace = null;
				$data =& $this->getData($namespace);
				
				switch ($this->getNamespaceType($namespace)) {
					
					case self::TYPE_ARRAY:
						if (count($parts) == 1) {
							$replace = $data[$parts[0]];
						}
					break;
					
					default: 
						
						if ($match[1] == '*' && $this->default !== null) {
							$replace = $this->default;
						}
						
						if ($replace == null) $replace = '((('. $match[0].' not implemented!!!)))';
					break;
					
				}
				
				$format = preg_replace('@'. preg_quote($match[0]). '@', $replace, $format);
				
			}
		}
		
		return $format;
	}
	
	private function getNamespaceType($namespace) {
		if ($this->namespaceExists($namespace)) {
			return $this->namespaces[$namespace];
		}
		
		return null;
	}
	
	private function getData($namespace) {
		if ($this->namespaceExists($namespace)) {
			return $this->data[$namespace];
		}
		
		return null;
	}
	
}

class AppKitFormatParserUtilException extends AppKitException {}

?>