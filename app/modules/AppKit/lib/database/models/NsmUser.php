<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
//
// Copyright (c) 2009-2014 Icinga Developer Team.
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
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class NsmUser extends BaseNsmUser {

    const HASH_ALGO = 'sha256';

    private static $prefCache = array();

    /**
     * Reduce database query overhead
     * @var array
     */
    private static $targetValuesCache = array();
    private static $cachedPreferences = array();
    /**
     * @var Doctrine_Collection
     */
    private $principals         = null;

    /**
     * @var array
     */
    private $principals_list    = null;

    private $target_list        = null;

    private $context = null;

    private $storage = null;

    /**
     * (non-PHPdoc)
     * @see lib/appkit/database/models/generated/BaseNsmUser#setTableDefinition()
     */
    public function setTableDefinition() {

        parent::setTableDefinition();

        $this->index('user_name_unique', array(
                         'fields' => array(
                             'user_name'
                         ),
                         'type' => 'unique'
        ));

           // Removed by #2228
//         $this->index('user_email_unique', array(
//                          'fields' => array(
//                              'user_email'
//                           ),
//                           'type' => 'unique'
//         ));

        $this->createUserSearchIndex();
    }

    /**
     * Decision maker. How the index should be created
     */
    private function createUserSearchIndex() {
        $conn = Doctrine_Manager::getInstance()->getConnection('icinga_web');

        $user_search_index = array('fields' => array(
            'user_name',
            'user_authsrc',
            'user_authid',
            'user_disabled'
        ));

        if (strtolower($conn->getDriverName()) === 'mysql') {
            $user_search_index = array('fields' => array(
                'user_name',
                'user_authsrc',
                'user_authid' => array('length' => 127),
                'user_disabled'
            ));
        }

        $this->index('user_search', $user_search_index);
    }

    public function getContext() {
        if(!$this->context)
            $this->context = AgaviContext::getInstance();
        return $this->context;
    }

    public function getStorage() {
        if(!$this->storage)
            $this->storage = $this->getContext()->getStorage();
        return $this->storage;
    }

    public function setUp() {

        parent::setUp();

        $this->hasMany('NsmRole', array('local'     => 'usro_user_id',
                                        'foreign'   => 'usro_role_id',
                                        'refClass'  => 'NsmUserRole'));

        $options = array(
                       'created' =>  array('name'   => 'user_created'),
                       'updated' =>  array('name'   => 'user_modified'),
                   );

        $this->actAs('Timestampable', $options);

    }

    public function givenName() {
        if ($this->user_lastname && $this->user_firstname) {
            return sprintf('%s, %s', $this->user_lastname, $this->user_firstname);
        } else {
            return $this->user_name;
        }
    }

    public function hasRoleAssigned($role_id) {
        foreach($this->NsmRole as $role) {
            if ($role_id == $role->role_id) {
                return true;
            }
        }

        return false;
    }

    private function __updatePassword($password) {
        $this->user_salt = $this->__createSalt($this->user_name);
        hash_hmac(self::HASH_ALGO, $password, $this->user_salt);
        $this->user_password = hash_hmac(self::HASH_ALGO, $password, $this->user_salt);
    }

    private function __createSalt($entropy) {
        return hash(self::HASH_ALGO, uniqid($entropy. '_', mt_rand()));
    }

    public function updatePassword($password) {
        if ($this->state() !== self::STATE_TDIRTY) {
            $this->__updatePassword($password);
        } else {
            throw new AppKitDoctrineException('Could not change a password on a not existing record!');
        }

        return false;
    }

    /**
     * Sets a random password for the user
     * @return null really nothing
     * @param integer $length
     */
    public function generateRandomPassword($length=10) {
        $password = AppKitRandomUtil::genSimpleId($length, microtime(true));
        $this->__updatePassword($password);
    }

    /**
     * Sets a pref value
     * @param string $key
     * @param mixed $val
     * @param boolean $overwrite
     * @param boolean $blob
     * @return unknown_type
     * @throws AppKitException
     * @author Marius Hein
     */
    public function setPref($key, $val, $overwrite = true, $blob = false) {
        $field = "upref_val";
        if ($blob == true) {
            $field = "upref_longval";
        }
        try {
            $pref = $this->getPrefObject($key, false, true);

            // DO NOT OVERWRITE
            if ($overwrite === false) {
                return false;
            }

            Doctrine_Query::create($this->getContext()->getDatabaseConnection("icinga_web"))
                ->update("NsmUserPreference p")->set($field,"?",$val)
                ->where("p.upref_user_id=? and p.upref_key=?",array($this->user_id,$key))
                ->execute();
            if(is_array($pref))
                $pref[$field] = $val;

        } catch (AppKitDoctrineException $e) {
            $pref = new NsmUserPreference();

            $pref->upref_key = $key;
            $pref->$field = $val;
            $pref->NsmUser = $this;
            $pref->save();

            AppKitLogger::warn("New: Setting %s => %s", $key,$pref->toArray(false) );
        }
        NsmUser::$cachedPreferences = array();
        return true;
    }

    /**
     * Returns a preferenceobject from a user
     * @param string $key
     * @return NsmUserPreference
     * @throws AppKitDoctrineException
     * @author Marius Hein
     */
    public function getPrefObject($key,$graceful = true, $ignoreDefaults = false) {
        $res = $this->getPreferences(false, $ignoreDefaults);
        if(isset($res[$key]))
            return $res[$key];
        else if($graceful) {
            self::$cachedPreferences = array();
            return $this->getPrefObject($key,false);
        }
        throw new AppKitDoctrineException('Preference record not found!');
    }

    /**
     * Returns the real value of a preference
     * @param string $key
     * @param mixed $default
     * @return mixed
     * @author Marius Hein
     */
    public function getPrefVal($key, $default=null, $blob = false) {

        try {
            $val = $this->getPrefObject($key,$noThrow = false);
            return $val;
        } catch (AppKitDoctrineException $e) {
            return $default;
        }
    }

    /**
     * Returns the authid to identify the user against the provider
     * @return string
     */
    public function getAuthId() {
        if ($this->user_authid) {
            return $this->user_authid;
        }

        return $this->user_name;
    }

    public function getPrefComponent($key, $component_name) {
        $val = $this->getPrefVal($key);

        if ($val) {
            return Doctrine::getTable($component_name)->find($val);
        }

        return null;
    }

    /**
     * Deletes a pref value from the user
     * @param string $key
     * @return boolean if something was deleted
     * @author Marius Hein
     */
    public function delPref($key) {
        /*
         * WORKAROUND:
         * Postgresql doesn't support limit, so we must first select a row, then delete it
         */
        $idToDelete = AppKitDoctrineUtil::createQuery()
                      ->select("upref_id")
                      ->from("NsmUserPreference p")
                      ->where('p.upref_user_id=? and p.upref_key=?', array($this->user_id, $key))
                      ->execute()->getFirst();

        if (!$idToDelete) {
            return false;
        }

        $upref_id = $idToDelete->get('upref_id');
        $test = AppKitDoctrineUtil::createQuery()
                ->delete('NsmUserPreference p')
                ->where('p.upref_id=? and p.upref_user_id=? and p.upref_key=?', array($upref_id,$this->user_id, $key))
                //->limit(1)  -> not supported by postgresql
                ->execute();
        self::$cachedPreferences = array();
        $this->getStorage()->remove("appkit.nsm_user.preferences");

        if ($test) {
            return true;
        } else {
            return false;
        }
    }

    public function getPreferences($shortenBlob = false, $ignoreDefaults = false) {
        if(!empty(self::$cachedPreferences)) {
            $res = self::$cachedPreferences;
        }
        else {
            $res = AppKitDoctrineUtil::createQuery()
                   ->select('p.upref_val, p.upref_key, p.upref_longval')
                   ->from('NsmUserPreference p INDEXBY p.upref_key')
                   ->where('p.upref_user_id=?', array($this->user_id))
                   ->execute(array(), Doctrine::HYDRATE_ARRAY);
            self::$cachedPreferences = $res;
        }


        $out = array();
        foreach($res as $key => $d) {
            $out[$key] = $d['upref_longval'] ? ($shortenBlob ? 'BLOB' : $d['upref_longval']) : $d['upref_val'];
        }
        // Adding defaults
        if(!$ignoreDefaults) {
            foreach(AgaviConfig::get('modules.appkit.user_preferences_default', array()) as $k=>$v) {
                if (!array_key_exists($k, $out)) {
                    $out[$k] = $v;
                }
            }
        }
        return $out;
    }


    /**
     * Returns the status of the corresponding principal
     * @return boolean
     */
    public function principalIsValid() {
        return ($this->principal->principal_id > 0 && $this->principal->principal_type == 'user') ? true : false;
    }

    /**
     * Returns a list of all belonging principals
     * @return array
     */
    public function getPrincipalsList() {

        if ($this->principals_list === null) {

            $this->principals_list = array_keys($this->getPrincipals()->toArray());

        }

        return $this->principals_list;
    }

    public function getUserPrincipalsList($withRoles = false) {
        $list = AppKitDoctrineUtil::createQuery()
            ->select('p.*')
            ->from('NsmPrincipal p INDEXBY p.principal_id')
            ->orWhere('p.principal_user_id = ?',$this->user_id);
        if($withRoles)
            $list->orWhereIn('p.principal_role_id',$this->getRoleIds());
        $list = $list->execute();
        $ids = array();
        foreach($list as $entry) {
            $ids[] = $entry->principal_id;
        }
        return $ids;
    }

    private function collectChildRoleIdentifier(NsmRole $role, array &$store = array ()) {
            foreach ($role->getChildren() as $child) {
                $this->collectChildRoleIdentifier($child, $store);
                $store[] = $child->role_id;
            }
    }

    private function getRoleIds() {

        $use_topdown = AgaviConfig::get('modules.appkit.auth.behaviour.group_topdown');

        $ids = array();
        foreach($this->NsmRole as $role) {
            if($role->role_disabled)
                continue;
            $ids[] = $role->role_id;

            /*
             * This is devel classic behaviour. Inheritance
             * of roles goes top-down. This means the role with all
             * credentials is the deepest.
             */
            if ($use_topdown === true) {
                while ($role->hasParent()){
                    $role = $role->parent;
                    if($role->role_disabled)
                        continue;
                    $ids[] = $role->role_id;
                }

            /*
             * This is more group managing like. The group on top
             * collects all credentials from underlaying groups
             */
            } else {
                $this->collectChildRoleIdentifier($role, $ids);
            }
        }

        $ids = array_unique($ids);

        return $ids;
    }

    /**
     * Return all principals belonging to this
     * user
     * @return Doctrine_Collection
     */
    public function getPrincipals($userOnly= false) {
        /* removed caching for principals due to problems on deletion -mfrosch
        if ($this->principals === null)
            $this->principals =  $this->getStorage()->read("appkit.nsm_user.principals");
        */

        if ($this->principals === null) {
            $roles = $this->getRoleIds();
            $this->principals = AppKitDoctrineUtil::createQuery()
                                ->select('p.*')
                                ->from('NsmPrincipal p INDEXBY p.principal_id')
                                ->andWhereIn('p.principal_role_id',$roles)

                                ->orWhere('p.principal_user_id = ?',$this->user_id)
                                ->execute();
            /* removed caching for principals due to problems on deletion -mfrosch
            $this->getStorage()->write("appkit.nsm_user.principals",$this->principals);
            */
        }

        return $this->principals;
    }

    public function getPrincipalsArray() {
        static $out = array();

        if (empty($out)) {
            $principals = $this->getPrincipals();
            foreach($principals as $p) {
                $out[] = $p->principal_id;
            }
        }

        return $out;
    }

    /**
     * Return all targets belonging to thsi user
     * @param string $type
     * @return Doctrine_Collection
     */
    public function getTargets($type=null,$userOnly = false,$withRoles = false) {
        $principals = $userOnly ? $this->getUserPrincipalsList($withRoles) : $this->getPrincipalsList();
        if(empty($principals))
            return array();
        return $this->getTargetsQuery($type,$userOnly,$principals)->execute();
    }

    /**
     *
     * Returns a DQL providing the user targets
     * @param string $type
     * @return Doctrine_Query
     */
    protected function getTargetsQuery($type=null,$userOnly = false,$principals = null, $checkRoles = false) {
        if($principals == null)
            $principals = $userOnly ? $this->getUserPrincipalsList($checkRoles) : $this->getPrincipalsList();

        $q = AppKitDoctrineUtil::createQuery()
             ->select('t.*')
             ->distinct(true)
             ->from('NsmTarget t INDEXBY t.target_id')
             ->innerJoin('t.NsmPrincipalTarget pt')
             ->andWhereIn('pt.pt_principal_id', $principals);

        if ($type !== null) {
            $q->andWhere('t.target_type=?', array($type));
        }

        return $q;
    }

    /**
     * Returns true if a target exists
     * @param string $name
     * @return boolean
     */
    public function hasTarget($name,$inheritRoleTargets = false) {

        if ($this->target_list === null) {
            $res = $this->getTargetsQuery(null,false,null,$inheritRoleTargets)->execute();
            $this->target_list = array();
            foreach ($res as $target) {
                $this->target_list[$target->target_name] = true;
            }
        }
        return isset($this->target_list[$name]);
    }

    /**
     *
     * @param string $name
     * @return NsmTarget
     */
    public function getTarget($name) {
        $col = Doctrine::getTable('NsmTarget')->findByDql('target_name=?', array($name));

        if ($col->count() == 1 && $this->hasTarget($name)) {
            return $col->getFirst();
        } else {
            return null;
        }
    }

    /**
     * Returns a query with all values to a target
     * @param string $name
     * @return Doctrine_Query
     */
    protected function getTargetValuesQuery($target_name,$withRoles = false) {
        $q = AppKitDoctrineUtil::createQuery()
             ->select('tv.*')
             ->from('NsmTargetValue tv')
             ->innerJoin('tv.NsmPrincipalTarget pt')
             ->innerJoin('pt.NsmTarget t with t.target_name=?', $target_name)
             ->andWhereIn('pt.pt_principal_id', $this->getUserPrincipalsList($withRoles));
        return $q;
    }

    /**
     * Return all target values as Doctrine_Collection
     * @param string $target_name
     * @return Doctrine_Collection
     */
    public function getTargetValues($target_name,$withRoles = false) {
        $result =  $this->getTargetValuesQuery($target_name,$withRoles)->execute();

        return $result;
    }

    public function getTargetValue($target_name, $value_name) {
        $q = $this->getTargetValuesQuery($target_name);
        $q->select('tv.tv_val');
        $q->andWhere('tv.tv_key=?', array($value_name));
        $res = $q->execute();

        $out = array();
        foreach($res as $r) {
            $out[] = $r->tv_val;
        }

        return $out;
    }

    public function getTargetValuesArray() {
        /* removed caching for target values due to problems on deletion -mfrosch
        if (empty(self::$targetValuesCache)) {
            self::$targetValuesCache = $this->getStorage()->read("appkit.nsm_user.targetvalues");
        }
        */
        $userPrincipals =  $this->getUserPrincipalsList(true);
        /*
        if (empty(self::$targetValuesCache)) {
        */
            $tc = AppKitDoctrineUtil::createQuery()
                  ->select('t.target_name, t.target_id')
                  ->from('NsmTarget t')
                  ->innerJoin('t.NsmPrincipalTarget pt')
                  ->andWhereIn('pt.pt_principal_id',$userPrincipals)
                  ->execute();

            $out = array();

            foreach($tc as $t) {
                $out[ $t->target_name ] = array();

                $ptc = AppKitDoctrineUtil::createQuery()
                       ->from('NsmPrincipalTarget pt')
                       ->innerJoin('pt.NsmTargetValue tv')
                       ->andWhereIn('pt.pt_principal_id', $userPrincipals)
                       ->andWhere('pt.pt_target_id=?', array($t->target_id))
                       ->execute();

                foreach($ptc as $pt) {
                    $tmp = array();
                    foreach($pt->NsmTargetValue as $tv) {
                        $tmp[ $tv->tv_key ] = $tv->tv_val;
                    }

                    $out[ $t->target_name ][] = $tmp;
                }
            }

        /* removed caching for target values due to problems on deletion -mfrosch
            self::$targetValuesCache =& $out;
            $this->getStorage()->write("appkit.nsm_user.targetvalues",self::$targetValuesCache);
        }
        return self::$targetValuesCache;
        */
        return $out;
    }
}
