<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class AppKitModuleUtil extends AppKitSingleton {

	const DEFAULT_NAMESPACE	= 'org.icinga.global';

	protected static $default_config_keys = array (
		'app.javascript_files'		=> 'flat',
		'app.javascript_actions'	=> false,
		'app.css_files'				=> 'flat',
		'app.meta_tags'				=> false
	);

	private $modules = null;

	private $s_configns = array();
	private $s_modnames = array();

	/**
	 * @return AppKitModuleUtil
	 */
	public static function  getInstance() {
		return parent::getInstance(__CLASS__);
	}

	public function  __construct() {
		parent::__construct();
		$this->modules = new ArrayObject();
	}

	public static function normalizeModuleName($module) {
		return strtolower($module);
	}

	public static function validConfig($module) {
		AppKitModuleUtil::normalizeModuleName($module);
		if (AgaviConfig::get(sprintf('modules.%s.version', false)) !== false) {
			return true;
		}

		return false;
	}

	public function isRegistered($module) {
		$module = $this->normalizeModuleName($module);
		return $this->modules->offsetExists($module);
	}

	/**
	 *
	 * @param <type> $module
	 * @return AppKitModuleConfigItem
	 */
	public function registerModule($module) {
		if (!$this->isRegistered($module) && AppKitModuleUtil::validConfig($module)) {
			$this->modules[$module] =
				new AppKitModuleConfigItem($module);
		}

		return $this->modules[$module];
	}

	/**
	 *
	 * @param <type> $module
	 * @return AppKitModuleConfigItem
	 */
	public function getConfigObject($module) {
		if ($this->isRegistered($module)) {
			return $this->modules[$module];
		}

		throw new AppKitModelException('The module %s does not exit!', $this->normalizeModuleName($module));
	}

	public function getValidConfigNamespaces() {
		if (!count($this->s_configns)) {
			foreach ($this->modules as $module=>$ci) {
				foreach ($ci->getConfigNamespaces() as $config_ns) {
					$this->s_configns[] = $config_ns;
					$this->s_modnames[$config_ns] = $module;
				}
			}
		}
		return $this->s_configns;
	}

	public function getSubConfig($subkey, $flat=true) {
		$out = array ();
		foreach ($this->getValidConfigNamespaces() as $ns) {
			$test = $ns. '.'. $subkey;

			if (($data = AgaviConfig::get($test, false)) !== false) {
				$out[$subkey][isset($this->s_modnames[$ns]) ? $this->s_modnames[$ns] : $ns] = $data;
			}
		}
		
		if ($flat==true)
			$out = AppKitArrayUtil::flattenArray($out, 'sub');

		return $out;
	}

	public function getWholeConfig() {
		$out=array();
		foreach (self::$default_config_keys as $subkey=>$subtype) {
			$out[$subkey] = $this->getSubConfig($subkey, ($subtype=='flat' ? true : false));
		}
		return $out;
	}

	public function applyToRequestAttributes(AgaviExecutionContainer &$container, array $which_subkeys=null, $ns=self::DEFAULT_NAMESPACE) {
		if ($which_subkeys===null)
			$which_subkeys = self::$default_config_keys;

		$rq = $container->getContext()->getRequest();

		foreach (self::$default_config_keys as $subkey=>$subtype) {
			$data = $this->getSubConfig($subkey, ($subtype=='flat' ? true : false));
			if (isset($data)) {
				foreach ($data as $value) {
					$rq->appendAttribute($subkey, $value, $ns);
				}
			}
		}
	}
}

class AppKitModuleConfigItem extends AgaviAttributeHolder {

	const NS_INT			= '__module_config_item';
	const DEFAULT_SUB_NS	= 'appkit_module';

	const A_MODULE_NAME		= 'module_name';
	const A_BASE_NS			= 'base_ns';
	const A_CONFIG_NS		= 'config_namespaces';

	protected $defaultNamespace = 'org.icinga.moduleConfig';

	public function  __construct($module) {
		$module = AppKitModuleUtil::normalizeModuleName($module);

		if (AppKitModuleUtil::validConfig($module)) {
			$this->setAttribute(self::A_MODULE_NAME, $module, self::NS_INT);
			$this->setAttribute(self::A_BASE_NS, 'modules.'. $module, self::NS_INT);

			$this->addConfigNamespace('modules.'. $module. '.'. self::DEFAULT_SUB_NS);

			parent::__construct();
		}
		else {
			throw new AppKitModelException('Configuration for module %s not found!', $module);
		}
	}

	public function addConfigNamespace($namespace) {
		$this->appendAttribute(self::A_CONFIG_NS, $namespace, self::NS_INT);
	}
	
	public function getAttributeNamespaces() {
		$ns = parent::getAttributeNamespaces();

		if (($index = array_search(self::NS_INT, $ns)) !== false) {
			unset($ns[$index]);
		}

		return $ns;
	}

	public function  getAttributes($ns = null) {
		parent::getAttributes($ns);
	}

	public function getModuleName() {
		return $this->getAttribute(self::A_MODULE_NAME, self::NS_INT);
	}

	public function getConfigNamespaces() {
		return $this->getAttribute(self::A_CONFIG_NS, self::NS_INT);
	}

}

class AppKitModuleUtilException extends AppKitException {}

?>
