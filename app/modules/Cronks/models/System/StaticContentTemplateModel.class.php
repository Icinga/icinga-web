<?php

class Cronks_System_StaticContentTemplateModel extends CronksBaseModel {

	const CACHE_DEFAULT				= 'data';
	const TEMPLATE_MAIN				= 'MAIN';
	const TEMPLATE_PRESET			= 'icinga-tactical-overview-presets';
	const CSS_CLASS_LINK			= 'x-icinga-grid-link';

	private $tid					= null;
	private $ts						= array ();
	private $ds						= array ();
	private $chain                  = array ();
	private $rparam                 = array ();
	private $args					= array ();
	private $js_code				= array ();

	private static $tcache			= array ();
	private static $protected_vars	= array ('t', 'a');
	private static $idc				= 0;

	public function initialize(AgaviContext $context, array $parameters = array()) {
		parent::initialize($context, $parameters);

		$this->api = $this->getContext()->getModel('Icinga.ApiContainer', 'Web');

		$this->ds = $this->getParameter('datasources', array ());
		$this->ts = $this->getParameter('templates', array ());
		$this->chain = $this->getParameter('chain', array ());
		$this->rparam = $this->getParameter('rparam', array ());
		
		$this->tid = $this->getOid();
	}

	private function hasCacheEntry($name, $type=self::CACHE_DEFAULT) {
		return isset(self::$tcache[$this->tid][$type][$name]);
	}

	private function setCache($name, &$value, $type=self::CACHE_DEFAULT) {
		if (!isset(self::$tcache[$this->tid])) {
			self::$tcache[$this->tid] = array ();
			self::$tcache[$this->tid][$type] = array ();
		}

		self::$tcache[$this->tid][$type][$name] =& $value;

		return $value;
	}

	private function getCache($name, $type=self::CACHE_DEFAULT) {
		if (isset(self::$tcache[$this->tid][$type][$name])) {
			return self::$tcache[$this->tid][$type][$name];
		}
	}

	private function getUid($prefix='touid-') {
		$id = str_replace('.', '-', $this->tid. '-'. microtime(true). '-');
		return $id. $prefix. sprintf('%03d', (++self::$idc));
	}

	private function evalPhp($code, array &$args=null) {
		
		if ($args==null) {
			$args =& $this->args;
		}
		
		$ctx = $this->getContext();
		$ro = $this->getContext()->getRouting();
		$tr = $this->getContext()->getTranslationManager();
		$t =& $this;
		$a =& $args;

		// Expand the arguments
		foreach ($args as $k=>$v) {
			${$k} = $v;
		}
		
		return eval('?> ' . $code . '<?php ');
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

	private function substituteArguments(array &$args, array &$target=null) {
		static $tp = null;

		if ($target == null) {
			$target =& $this->args;
		}

		if ($tp===null) {
			$tp = new AppKitFormatParserUtil();
			$tp->registerNamespace('arg', AppKitFormatParserUtil::TYPE_ARRAY);
			$tp->setDefault('NOT_FOUND');
		}
		
		$tp->registerData('arg', $this->args);

		foreach ($args as $key=>$val) {
			if (is_array($val)) {
				$this->substituteArguments($args[$key], $target);
			}
			elseif (is_object($val)) {
				continue;
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

	private function processDsFiltermap(array $dataSource, array $filter) {
		if (isset($dataSource['filter_mapping']) && is_array($dataSource['filter_mapping'])) {
			$map =& $dataSource['filter_mapping'];
			if (array_key_exists($filter[0], $map)) {
				$filter[0] = $map[ $filter[0] ];
			}
		}
		
		return $filter;
	}

	private function getDsFilters($name, array $additional=array(), $ignore_defined_filters=false) {
	    
	    $out = array ();
	    
		if (array_key_exists($name, $this->ds)) {

			$dataSource = $this->ds[$name];
			
			if (!array_key_exists('target', $dataSource)) {
				throw new Cronks_System_StaticContentTemplateException('Datasource \'%s\' needs attribute target!', $name);
			}
			else {

			    if (!$ignore_defined_filters && array_key_exists('filters', $dataSource)) {
			        
					foreach ($dataSource['filters'] as $filter) {
					    $out[] = array(
					        $filter['field'],
					        $filter['value'], 
					        constant(isset($filter['match']) ? $filter['match'] : 'IcingaApi::MATCH_LIKE')
					    );
					}
					
				}
				
				if (count($additional)) {
					foreach ($additional as $f) {
						if (!isset($f[2])) $f[2] = IcingaApi::MATCH_EXACT;
						$f = $this->processDsFiltermap($dataSource, $f);
						$out[] = array($f[0], $f[1], $f[2]);
					}
				}

			}
		}
		
		return $out;
	}
	
	private function getDsArray($name, array $filters=array(), $index=false, $ignore_defined_filters=false) {

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
				 * @todo Check if we can remove this
				 */
//			    if (array_key_exists('filters', $dataSource)) {
//			        
//					foreach ($dataSource['filters'] as $filter) {
//					    $filter = $apiSearch->createFilter($filter['field'], 
//					        $filter['value'], 
//					        constant(isset($filter['match']) ? $filter['match'] : 'IcingaApi::MATCH_LIKE')
//					    );
//
//                        $apiSearch->setSearchFilter($filter);					    
//					}
//				}
//				
//				if (count($filters)) {
//					foreach ($filters as $f) {
//						if (!isset($f[2])) $f[2] = IcingaApi::MATCH_EXACT;
//						$f = $this->processDsFiltermap($dataSource, $f);
//						$apiSearch->setSearchFilter($f[0], $f[1], $f[2]);
//					}
//				}

				$add_filters = $this->getDsFilters($name, $filters, $ignore_defined_filters);
				foreach ($add_filters as $f) {
				    $apiSearch->setSearchFilter($f[0], $f[1], $f[2]);
				}

				/*
				 * @todo Filters missing!
				 */

				IcingaPrincipalTargetTool::applyApiSecurityPrincipals($apiSearch);

				$res = $apiSearch->fetch();
				
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

	public function dumpData() {
	    return $this->tree;
	}
	
	public function getOid() {
		static $oid=null;
		if ($oid===null) {
			$oid = spl_object_hash($this);
		}
		return $oid;
	}

	public function templateExists($name) {
		return array_key_exists($name, $this->ts);
	}

	public function templateCode($name) {
		if ($this->templateExists($name)) {
			return $this->ts[$name];
		}

		throw new Cronks_System_StaticContentTemplateException('Template %s does not exist', $name);
	}

	public function renderSub($file, $name='MAIN', array $args=array()) {
		if (!($tmpl = $this->getCache($file, 'template'))) {
			$tmpl = $this->getContext()->getModel('System.StaticContent', 'Cronks');
			$tmpl->setTemplateFile($file);
			$this->setCache($file, $tmpl, 'template');
		}

		$new_args = $this->args;

		foreach ($args as $k=>$v) {
			if (array_key_exists($k, $new_args)) {
				unset($new_args[$k]);
			}
		}

		$args = $new_args + $args;

		$to = $tmpl->getTemplateObj();

		$content = $to->renderTemplate($name, $args);

		$this->jsAddCode($to->jsGetCode(false, true));
		return $content;
	}

	public function renderTemplate($name, array $args=array(), $only_local_args=false, $is_root=false) {

		if ($only_local_args == false) {
			$this->appendArguments($args);
			$this->substituteArguments($this->args);
			$args = $this->args;
		}
		elseif (is_array($only_local_args)) {
			foreach ($only_local_args as $k=>$v) {
				if (array_key_exists($v, $this->args) && !array_key_exists($v, $args)) {
					$args[$v] = $this->args[$v];
				}
			}
		}

		if ($only_local_args !== false) {
			$this->substituteArguments($args, $args);
		}
		
		ob_start();
		
		$re = $this->evalPhp($this->templateCode($name), $args);
		
		$error = error_get_last();
		
		if ($error === null) {
		    $content = ob_get_contents();
		} else {
		    $error_message = sprintf(
		        'TO PHP error: %s (Line %d) in template %s!',
		        $error['message'],
		        $error['line'],
		        $name
		    );
		    
		    $content = $error_message;
		}
		ob_end_clean();
		
		if ($name === self::TEMPLATE_MAIN || $is_root==true) {
			$content .= $this->jsGetCode();
		}
		
		return $content;
	}

	public function linkWrap($content, $uid) {
		return (string)  AppKitXmlTag::create('div', $content)
		->addAttribute('id', $uid);
	}

	public function linkFunctionWrapper($js_code, $uid) {
		$code = $this->renderSub(self::TEMPLATE_PRESET, 'js_clickwrap', array (
			'uid'		=> $uid,
			'js_code'	=> $js_code
		));

		$this->jsAddCode($code);

		return $code;
	}

	public function jsChart(array $data, $type, array $config) {
		$uid = $this->getUid();

		$code = $this->renderSub(self::TEMPLATE_PRESET, 'js_simplechart', array (
			'data'		=> $data,
			'uid'		=> $uid,
			'config'	=> $config,
			'type'		=> $type
		));
		
		$this->jsAddCode($code);

		return (string)AppKitXmlTag::create('div')
		->addAttribute('id', $uid);
	}

	public function link2To($content, $template, $title, array $filter=array()) {
		$uid = $this->getUid();

		$fc = new stdClass();

		foreach ($filter as $k=>$v) {
			$fc->{ $k } = $v;
		}

		$code = $this->renderSub(self::TEMPLATE_PRESET, 'js_link2to', array (
			'uid'		=> $uid,
			'template'	=> $template,
			'toTitle'	=> $title,
			'filterObj'	=> $fc
		));

		$this->jsAddCode($code);

		return $this->linkWrap($content, $uid);
	}

	public function link2Grid($content, $template, $title, array $filter=array()) {
		$uid = $this->getUid();

		if (count($filter)==2 && isset($filter[0]) && isset($filter[1])) {
			$filter = array (
				$filter[0] => $filter[1]
			);
		}

		$fc = new stdClass();

		foreach ($filter as $k=>$v) {
			$fc->{ $k } = $v;
		}

		$code = $this->renderSub(self::TEMPLATE_PRESET, 'js_link2grid', array (
			'uid'		=> $uid,
			'template'	=> $template,
			'gridTitle'		=> $title,
			'filterObj'	=> $fc
		));

		$this->jsAddCode($code);

		return $this->linkWrap($content, $uid);
	}

	public function jsAddCode($code) {
		$id = count($this->js_code);
		$this->js_code[] = $code;
		return $id;
	}

	public function jsGetCode($with_tags = true, $flush=false) {
		if (count($this->js_code)) {
			$content = "\n". implode("\n\n", $this->js_code). "\n";

			if ($with_tags == true) {
				$content = "\n". (string)AppKitXmlTag::create('script', $content)
				->addAttribute('type', 'text/javascript'). "\n";
			}

			if ($flush) {
				$this->jsFlushData();
			}

			return $content;
		}
	}

	public function jsFlushData() {
		$this->js_code = array ();
	}

	public function dsCachedField($name, $field) {
		$data = $this->getCache($name);
		
		if (!$data) {
			$dsArray =  $this->getDsArray($name, array(), 0);
			$data = $this->setCache($name,$dsArray);
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

	public function ds2Template($name, $template, $count_name='count', array $args = array(), array $filter=array(), $only_local_args=false) {
		$data = $this->getDsArray($name, $filter);
		$content = '';

		foreach ($data as $c=>$row) {
			$args = $row + $args;
			$args[$count_name] = ($c+1);
			$content .= $this->renderTemplate($template, $args, $only_local_args);
		}
		return $content;
	}

	public function dsExpandVars($name, array $filter=array()) {
		$data = $this->getDsArray($name, $filter, 0);
		$this->appendArguments($data);
		return $data;
	}
	
	public function renderFilterchain($id=0) {
	    $chains = $this->chain;
	    
	    if (isset($chains[$id])) {
	        
	        $chain = $chains[$id];
	        
	        $chain_filters = array ();
	        if (isset($this->rparam['filter_appendix'])) {
	            $parts = explode('|', $this->rparam['filter_appendix']);
	            $chain_filters[] = explode(',', array_pop($parts));
	        }
	        
	        $subfilters = $this->getDsFilters($chain['datasource'], $chain_filters, ($id==0) ? false : true);
	        
	        $data = $this->getDsArray($chain['datasource'], $chain_filters, false, ($id==0) ? false : true);
	        	        
	        return $this->renderTemplate($chain['template'], array (
	            'chain'      => $chain,
	            'chainid'    => $id,
	            'hasnext'    => isset($chains[$id+1]) ? true : false,
	            'hasprev'    => isset($chains[$id-1]) ? true : false,
	            'data'       => $data,
	            'subfilter'  => $subfilters
	        ));
	    } else {
	        return sprintf('Filterchain with id %d not found!', $id);
	    }
	}

}

class Cronks_System_StaticContentTemplateException extends AppKitException {
	
}

?>