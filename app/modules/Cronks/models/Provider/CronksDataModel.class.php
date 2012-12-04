<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}


/**
 * Our provider (readable/writable) from combined cronk
 * data sets (xml and database)
 * @author mhein
 *
 */
class Cronks_Provider_CronksDataModel extends CronksBaseModel implements AgaviISingletonModel {

    const DEFAULT_CRONK_IMAGE    = 'cronks.Folder';
    const DEFAULT_CRONK_OWNER    = 'System';
    const DEFAULT_CRONK_OWNERID  = 0;

    private static $cronk_xml_fields = array(
                                           'module', 'action', 'hide', 'description', 'name',
                                           'categories', 'image', 'disabled', 'groupsonly', 'state',
                                           'ae:parameter', 'disabled', 'position'
                                       );

    private static $cronk_xml_default = array(
                                            'hide'      => false,
                                            'disabled'  => false,
                                            'position'  => 0
                                        );

    private static $cronk_xml_map = array(
                                        'p'         => 'ae:parameter',
                                        'roles'     => 'groupsonly',
                                    );

    private static $xml_cronk_data = array();

    private static $xml_ready = false;

    /**
     * An array full of cronks
     * @var array
     */
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
    
    /**
     * @var Cronks_Provider_CronksSecurityModel
     */
    private $security = null;
    
    /**
     * Category model
     * @var Cronks_Provider_CronkCategoryDataModel
     */
    private $categories = null;

    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        
        $this->agaviUser = $this->getContext()->getUser();
        
        if ($this->agaviUser->isAuthenticated()===true) {
            $this->user = $this->agaviUser->getNsmUser();
            $this->setPrincipals($this->user->getPrincipalsArray());
        } else {
            throw new AppKitModelException('The model need an authenticated user');
        }
        
        $this->initializeXmlData();
        
        $this->cronks = $this->getCronks(true);
        
    }
    
    /**
     * Lazy initializing to avoid circular calls
     * @return Cronks_Provider_CronksDataModel
     */
    private function getSecurityModel() {
        if ($this->security === null) {
            $this->security = $this->getContext()
                ->getModel('Provider.CronksSecurity', 'Cronks', array(
                    'security_only' => true
                ));
        }
        
        return $this->security;
    }
    
    /**
     * Lazy categories model
     * @return Cronks_Provider_CronkCategoryDataModel
     */
    private function getCategoryModel() {
        if ($this->categories === null) {
            $this->categories = $this->getContext()
            ->getModel('Provider.CronkCategoryData', 'Cronks');
        }
        
        return $this->categories;
    }

    /**
     * Fills the static xml cache variables with agavi config cache data of
     * cronks and categories. This method is called only if the first instance
     * of this model is initiated
     * @throws AgaviParseException If XML parsin fails
     * @return boolean If cache is parsed
     */
    private function initializeXmlData() {

        if (self::$xml_ready===true) {
            return true;
        }

        $tmp = include(AgaviConfigCache::checkConfig(AgaviConfig::get('core.config_dir'). '/cronks.xml'));
        self::$xml_cronk_data = (array)$tmp[0] + self::$xml_cronk_data;

        return self::$xml_ready=true;
    }

    /**
     * Returns true if the cronk exists in the stack
     * @param string $cronkid
     * @return boolean
     */
    public function hasCronk($cronkid) {
        return array_key_exists($cronkid, $this->cronks);
    }

    /**
     * Rerturn the cronk record
     * @param string $cronkid
     * @return mixed cronk struct of type array
     */
    public function getCronk($cronkid) {
        return $this->cronks[$cronkid];
    }

    /**
     * Set principals interface to the world
     * @param array $p
     */
    public function setPrincipals(array $p) {
        $this->principals = $p;
    }

    /**
     * Check if the user blongs to this groups
     * @param string$listofnames comma separated list of group names
     */
    private function checkGroups($listofnames) {
        $groups = AppKitArrayUtil::trimSplit($listofnames, ',');

        if (is_array($groups) && count($groups)) {
            $c = AppKitDoctrineUtil::createQuery()
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

    /**
     * Check if a user can access this principals
     * @param string $listofprincipals comma separated string of principals
     */
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

    /**
     * Return a list of cronks defined in xml
     * @param boolean $all
     * @return array
     */
    private function getXmlCronks($all=false) {
        $out = array();

        foreach(self::$xml_cronk_data as $uid=>$cronk) {
            
            /*
             * Database credentials overwrite xml credentials
             */
            $this->getSecurityModel()->setCronkUid($uid);
            if ($this->getSecurityModel()->hasDatabaseRoles()) {
                $cronk['groupsonly'] = $this->getSecurityModel()->getRoleNamesAsString();
            }
            
            if (isset($cronk['groupsonly']) 
                && $this->checkGroups($cronk['groupsonly']) !== true
              && $this->agaviUser->hasCredential('icinga.cronk.admin') === false 
           ) {
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
                'cronkid' => $uid,
                'module' => $cronk['module'],
                'action' => $cronk['action'],
                'hide' => isset($cronk['hide']) ? (bool)$cronk['hide'] : false,
                'description' => isset($cronk['description']) ? $cronk['description'] : null,
                'name' => isset($cronk['name']) ? $cronk['name'] : null,
                'categories' => isset($cronk['categories']) ? $cronk['categories'] : null,
                'image' => isset($cronk['image']) ? $cronk['image'] : self::DEFAULT_CRONK_IMAGE,
                'disabled' => isset($cronk['disabled']) ? (bool)$cronk['disabled'] : false,
                'filter' => isset($cronk['filter']) ? $cronk['filter'] : "{}",
                'groupsonly' => isset($cronk['groupsonly']) ? $cronk['groupsonly'] : null,
                'state' => isset($cronk['state']) ? $cronk['state'] : null,
                'ae:parameter' => isset($cronk['ae:parameter']) ? $cronk['ae:parameter'] : null,
                'system' => true,
                'owner' => false,
                'position' => isset($cronk['position']) ? $cronk['position'] : 0,
                'owner_name' => self::DEFAULT_CRONK_OWNER,
                'owner_id' => self::DEFAULT_CRONK_OWNERID
                         );
        }
        
        return $out;
    }

    /**
     * Creates a cronk array structure based on xml
     * @param string $xml
     * @return array
     */
    private function xml2array($xml) {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($xml);
        $root = $dom->documentElement;

        $out = array();

        AppKitArrayUtil::xml2Array($root->childNodes, $out);

        return $out;
    }

    /**
     * Create a cronk array structure based on database cronk recprd
     * @return array
     * @param Cronk $cronk
     */
    private function cronkStructure(Cronk $cronk) {
        $c = $this->xml2array($cronk->cronk_xml);
        
        $out = array();
        foreach($c as $cuid=>$cd) {
            
            $this->getSecurityModel()->setCronkUid($cronk->cronk_uid);
            
            $out[$cronk->cronk_uid] = array(
                'cronkid' => $cronk->cronk_uid,
                'module' => $cd['module'],
                'action' => $cd['action'],
                'hide' => isset($cd['hide']) ? (bool)$cd['hide'] : false,
                'description' => $cronk->cronk_description ? $cronk->cronk_description : $cd['description'],
                'name' => $cronk->cronk_name ? $cronk->cronk_name : $cd['name'],
                'categories' => isset($cd['categories']) ? $cd['categories'] : null,
                'image' => isset($cd['image']) ? $cd['image'] : self::DEFAULT_CRONK_IMAGE,
                'disabled' => isset($cd['disabled']) ? (bool)$cd['disabled'] : false,
                'groupsonly' => $this->getSecurityModel()->getRoleNamesAsString(),
                "filter" => $cronk->cronk_filter ? $cronk->cronk_filter : "{}",
                'state' => isset($cd['state']) ? $cd['state'] : null,
                'ae:parameter' => isset($cd['ae:parameter']) ? $cd['ae:parameter'] : null,
                'system' => false,
                'owner' => ($this->user->user_id == $cronk->cronk_user_id) ? true : false,
                'position' => isset($cd['position']) ? $cd['position'] : 0,
                'owner_name' => $cronk->NsmUser->user_name,
                'owner_id' => $cronk->NsmUser->user_id
            );
        }

        return $out;
    }

    /**
     * Return array of cronks from database
     * @param boolean $get_all
     * @return array
     */
    private function getDbCronks($get_all = false) {

        $p = $this->principals;

        $query = AppKitDoctrineUtil::createQuery()
                  ->from('Cronk c');

        if ($get_all === false
           && $this->agaviUser->hasCredential('icinga.cronk.admin')===false) {
            $query->innerJoin('c.CronkPrincipalCronk cpc')
            ->andWhereIn('cpc.cpc_principal_id', $p);
        }
        
        /*
         * Don't want system credential entries
         */
        $query->andWhere('c.cronk_system=?', false);
        
        $cronks = $query->execute();
        
        $out = array();

        foreach($cronks as $cronk) {
            $cronks2 = $this->cronkStructure($cronk);
            foreach($cronks2 as $cid=>$cdata) {
                $out[$cid] = $cdata;
            }
        }

        return $out;
    }

    /**
     * Get all cronks defined in system
     * @param boolean $all
     * @return array
     */
    public function getCronks($all=false) {
        $cronks = $this->getXmlCronks($all);
        $cronks = (array)$this->getDbCronks() + $cronks;

        $this->reorderCronks($cronks);

        return $cronks;
    }
    
    /**
     * Return all cronks belongs to a specific category
     * @param string $category_uid category id
     * @return array
     */
    public function getCronksByCategory($category_uid) {
        $out = array();
        $cronks = $this->getCronks(true); // Get all
        foreach ($cronks as $cid=>$cronk) {
            if (isset($cronk['categories']) 
                && AppKitArrayUtil::matchAgainstStringList($cronk['categories'], $category_uid)) {
                $out[$cid] = $cronk;
            }
        }
        return $out;
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
                        // To avoid "unterminated entity reference" warnings /
                        // exceptions, putt all into cdata section
                        $cdata = $dom->createCDATASection($sv);
                        $se = $dom->createElement('ae:parameter');
                        $se->appendChild($cdata);
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
                            /*
                             * Do not save the group attributes within the XML.
                             * We add them while fetching
                             */
                            $value = '';
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

        $ccollection = AppKitDoctrineUtil::createQuery()
                       ->from('CronkCategory cc')
                       ->andWhereIn('cc.cc_uid', $carr)
                       ->execute();

        foreach($ccollection as $category) {
            $cronk->CronkCategory[] = $category;
        }

        return $cronk;
    }

    /**
     * Apply users to the cronk, owner
     * shared principals from groups, ...
     * 
     * @param Cronk $cronk
     * @param string $roles
     * @return Cronk the modified record
     */
    private function cronkBuildPrincipalDependencies(Cronk $cronk, $roles) {
        
        $parr = array();
        
        /*
         * Adding existing user principals back to 
         * cronk ( == owner of the cronk)
         */
        foreach ($cronk->CronkPrincipalCronk as $cpc) {
            if ($cpc->NsmPrincipal->principal_user_id) {
                $parr[] = $cpc->NsmPrincipal->principal_id;
            }
        }
        
        /*
         * If no user principals available, this must be new:
         * -> defining a new owner of the object
         */
        if (count($parr)<=0) {
            $parr = $this->user->principal->principal_id;
        }

        $rarr = AppKitArrayUtil::trimSplit($roles, ',');

        $cronk->CronkPrincipalCronk->delete();

        if (is_array($rarr)) {

            $principals = AppKitDoctrineUtil::createQuery()
                          ->select('p.principal_id')
                          ->from('NsmPrincipal p')
                          ->innerJoin('p.NsmRole r')
                          ->andWhereIn('r.role_id', $rarr)
                          ->execute();

            foreach($principals as $principal) {
                $parr[] = (integer)$principal->principal_id;
            }
        }

        $principals = AppKitDoctrineUtil::createQuery()
                      ->select('p.principal_id')
                      ->from('NsmPrincipal p')
                      ->andWhereIn('p.principal_id', $parr)
                      ->execute();

        foreach($principals as $principal) {
            $cronk->NsmPrincipal[] = $principal;
        }
        
        /*
         * If the cronk is new,
         * no native owner record is set, do this!
         */
        if (!$cronk->NsmUser->user_id) {
            $cronk->NsmUser = $this->user;
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
        
        if (
            $this->agaviUser->hasCredential('icinga.cronk.custom') === false
           && $this->agaviUser->hasCredential('icinga.cronk.admin') === false) {
            throw new AppKitModelException('No access to create cronks!');
        }
        
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
        }

        $record->cronk_description = $data['description'];
        $record->cronk_name = $data['name'];
        $record->cronk_xml = $dom->saveXML($dom);

        $this->cronkBuildCategoriesFromString($record, $data['categories']);
        /*
         * Apply owner, shared users and stuff
         * like this.
         */
        $this->cronkBuildPrincipalDependencies($record, isset($data['roles']) ? $data['roles'] : null);
        
        return $record;
    }

    public function deleteCronkRecord($cronkid, $cronkname, $own=true) {
        
        if (
            $this->agaviUser->hasCredential('icinga.cronk.custom') === false
           && $this->agaviUser->hasCredential('icinga.cronk.admin') === false) {
            throw new AppKitModelException('No access to delete cronks!');
        }
        
        $q = AppKitDoctrineUtil::createQuery()
             ->select('c.*')
             ->from('Cronk c')
             ->where('c.cronk_uid=?', array($cronkid));

        if ($own==true 
           && $this->agaviUser->hasCredential('icinga.cronk.admin') === false) {
            $q->andWhere('c.cronk_user_id=?', array($this->user->user_id));
        }

        $cronk = $q->execute()->getFirst();

        if ($cronk instanceof Cronk && $cronk->cronk_id > 0) {
            AppKitDoctrineUtil::getConnection()->beginTransaction();
            
            $params = array($cronk->cronk_id);
            
            AppKitDoctrineUtil::createQuery()->delete('CronkCategoryCronk c')
            ->andWhere('c.ccc_cronk_id=?')
            ->execute($params);
            
            AppKitDoctrineUtil::createQuery()->delete('CronkPrincipalCronk c')
            ->andWhere('c.cpc_cronk_id=?')
            ->execute($params);
            
            AppKitDoctrineUtil::getConnection()->commit();
            
            $cronk->delete();

            return true;
        } else {
            throw new AppKitModelException('Could not delete cronk: '. $cronkid);
        }
    }

    public function combinedData() {
        $cat_out = array();

        $cronks_out = array();
        
        $categories = $this->getCategoryModel()->getCategories();
        
        $cronks = $this->getCronks();

        foreach($categories as $category_name=>$category) {
            $tmp = array();

            foreach($cronks as $cronk) {
                if (AppKitArrayUtil::matchAgainstStringList($cronk['categories'], $category_name)) {
                    $tmp[] = $cronk;
                }
            }
            
            if (($count = count($tmp))) {
                $cronks_out[$category_name] = array(
                    'rows' => $tmp,
                    'success' => true,
                    'total' => $count
                );
                $cat_out[] = $category;
            }
        }

        $data = array(
            'categories'    => $cat_out,
            'cronks'        => $cronks_out
        );

        return $data;
    }
    
    /**
     * Sorting of cronks based on position flag in the cronk records
     * @param array $cronks
     * @return array
     */
    private function reorderCronks(array &$cronks) {
        
        $c_ids = array();
        $c_names = array();
        $c_positions = array();
        
        foreach ($cronks as $id=>$cronk) {
            $c_ids[$id] = $cronk['cronkid'];
            $c_names[$id] = $cronk['name'];
            $c_positions[$id] = (int)$cronk['position'];
        }
        array_multisort($c_positions, SORT_ASC, $c_names, SORT_STRING, $c_ids, SORT_STRING, $cronks);
        
        return $cronks;
    }

}
