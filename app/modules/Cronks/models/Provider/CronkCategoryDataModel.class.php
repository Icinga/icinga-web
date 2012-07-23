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
 * Model to deal with category data
 * @author mhein
 * @package IcingaWeb
 * @subpackage Cronks
 * @since 1.8.0
 */
class Cronks_Provider_CronkCategoryDataModel extends CronksBaseModel
    implements AgaviISingletonModel {
    
    /**
     * XML structure of categories
     * @var array
     */
    private static $xml_categories = array();
    
    /**
     * Information how does a category record look like
     * @var array
     */
    private static $cat_map = array(
            'catid'       => 'cc_uid',
            'title'       => 'cc_name',
            'visible' => 'cc_visible',
            'position'    => 'cc_position'
    );
    
    /**
     * Agavi user interface
     * @var AppKitSecurityUser
     */
    private $agaviUser = null;
    
    /**
     * Database user model
     * @var NsmUser
     */
    private $user = null;
    
    /**
     * Pre fetched principal ids as array
     * @var array
     */
    private $principals = array();
    
    /**
     * Cronk model
     * @var Cronks_Provider_CronksDataModel
     */
    private $cronks = null;
    
    /**
     * (non-PHPdoc)
     * @see CronksBaseModel::initialize()
     */
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        
        // Init cronk categgory configurtion
        $tmp = include(AgaviConfigCache::checkConfig(
            AgaviConfig::get('core.config_dir'). '/cronks.xml')
        );
        
        self::$xml_categories = (array)$tmp[1];
        
        // Init user objects
        $this->refreshUser();
    }
    
    /**
     * Applies user principals to the cache of the model.
     * Public to the world for testing
     */
    public function refreshUser() {
        $this->agaviUser = $this->getContext()->getUser();
        if ($this->agaviUser->isAuthenticated() === true) {
            $this->user = $this->agaviUser->getNsmUser();
            $this->principals = $this->user->getPrincipalsArray();
        }
    }
    
    /**
     * Lazy method to avoid circular calls
     * @return Cronks_Provider_CronksDataModel
     */
    private function getCronkModel() {
        if ($this->cronks === null) {
            $this->cronks = $this->getContext()
                ->getModel('Provider.CronksData', 'Cronks');
        }
        
        return $this->cronks;
    }
    
    /**
     * Returns category structure from xml
     * @return array categories
     */
    private function getXmlCategories() {
        
        $isCategoryAdmin = $this->agaviUser
            ->hasCredential('icinga.cronk.category.admin');
        
        $check = array();
        
        /*
         * Checking permissions for system cronks
         */
        $syscats = AppKitDoctrineUtil::createQuery()
        ->select('cc.cc_uid')
        ->from('CronkCategory cc')
        ->andWhere('cc.cc_system=?', array(true))
        ->execute();
        
        foreach($syscats as $syscat) {
            // Default, no access
            $check[$syscat->cc_uid] = false;
            
            // Access if no principals defined for system
            // category record
            if ($syscat->principals->count() === 0) {
                $check[$syscat->cc_uid] = true;
            
            // Test if we have credentials
            } else {
                foreach ($syscat->principals as $principal) {
                    if (in_array($principal->principal_id, $this->principals)===true) {
                        $check[$syscat->cc_uid] = true;
                        break; // Ok, got it!
                    }
                }
            }
        }

        
        $out = array();
        foreach(self::$xml_categories as $cid=>$category) {
            
            if (!$isCategoryAdmin 
                    && array_key_exists($cid, $check) 
                    && $check[$cid] === false) {
                continue;
            }
            
            $out[$cid] = array(
                    'catid'          => $cid,
                    
                    'title'          => $category['title'],
                    
                    'visible'        => isset($category['visible']) ? 
                                            $category['visible'] : true,
                    
                    'active'         => isset($category['active']) ?
                                            $category['active'] : false,
                    
                    'position'       => isset($category['position']) ?
                                            $category['position'] : 0,
                    
                    'system'         => true,
                    
                    'permission_set' => (array_key_exists($cid, $check)) ?
                                            true : false
            );
        }
        return $out;
    }
    
    private function createCategoryStruct(CronkCategory $category) {
        return array(
            'catid'           => $category->cc_uid,
            'title'           => $category->cc_name,
            'visible'         => (bool)$category->cc_visible,
            'active'          => true,
            'position'        => (int)$category->cc_position,
            'system'          => false,
            'permission_set'  => ($category->principals->count()===0) ? 
                                    false : true
        );
    }
    
    /**
     * Return all categories from database
     * @param boolean $get_all
     * @return array categories from database
     */
    private function getDbCategories($get_all=false) {
        
        $isCategoryAdmin = $this->agaviUser
        ->hasCredential('icinga.cronk.category.admin');
        
        $base = AppKitDoctrineUtil::createQuery()
        ->select('cat.*')
        ->from('CronkCategory cat')
        ->andWhere('cat.cc_system=?', false);
    
        /**
         * Only category for cronks which belongs to you
         */
        if ($get_all === false
           && $this->agaviUser->hasCredential('icinga.cronk.category.admin')===false) {
            $base->innerJoin('cat.Cronk c')
            ->innerJoin('c.NsmPrincipal p')
            ->andWhereIn('p.principal_id', $this->principals);
        }
        
        
        $collection = clone $base;
        if (!$isCategoryAdmin) {
            $collection->innerJoin('cat.principals ccp')
            ->andWhereIn('ccp.principal_id', $this->principals);
        }
        
        $out = array();
        foreach($collection->execute() as $category) {
            $out[$category->cc_uid] = $this->createCategoryStruct($category);
        }
        
        /**
         * Need to add all custom cronks without principals
         * here
         * 
         * @todo Please refactor, this is not nice
         */
        if (!$isCategoryAdmin) {
            $collection = clone $base;
            foreach($collection->execute() as $category) {
                if ($category->principals->count() === 0) {
                    $out[$category->cc_uid] = $this->createCategoryStruct($category);
                }
            }
        }
        
        return $out;
    }
    
    /**
     * Get all categories from system
     * @param boolean $get_all
     * @param boolean $show_invisible
     * @return array
     */
    public function getCategories($get_all=false, $show_invisible=false) {
        
        static $cronks = null;
        
        $isCategoryAdmin = $this->agaviUser
            ->hasCredential('icinga.cronk.category.admin');
        
        if ($cronks === null) {
            $cronks = $this->getCronkModel()->getCronks($get_all);
        }
        
        if ($show_invisible == true && !$isCategoryAdmin) {
            $show_invisible = false;
        }
        
        $categories = $this->getXmlCategories();
        $categories = (array)$this->getDbCategories($get_all) + $categories;
        
        AppKitArrayUtil::subSort($categories, 'title');
        AppKitArrayUtil::subSort($categories, 'position');
        
        foreach($categories as $cid=>$category) {
            $count=0;
            
            /**
             * This implementation is cached and more fast than
             * using the CronksData model
             * @todo More fast, request needs 300-400ms, too slow!
             */
            foreach ($cronks as $cronk) {
                if (AppKitArrayUtil::matchAgainstStringList($cronk['categories'], $cid)) {
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
    
    /**
     * Test if a category exist
     * @param string $category_uid
     * @return boolean
     */
    public function hasCategory($category_uid) {
        $categories = $this->getCategories(true, true);
        return array_key_exists($category_uid, $categories);
    }
    
    /**
     * Return a category record
     * @param string $category_uid
     * @throws AppKitModelException
     * @return array
     */
    public function getCategory($category_uid) {
        $categories = $categories = $this->getCategories(true, true);
        if (array_key_exists($category_uid, $categories)) {
            return $categories[$category_uid];
        } else {
            throw new AppKitModelException('Category not found: '. $category_uid);
        }
    }
    
    /**
     * Remove a category and their principals from database
     * @param string $cc_uid
     * @return boolean success state
     */
    public function deleteCategoryRecord($cc_uid) {
        if ($this->agaviUser->hasCredential('icinga.cronk.category.admin') && isset($cc_uid)) {
            
            $category = Doctrine::getTable('CronkCategory')
            ->findOneBy('cc_uid', $cc_uid);

            if ($category) {
                AppKitDoctrineUtil::createQuery()
                ->delete('CronkPrincipalCategory')
                ->andWhere('category_id=?', array($category->cc_id))
                ->execute();
                
                $category->delete();
                
                return true;
            }
        }
    
        return false;
    }
    
    /**
     * Create of update a category
     * @param array $cat
     * @return Ambigous <NULL, CronkCategory>
     */
    public function createCategory(array $cat) {
        AppKitArrayUtil::swapKeys($cat, self::$cat_map, true);
    
        $category = null;
    
        if ($this->agaviUser->hasCredential('icinga.cronk.category.admin') && isset($cat['cc_uid'])) {
            $category = AppKitDoctrineUtil::createQuery()
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
}