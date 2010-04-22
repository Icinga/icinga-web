<?php

class Cronks_System_CronkDataModel extends ICINGACronksBaseModel {

	const CONFIG_CATEGORIES	= 'modules.cronks.categories';
	const CONFIG_CRONKS		= 'modules.cronks.cronks';

	const IMAGE_DEFAULT		= 'cronks.default';
	
	const F_HIDDEN			= 1;
	const F_GROUP			= 2;
	const F_DISABLED		= 4;
	const F_SECURITY		= 8;
	const F_VISIBLE			= 16;
	const F_IMAGE			= 32;
	const F_CATEGORY		= 64;
	
	/**
	 * Predefined filter names for cronks
	 * @var array
	 */
	private static $F_LIST	= array (
		'list'	=> 111,
		'exec'	=> 70
	);
	
	/**
	 * Working filter in this instance
	 * @var integer
	 */
	private $filter			= 127;
	
	/**
	 * Array of cronks
	 * @var array
	 */
	private $cronks			= array();
	
	/**
	 * Array of categories
	 * @var array
	 */
	private $categories		= array();
	
	/**
	 * Our user
	 * @var AppKitSecurityUser
	 */
	private $user			= null;
	
	/**
	 * Array of roles
	 * @var array
	 */
	private $roles			= array ();
	
	/**
	 * A sort flag to return all sub arrays sorted
	 * @var boolean
	 */
	private $sort			= false;
	
	/**
	 * (non-PHPdoc)
	 * @see lib/agavi/src/model/AgaviModel#initialize($context, $parameters)
	 */
	public function initialize($context, $parameters) {
				
		parent::initialize($context, $parameters);
		
		$this->user = $this->getContext()->getUser();
		$this->roles = $this->user->getRoles();

		if (array_key_exists('filter', $parameters)) {
			$this->applyFilter($parameters['filter']);
		}
		
		if (array_key_exists('sort', $parameters)) {
			$this->sort = (bool)$parameters['sort'];
		}

		$this->categories = $this->filterData(AgaviConfig::get(self::CONFIG_CATEGORIES), 21);
		
		$this->cronks = $this->filterData(AgaviConfig::get(self::CONFIG_CRONKS));
		
		if ($this->sort == true) {
			$this->sortBySubcol($this->categories, array('title' => SORT_ASC));
			$this->sortBySubcol($this->cronks, array('name' => SORT_ASC));
		}
	}
	
	/**
	 * Checks values agains nummeric or string values and 
	 * sets the integer value
	 * @param mixed $val
	 * @return integer
	 */
	private function applyFilter($val) {
		if (is_numeric($val)) {
			$this->filter = (int)$val;
		}
		elseif (is_string($val) && array_key_exists($val, self::$F_LIST)) {
			$this->filter = (int)self::$F_LIST[$val];
		}
		
		return $this->filter;
	}
	
	/**
	 * Initially filters all the cronks 
	 * @param array $cronks
	 * @param integer $f
	 * @return unknown_type
	 */
	private function filterData(array $data=array(), $f=null) {
		
		if ($f==null) {
			$f = $this->filter;
		}
		
		if (!is_int($f) || $f <= 0) {
			throw new AppKitModelException('Filter is not an integer of below zero!');
		}
		
		$out = array ();
		foreach ($data as $key=>$i) {
			if (is_array($i) && count($i)) {
				
				if	($f & self::F_DISABLED && array_key_exists('disabled', $i) && $i['disabled'] == true) continue;
				elseif ($f & self::F_DISABLED && array_key_exists('visible', $i) && $i['visible'] !== true) continue;
				elseif ($f & self::F_HIDDEN &&	array_key_exists('hide', $i) && $i['hide'] == true) continue;
				elseif ($f & self::F_GROUP && array_key_exists('groupsonly', $i) && $this->checkGroup($i['groupsonly']) !== true) continue;
				
				if ($f & self::F_SECURITY) {
					unset($i['module']);
					unset($i['action']);
				}
				
				if ($f & self::F_IMAGE) {
					if (!array_key_exists('image', $i)) {
						$i['image'] = self::IMAGE_DEFAULT;
					}
					
					$i['image'] = AppKitHtmlHelper::Obj()->imageUrl($i['image']);
				} 
				
				if ($f & self::F_CATEGORY) {
					
					if (!array_key_exists('parameter', $i)) {
						$i['parameter'] = array();
					}
					
					if (array_key_exists('ae:parameter', $i)) {
						$i['parameter'] += (array)$i['ae:parameter'];
						unset($i['ae:parameter']);
					}
				}
				
				$out[$key] = $i;
			}
		}
		
		return $out;		
	}
	
	private function sortBySubcol(&$array, array $orders=array()) {
		$arg_array = array ();
		$tmp = array ();
		foreach ($orders as $col=>$order) {
				$tmp[$col] = array();
				foreach ($array as $key=>$subcols) {
					if (array_key_exists($col, $subcols)) {
						$tmp[$col][$key] = $subcols[$col];
					}
				}
				
				$arg_array[] =& $tmp[$col];
				$arg_array[] =& $order;
		}
		
		$arg_array[] =& $array;
		
		$re = @call_user_func_array('array_multisort', $arg_array);
		return $re;
	}
	
	private function checkGroup($role_string) {
		return $this->testArrayIntersects($role_string, $this->roles);
	}
	
	private function testArrayIntersects($totest, array $against=array()) {
		if (!is_array($totest) && is_string($totest)) $totest = split(',', $totest);
		$t = array_intersect($totest, $against);
		if (is_array($t) && count($t)>0) {
			return true;
		}
		return false;
	}
	
	public function getCronks() {
		return $this->cronks;
	}
	
	public function getCronksByCategory($category_key, $asarray=false, $keyname='id') {
		if (!is_array($category_key)) $category_key = array ($category_key);
		$out=array();
		foreach ($this->cronks as $key=>$i) {
			if (array_key_exists('categories', $i) && $this->testArrayIntersects($i['categories'], $category_key)) {
				if ($asarray==true) {
					$i[$keyname] = $key;
					$out[] = $i;
				}
				else {
					$out[$key] = $i;
				}
			}
		}
		
		if ($this->sort == true) {
			$this->sortBySubcol($out, array('name' => SORT_ASC));
		}
		
		return $out;
	}
	
	public function getCronk($key) {
		if ($this->hasCronk($key)) {
			return $this->cronks[$key];
		}
	}
	
	public function hasCronk($key) {
		return array_key_exists($key, $this->cronks);
	}
	
	public function getCategories() {
		return $this->categories;
	}
	
	
}

?>