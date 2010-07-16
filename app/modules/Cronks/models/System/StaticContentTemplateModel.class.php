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

	private function setCache($name, &$value) {
		if (!isset(self::$tcache[$this->tid])) {
			self::$tcache[$this->tid] = array ();
		}

		self::$tcache[$this->tid][$name] =& $value;

		return $value;
	}

	private function getCache($name) {
		if (isset(self::$tcache[$this->tid][$name])) {
			return self::$tcache[$this->tid][$name];
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

	private function getDsArray($name) {

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


				/*
				 * @todo Filters missing!
				 */

				IcingaPrincipalTargetTool::applyApiSecurityPrincipals($apiSearch);

				$res = $apiSearch->fetch();
				$d = $res->getAll();

				if ($res->getResultCount() == 1) {
					$d = $d[0];
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

	public function render($name, $args=array()) {
		$this->appendArguments($args);
		ob_start();
		$re = $this->evalPhp($this->templateCode($name));
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	public function dsCachedField($name, $field) {
		$data = $this->getCache($name);

		if (!$data) {
			$data = $this->setCache($name, $this->getDsArray($name));
		}

		if (array_key_exists($field, $data)) {
			return $data[$field];
		}
	}

	public function ds2Array($name, $filter=array()) {
//		if (is_array( ($data = $this->getCache($name)) )) {
//			return $data;
//		}

//		return $this->setCache($name, $this->getDsArray($name));

		return $this->getDsArray($name);
	}

	public function ds2template($name, $template, $count_name, array $args = array(), array $filter=array()) {
		$data = $this->getDsArray($name, $filter);
		$content = '';

		foreach ($data as $c=>$row) {
			$args = $row + $args;
			$args[$count_name] = ($c+1);
			$content .= $this->render($template, $args);
		}

		return $content;
	}

}

class Cronks_System_StaticContentTemplateException extends AppKitException {
	
}

?>