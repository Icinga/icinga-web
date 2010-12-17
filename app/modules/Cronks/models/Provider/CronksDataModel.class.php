<?php

class Cronks_Provider_CronksDataModel extends CronksBaseModel {

	private static $cat_map = array (
		'title'		=> 'cc_name',
		'visible'	=> 'cc_visible',
		'position'	=> 'cc_position'
	); 
	
	/**
	 * @var array
	 */
	private $principals = array ();
	
	/**
	 * @var NsmUser
	 */
	private $user = null;
	
	private $categories = array();
	
	public function initialize(AgaviContext $context, array $parameters = array()) {
		parent::initialize($context, $parameters);
		
		$user = $this->getContext()->getUser();
		
		if ($user->isAuthenticated()===true) {
			$this->user = $user->getNsmUser();
			$this->setPrincipals($this->user->getPrincipalsArray());
		}
		else {
			throw new AppKitModelException('The model need an authenticated user');
		}
		
		if ($this->hasParameter('categories')) {
			$this->setCategories($this->getParameter('categores'));
		}
	}
	
	public function setCategories($list) {
		if (is_array($list)) {
			$this->categories = $list;
		}
		else {
			$this->categories = AppKitArrayUtil::trimSplit($list, ',');
		}
		return true;
	}
	
	public function setPrincipals(array $p) { 
		$this->principals = $p;
	}
	
	private function getXMLCategories() {
		$categories = AgaviConfig::get('modules.cronks.categories');
		$out = array ();
		foreach ($categories as $category) {
			$out[ $category['title'] ] = array (
				'title'		=> $category['title'],
				'visible'	=> isset($category['visible']) ? $category['visible'] : true,
				'active'	=> isset($category['active']) ? $category['active'] : false,
				'position'	=> isset($category['position']) ? $category['position'] : 0
			);
		}
		return $out;
	}
	
	private function getDbCategories($get_all=false) {
		$collection = Doctrine_Query::create()
		->select('cat.*')
		->from('CronkCategory cat');
		
		if ($get_all !== true) {
			
			$p = $this->principals;
			$p[] = $this->user->user_id;
			
			$collection->innerJoin('cat.Cronk c')
			->innerJoin('c.NsmPrincipal p')
			->andWhereIn('p.principal_id', $u);
		}
		
		$res = $collection->execute();
		
		$out = array ();
		
		foreach ($res as $category) {
			$out[$category->cc_name] = array (
				'title'		=> $category->cc_name,
				'visible'	=> (bool)$category->cc_visible,
				'active'	=> true,
				'position'	=> (int)$category->cc_position
			);
		}
		
		return $out;
	}
	
	public function getCategories($get_all=false) {
		
		$categories = $this->getXMLCategories();
		$categories = (array)$this->getDbCategories($get_all) + $categories;
		
		AppKitArrayUtil::subSort($categories, 'title');
		AppKitArrayUtil::subSort($categories, 'position');		
		
		foreach ($categories as $cid=>$category) {
			if (!$category['visible']) {
				unset($categories[$cid]);
			}
		}
		
		return $categories;
	}
	
	public function createCategory(array $cat) {
		AppKitArrayUtil::swapKeys($cat, self::$cat_map);

		$category = new CronkCategory();
		$category->fromArray($cat);
		$category->save();
		
		return $category;
	}
	
	private function checkGroups($listofnames) {
		$groups = AppKitArrayUtil::trimSplit($listofnames, ',');
		if (is_array($groups) && count($groups)) {
			$c = Doctrine_Query::create()
			->select('r.role_id')
			->from('NsmRole r')
			->innerJoin('r.NsmUserRole ur WITH ur.usro_user_id=?', $this->user->user_id)
			->whereIn('r.role_name', $groups)
			->count();
			
			if ($c === 1) {
				return true;
			}
		}
		return false;
	}
	
	private function getXmlCronks() {
		$cronks = AgaviConfig::get('modules.cronks.cronks');
		
		$out = array ();
		
		foreach ($cronks as $uid=>$cronk) {
			
			if (isset($cronk['groupsonly']) && $this->checkGroups($cronk['groupsonly']) !== true) {
				continue;
			}
			elseif (isset($cronk['disabled']) && $cronk['disabled'] == true) {
				continue;
			}
			elseif (isset($cronk['hide']) && $cronk['hide'] == true) {
				continue;
			}
			elseif (!isset($cronk['action']) || !isset($cronk['module'])) {
				$this->getContext()->getLoggerManager()->log('No action or module for cronk: '. $uid, AgaviLogger::ERROR);
				continue;
			}
			
			
			$out[$uid] = array (
				'module'		=> $cronk['module'],
				'action'		=> $cronk['action'],
				'hide'			=> isset($cronk['hide']) ? (bool)$cronk['hide'] : false,
				'description'	=> isset($cronk['description']) ? $cronk['description'] : null,
				'name'			=> isset($cronk['name']) ? $cronk['name'] : null,
				'categories'	=> isset($cronk['categories']) ? $cronk['categories'] : null,
				'image'			=> isset($cronk['image']) ? $cronk['image'] : null,
				'disabled'		=> isset($cronk['disabled']) ? (bool)$cronk['disabled'] : false,
				'groupsonly'	=> isset($cronk['groupsonly']) ? $cronk['groupsonly'] : null,
			);
		}
		
		return $out;
	}
	
	private function xml2array($xml) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML($xml);
		$root = $dom->documentElement;
		
				
		$out = array ();
		
		AppKitArrayUtil::xml2Array($root->childNodes, $out);
		
		return $out;
	}
	
	private function cronkStructure(Cronk $cronk) {
		$c = $this->xml2array($cronk->cronk_xml);
		$out = array ();
		foreach ($c as $cuid=>$cd) {
			$out[$cronk->cronk_uid] = array (
					'module'		=> $cd['module'],
					'action'		=> $cd['action'],
					'hide'			=> isset($cd['hide']) ? (bool)$cd['hide'] : false,
					'description'	=> $cronk->cronk_description ? $cronk->cronk_description : $cd['description'],
					'name'			=> $cronk->cronk_name ? $cronk->cronk_name : $cd['name'],
					'categories'	=> isset($cd['categories']) ? $cd['categories'] : null,
					'image'			=> isset($cd['image']) ? $cd['image'] : null,
					'disabled'		=> isset($cd['disabled']) ? (bool)$cd['disabled'] : false,
					'groupsonly'	=> isset($cd['groupsonly']) ? $cd['groupsonly'] : null,
			);
		}
		return $out;
	}
	
	private function getDbCronks() {
		
		$p = $this->principals;
		$p[] = $this->user->user_id;
		
		$cronks = Doctrine_Query::create()
		->from('Cronk c')
		->innerJoin('c.CronkPrincipalCronk cpc')
		->andWhereIn('cpc.cpc_principal_id', $p)
		->execute();
		
		$out = array ();
		
		foreach ($cronks as $cronk) {
			$cronks2 = $this->cronkStructure($cronk);
			foreach ($cronks2 as $cid=>$cdata) {
				$out[$cid] = $cdata;
			}
		}
		
		return $out;
	}
	
	public function getCronks() {
		$cronks = $this->getXmlCronks();
		$cronks = (array)$this->getDbCronks() + $cronks;
		
		AppKitArrayUtil::subSort($cronks, 'name');
		
		return $cronks;
	}
}

?>