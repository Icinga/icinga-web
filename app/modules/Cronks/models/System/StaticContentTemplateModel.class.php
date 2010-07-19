<?php

class Cronks_System_StaticContentTemplateModel extends CronksBaseModel {

	private $tid = null;
	private $ts	= array ();
	private $ds = array ();
	private $template = null;
	private $args = array ();

	private static $tcache			= array ();
	private static $protected_vars	= array ('t', 'a');

	public function initialize(AgaviContext $context, array $parameters = array()) {
		parent::initialize($context, $parameters);

		$this->api = $this->getContext()->getModel('Icinga.ApiContainer', 'Web');

		$this->template = $this->getParameter('template');
		$this->ds = $this->getParameter('datasources', array ());
		$this->ts = $this->getParameter('templates', array ());

		$this->tid = $this->getParameter('tid', microtime(true));
	}

	private function setCache($name, &$value, $type='data') {
		if (!isset(self::$tcache[$this->tid])) {
			self::$tcache[$this->tid] = array ();
			self::$tcache[$this->tid][$type] = array ();
		}

		self::$tcache[$this->tid][$type][$name] =& $value;

		return $value;
	}

	private function getCache($name, $type='data') {
		if (isset(self::$tcache[$this->tid][$type][$name])) {
			return self::$tcache[$this->tid][$type][$name];
		}
	}

	private function evalPhp($code) {
		$t =& $this;
		$a =& $this->args;

		// Expand the arguments
		foreach ($this->args as $k=>$v) {
			${$k} = $v;
		}

		return eval('?> ' . $code . ' <?');
	}

	private function appendArguments(array $args) {
		foreach (self::$protected_vars as $v) {
			if (array_key_exists($v, $args)) {
				unset($args[$v]);
			}
		}

		$this->args = (array)$args + $this->args;

		return true;
	}

	private function substituteArguments(array &$args) {
		static $tp = null;

		if ($tp===null) {
			$tp = new AppKitFormatParserUtil();
			$tp->registerNamespace('arg', AppKitFormatParserUtil::TYPE_ARRAY);
			$tp->setDefault('NOT_FOUND');
		}
		
		$tp->registerData('arg', $this->args);

		foreach ($args as $key=>$val) {
			if (is_array($val)) {
				$this->substituteArguments($args[$key]);
			}
			else {
				 $args[$key] = $tp->parseData($val);
			}
		}

		return $args;
	}

	private function dsRecursiveWalk(array &$array, array $method) {

		$id = $method['name'];
		if (!($reflection = $this->getCache($id, 'method'))) {
			$reflection = new ReflectionFunction($method['name']);
			$this->setCache($id, $reflection, 'method');
		}

		$aargs = array();
		if (isset($method['param'])) {
			$aargs = explode(',', $method['param']);
		}

		foreach ($array as $key=>$val) {
			if (is_array($val)) {
				$this->dsRecursiveWalk($array[$key], $method);
			}
			else {
				$args = array($val);
				foreach ($aargs as $aarg) {
					$args[] = $aarg;
				}
				$array[$key] = $reflection->invokeArgs($args);
			}
		}

		return $array;
	}

	private function getDsArray($name, array $filters=array(), $index=false) {

		if (array_key_exists($name, $this->ds)) {

			$dataSource = $this->ds[$name];

			if (!array_key_exists('target', $dataSource)) {
				throw new Cronks_System_StaticContentTemplateException('Datasource \'%s\' needs attribute target!', $name);
			}
			else {

				$apiSearch = $this->api->getConnection()
				->createSearch()
				->setResultType(IcingaApi::RESULT_ARRAY)
				->setSearchTarget(constant($dataSource['target']));

				// set search type
				if (array_key_exists('search_type', $dataSource)) {
					$apiSearch->setSearchType(constant($dataSource['search_type']));
				}

				if (array_key_exists('columns', $dataSource)) {
					$columns = explode(',', $dataSource['columns']);
					foreach ($columns as $currentColumn) {
						$apiSearch->setResultColumns(trim($currentColumn));
					}
				}
				
				if (array_key_exists('limit', $dataSource)) {
					$apiSearch->setSearchLimit(0, (int)$dataSource['limit']);
				}

				if (count($filters)) {
					foreach ($filters as $f) {
						if (!isset($f[2])) $f[2] = IcingaApi::MATCH_EXACT;
						$apiSearch->setSearchFilter($f[0], $f[1], $f[2]);
					}
				}

				/*
				 * @todo Filters missing!
				 */

				IcingaPrincipalTargetTool::applyApiSecurityPrincipals($apiSearch);

				$res = $apiSearch->fetch();

				if ($name=='SERVICE_STATUS_SUMMARY') {
//					var_dump($apiSearch);
				}

				$d = $res->getAll();


				if (is_array($d)) {
					if ($res->getResultCount() > 0) {
						if ($index !== false) {
							if (isset($d[$index])) {
								$d = $d[$index];
							}
						}

						if (isset($dataSource['function'])) {
							$this->dsRecursiveWalk($d, $dataSource['function']);
						}
					}
				}

				return $d;

			}
		}

	}

	// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	// INTERFACE METHODS
	// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	public function templateExists($name) {
		return array_key_exists($name, $this->ts);
	}

	public function templateCode($name) {
		if ($this->templateExists($name)) {
			return $this->ts[$name];
		}

		throw new Cronks_System_StaticContentTemplateException('Template %s does not exist', $name);
	}

	public function renderTemplate($name, array $args=array()) {
		$this->appendArguments($args);
		$this->substituteArguments($this->args);
		ob_start();
		$re = $this->evalPhp($this->templateCode($name));
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	public function renderSub($file, $name='MAIN', array $args=array()) {
		if (!($tmpl = $this->getCache($file, 'template'))) {
			$tmpl = $this->getContext()->getModel('System.StaticContent', 'Cronks');
			$tmpl->setTemplateFile($file);
			$this->setCache($file, $tmpl, 'template');
		}
		return $tmpl->parseTemplate($name, $args);
	}

	public function dsCachedField($name, $field) {
		$data = $this->getCache($name);
		
		if (!$data) {
			$data = $this->setCache($name, $this->getDsArray($name, array(), 0));
		}

		if (array_key_exists($field, $data)) {
			return $data[$field];
		}
	}

	public function ds2Array($name, $filter=array(), $index=false, $keyfield=null) {
		$data =  $this->getDsArray($name, $filter, $index);
		if (is_array($data) && $keyfield !== null && isset($data[0][$keyfield])) {
			$out = array ();
			foreach ($data as $key=>$val) {
				$out[$val[$keyfield]] = $val;
			}
			$data = $out;
		}
		return $data;
	}

	public function ds2template($name, $template, $count_name='count', array $args = array(), array $filter=array()) {
		$data = $this->getDsArray($name, $filter);
		$content = '';

		foreach ($data as $c=>$row) {
			$args = $row + $args;
			$args[$count_name] = ($c+1);
			$content .= $this->renderTemplate($template, $args);
		}

		return $content;
	}

	public function dsExpandVars($name, array $filter=array()) {
		$data = $this->getDsArray($name, $filter, 0);
		$this->appendArguments($data);
		return $data;
	}

}

class Cronks_System_StaticContentTemplateException extends AppKitException {
	
}

?>