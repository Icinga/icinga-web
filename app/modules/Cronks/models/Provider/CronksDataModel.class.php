<?php

class Cronks_Provider_CronksDataModel extends CronksBaseModel {

    private static $cat_map = array(
                                  'catid'		=> 'cc_uid',
                                  'title'		=> 'cc_name',
                                  'visible'	=> 'cc_visible',
                                  'position'	=> 'cc_position'
                              );

    private static $cronk_xml_fields = array(
                                           'module', 'action', 'hide', 'description', 'name',
                                           'categories', 'image', 'disabled', 'groupsonly', 'state',
                                           'ae:parameter', 'disabled'
                                       );

    private static $cronk_xml_default = array(
                                            'hide'		=> false,
                                            'disabled'	=> false
                                        );

    private static $cronk_xml_map = array(
                                        'p'			=> 'ae:parameter',
                                        'roles'		=> 'groupsonly',
                                    );

    private $cronks = array();

    /**
     * @var array
     */
    private $principals = array();

    /**
     * @var NsmUser
     */
    private $user = null;

    /**
     * @var AppKitSecurityUser
     */
    private $agaviUser = null;

    private $categories = array();

    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);

        $this->agaviUser = $this->getContext()->getUser();

        if ($this->agaviUser->isAuthenticated()===true) {
            $this->user = $this->agaviUser->getNsmUser();
            $this->setPrincipals($this->user->getPrincipalsArray());
        } else {
            throw new AppKitModelException('The model need an authenticated user');
        }

        if ($this->hasParameter('categories')) {
            $this->setCategories($this->getParameter('categores'));
        }

        $this->cronks = $this->getCronks(true);
    }

    public function hasCronk($cronkid) {
        return array_key_exists($cronkid, $this->cronks);
    }

    public function getCronk($cronkid) {
        return $this->cronks[$cronkid];
    }

    public function setCategories($list) {
        if (is_array($list)) {
            $this->categories = $list;
        } else {
            $this->categories = AppKitArrayUtil::trimSplit($list, ',');
        }

        return true;
    }

    public function setPrincipals(array $p) {
        $this->principals = $p;
    }

    private function getXMLCategories() {
        $categories = AgaviConfig::get('modules.cronks.categories');
        $out = array();
        foreach($categories as $cid=>$category) {
            $out[ $cid ] = array(
                               'catid'		=> $cid,
                               'title'		=> $category['title'],
                               'visible'	=> isset($category['visible']) ? $category['visible'] : true,
                               'active'	=> isset($category['active']) ? $category['active'] : false,
                               'position'	=> isset($category['position']) ? $category['position'] : 0,
                               'system'	=> true
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
            ->andWhereIn('p.principal_id', $this->principals);
        }

        $res = $collection->execute();

        $out = array();

        foreach($res as $category) {
            $out[$category->cc_uid] = array(
                                          'catid'		=> $category->cc_uid,
                                          'title'		=> $category->cc_name,
                                          'visible'	=> (bool)$category->cc_visible,
                                          'active'	=> true,
                                          'position'	=> (int)$category->cc_position,
                                          'system'	=> false
                                      );
        }

        return $out;
    }

    public function getCategories($get_all=false, $show_invisible=false) {

        if ($show_invisible == true && !$this->agaviUser->hasCredential('icinga.cronk.category.admin')) {
            $show_invisible = false;
        }

        $cronks = $this->getCronks(true);
        $categories = $this->getXMLCategories();
        $categories = (array)$this->getDbCategories($get_all) + $categories;

        AppKitArrayUtil::subSort($categories, 'title');
        AppKitArrayUtil::subSort($categories, 'position');

        foreach($categories as $cid=>$category) {
            $count = 0;
            foreach($cronks as $cronk) {
                if (isset($cronk['categories']) && $this->matchCategoryString($cronk['categories'], $cid)) {
                    $count++;
                }
            }
            $categories[$cid]['count_cronks'] = $count;

            if (!$category['visible'] && !$show_invisible) {
                unset($categories[$cid]);
            }
        }

        return $categories;
    }

    public function deleteCategoryRecord($cc_uid) {
        if ($this->agaviUser->hasCredential('icinga.cronk.category.admin') && isset($cc_uid)) {
            $res = Doctrine_Query::create()
                   ->delete('CronkCategory cc')
                   ->andWhere('cc.cc_uid=?', array($cc_uid))
                   ->limit(1)
                   ->execute();

            if ($res == 1) {
                return true;
            }
        }

        return false;
    }

    public function createCategory(array $cat) {
        AppKitArrayUtil::swapKeys($cat, self::$cat_map, true);

        $category = null;

        if ($this->agaviUser->hasCredential('icinga.cronk.category.admin') && isset($cat['cc_uid'])) {
            $category = Doctrine_Query::create()
                        ->from('CronkCategory cc')
                        ->andWhere('cc.cc_uid=?', $cat['cc_uid'])
                        ->execute()->getFirst();
        }

        if (!$category instanceof CronkCategory || !$category->cc_id > 0) {
            $category = new CronkCategory();
        }

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

    private function checkPrincipals($listofprincipals) {
        $principals = AppKitArrayUtil::trimSplit($listofprincipals);

        if (is_array($principals)) {
            foreach($principals as $principal) {
                if ($this->agaviUser->hasCredential($principal)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function getXmlCronks($all=false) {
        $cronks = AgaviConfig::get('modules.cronks.cronks');

        $out = array();

        foreach($cronks as $uid=>$cronk) {

            if (isset($cronk['groupsonly']) && $this->checkGroups($cronk['groupsonly']) !== true) {
                continue;
            }

            elseif(isset($cronk['principalsonly']) && $this->checkPrincipals($cronk['principalsonly']) !== true) {
                continue;
            }
            elseif(isset($cronk['disabled']) && $cronk['disabled'] == true) {
                continue;
            }
            elseif($all == false && isset($cronk['hide']) && $cronk['hide'] == true) {
                continue;
            }
            elseif(!isset($cronk['action']) || !isset($cronk['module'])) {
                $this->getContext()->getLoggerManager()->log('No action or module for cronk: '. $uid, AgaviLogger::ERROR);
                continue;
            }


            $out[$uid] = array(
                             'cronkid'		=> $uid,
                             'module'		=> $cronk['module'],
                             'action'		=> $cronk['action'],
                             'hide'			=> isset($cronk['hide']) ? (bool)$cronk['hide'] : false,
                             'description'	=> isset($cronk['description']) ? $cronk['description'] : null,
                             'name'			=> isset($cronk['name']) ? $cronk['name'] : null,
                             'categories'	=> isset($cronk['categories']) ? $cronk['categories'] : null,
                             'image'			=> isset($cronk['image']) ? $cronk['image'] : null,
                             'disabled'		=> isset($cronk['disabled']) ? (bool)$cronk['disabled'] : false,
                             'groupsonly'	=> isset($cronk['groupsonly']) ? $cronk['groupsonly'] : null,
                             'state'			=> isset($cronk['state']) ? $cronk['state'] : null,
                             'ae:parameter'	=> isset($cronk['ae:parameter']) ? $cronk['ae:parameter'] : null,
                             'system'		=> true,
                             'owner'			=> false
                         );
        }

        return $out;
    }

    private function xml2array($xml) {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($xml);
        $root = $dom->documentElement;

        $out = array();

        AppKitArrayUtil::xml2Array($root->childNodes, $out);

        return $out;
    }

    private function cronkStructure(Cronk $cronk) {
        $c = $this->xml2array($cronk->cronk_xml);
        $out = array();
        foreach($c as $cuid=>$cd) {
            $out[$cronk->cronk_uid] = array(
                                          'cronkid'		=> $cronk->cronk_uid,
                                          'module'		=> $cd['module'],
                                          'action'		=> $cd['action'],
                                          'hide'			=> isset($cd['hide']) ? (bool)$cd['hide'] : false,
                                          'description'	=> $cronk->cronk_description ? $cronk->cronk_description : $cd['description'],
                                          'name'			=> $cronk->cronk_name ? $cronk->cronk_name : $cd['name'],
                                          'categories'	=> isset($cd['categories']) ? $cd['categories'] : null,
                                          'image'			=> isset($cd['image']) ? $cd['image'] : null,
                                          'disabled'		=> isset($cd['disabled']) ? (bool)$cd['disabled'] : false,
                                          'groupsonly'	=> isset($cd['groupsonly']) ? $cd['groupsonly'] : null,
                                          'state'			=> isset($cd['state']) ? $cd['state'] : null,
                                          'ae:parameter'	=> isset($cd['ae:parameter']) ? $cd['ae:parameter'] : null,
                                          'system'		=> false,
                                          'owner'			=> ($this->user->user_id == $cronk->cronk_user_id) ? true : false
                                      );
        }

        return $out;
    }

    private function getDbCronks() {

        $p = $this->principals;

        $cronks = Doctrine_Query::create()
                  ->from('Cronk c')
                  ->innerJoin('c.CronkPrincipalCronk cpc')
                  ->andWhereIn('cpc.cpc_principal_id', $p)
                  ->execute();

        $out = array();

        foreach($cronks as $cronk) {
            $cronks2 = $this->cronkStructure($cronk);
            foreach($cronks2 as $cid=>$cdata) {
                $out[$cid] = $cdata;
            }
        }

        return $out;
    }

    public function getCronks($all=false) {
        $cronks = $this->getXmlCronks($all);
        $cronks = (array)$this->getDbCronks() + $cronks;

        AppKitArrayUtil::subSort($cronks, 'name');

        return $cronks;
    }

    /**
     * @param array $data
     * @return DOMDocument
     */
    private function createCronkDom(array $data) {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $root = $dom->createElement('cronk');

        // Agavi config namespace
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ae', 'http://agavi.org/agavi/config/global/envelope/1.0');

        $dom->appendChild($root);

        $cronk = $dom->createElement('ae:parameter');
        $cronk->setAttribute('name', $data['cid']);

        $root->appendChild($cronk);

        foreach($data as $name => $value) {

            if (isset(self::$cronk_xml_map[$name])) {
                $name = self::$cronk_xml_map[$name];
            }

            if (in_array($name, self::$cronk_xml_fields)) {

                $ele = $dom->createElement('ae:parameter');

                if (is_array($value)) {

                    foreach($value as $sn=>$sv) {
                        $se = $dom->createElement('ae:parameter', $sv);
                        $se->setAttribute('name', $sn);
                        $ele->appendChild($se);
                    }
                } else {
                    switch ($name) {
                        case 'state':
                            $cdata = $dom->createCDATASection($value);
                            $ele->appendChild($cdata);
                            unset($value);
                            break;

                        case 'groupsonly':

                            $roles = AppKitArrayUtil::trimSplit($value, ',');

                            $arry = Doctrine_Query::create()
                                    ->select('r.role_name')
                                    ->from('NsmRole r INDEXBY r.role_name')
                                    ->andWhereIn('r.role_id', $roles)
                                    ->execute(null, Doctrine::HYDRATE_ARRAY);

                            if (isset($arry) && is_array($arry)) {
                                $value = implode(',', array_keys($arry));
                            }

                            break;

                        case 'hide':
                            if ($value && $value == 'on') {
                                $value = 'true';
                            } else {
                                $value = 'false';
                            }

                            break;

                        case 'disabled':
                            $value = 'false';
                            break;

                        case 'image':
                            $value = 'cronks.'. $value;
                            break;
                    }

                    if (isset($value)) {
                        $text = $dom->createTextNode($value);
                        $ele->appendChild($text);
                    }
                }

                if (isset($ele)) {
                    $ele->setAttribute('name', $name);
                    $cronk->appendChild($ele);
                }
            }

        }


        return $dom;
    }

    private function cronkBuildCategoriesFromString(Cronk $cronk, $categories) {
        $carr = AppKitArrayUtil::trimSplit($categories, ',');

        $cronk->CronkCategoryCronk->delete();

        $ccollection = Doctrine_Query::create()
                       ->from('CronkCategory cc')
                       ->andWhereIn('cc.cc_uid', $carr)
                       ->execute();

        foreach($ccollection as $category) {
            $cronk->CronkCategory[] = $category;
        }

        return $cronk;
    }

    private function cronkBuildRoleDepencies(Cronk $cronk, $roles) {

        $parr = array($this->user->NsmPrincipal->principal_id);

        $rarr = AppKitArrayUtil::trimSplit($roles, ',');

        $cronk->CronkPrincipalCronk->delete();

        if (is_array($rarr)) {
            $principals = Doctrine_Query::create()
                          ->select('p.principal_id')
                          ->from('NsmPrincipal p')
                          ->innerJoin('p.NsmRole r')
                          ->andWhereIn('r.role_id', $rarr)
                          ->execute();

            foreach($principals as $principal) {
                $parr[] = $principal->principal_id;
            }
        }

        $principals = Doctrine_Query::create()
                      ->select('p.principal_id')
                      ->from('NsmPrincipal p')
                      ->andWhereIn('p.principal_id', $parr)
                      ->execute();

        foreach($principals as $principal) {
            $cronk->NsmPrincipal[] = $principal;
        }

        return $cronk;
    }

    /**
     *
     * Enter description here ...
     * @param array $data
     * @param boolean $load
     * @throws AppKitModelException
     * @return Cronk
     */
    public function createCronkRecord(array $data, $load = true) {

        if (!isset($data['cid'])) {
            throw new AppKitModelException('cid is needed for record creation/loading (Cronk UID)');
        }

        $data = self::$cronk_xml_default + $data;

        $dom = $this->createCronkDom($data);

        $record = null;

        if ($load == true) {
            $record = Doctrine::getTable('Cronk')->findBy('cronk_uid', $data['cid'])->getFirst();
        }

        if (!$record instanceof Cronk) {
            $record = new Cronk();
            $record->cronk_uid = $data['cid'];
            $record->NsmUser = $this->user;
        }

        $record->cronk_description = $data['description'];
        $record->cronk_name = $data['name'];
        $record->cronk_xml = $dom->saveXML($dom);

        $this->cronkBuildCategoriesFromString($record, $data['categories']);

        $this->cronkBuildRoleDepencies($record, isset($data['roles']) ? $data['roles'] : null);

        return $record;
    }

    public function deleteCronkRecord($cronkid, $cronkname, $own=true) {
        $q = Doctrine_Query::create()
             ->select('c.*')
             ->from('Cronk c')
             ->where('c.cronk_uid=? and c.cronk_name=?', array($cronkid, $cronkname));

        if ($own==true) {
            $q->andWhere('c.cronk_user_id=?', array($this->user->user_id));
        }

        $cronk = $q->execute()->getFirst();

        if ($cronk instanceof Cronk && $cronk->cronk_id > 0) {
            Doctrine_Manager::getInstance()->getCurrentConnection()->beginTransaction();
            $cronk->CronkCategoryCronk->delete();
            $cronk->CronkPrincipalCronk->delete();
            $cronk->save();
            Doctrine_Manager::getInstance()->getCurrentConnection()->commit();

            $cronk->delete();

            return true;
        } else {
            throw new AppKitModelException('Cronk not found: '. $cronkid);
        }
    }

    public function combinedData() {
        $cat_out = array();

        $cronks_out = array();

        $categories = $this->getCategories();

        $cronks = $this->getCronks();

        foreach($categories as $category_name=>$category) {
            $tmp = array();

            foreach($cronks as $cronk) {
                if ($this->matchCategoryString($cronk['categories'], $category_name)) {
                    $tmp[] = $cronk;
                }

            }

            if (count($tmp)) {
                $cronks_out[$category_name] = array(
                                                  'rows'		=> $tmp,
                                                  'success'	=> true,
                                                  'total'		=> count($tmp)
                                              );
                $cat_out[] = $category;
            }
        }

        $data = array(
                    'categories'	=> $cat_out,
                    'cronks'		=> $cronks_out
                );

        return $data;
    }

    private function matchCategoryString($categories, $match) {
        $match=preg_quote($match);
        return preg_match('/(^|,)'. $match. '(,|$)/i', $categories);
    }
}

?>
