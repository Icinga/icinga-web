<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
//
// Copyright (c) 2009-present Icinga Developer Team.
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
class NsmRole extends BaseNsmRole {
    private $principals_list = null;
    private $principals = null;
    private $children = null;
    private $context = null;
    private $storage = null;

    /**
    * Reduce database query overhead
    * @var array
    */
    private static $targetValuesCache = array();

    public function setUp() {

        parent::setUp();

        $this->hasMany('NsmUser', array('local'     => 'usro_role_id',
                                        'foreign'   => 'usro_user_id',
                                        'refClass'  => 'NsmUserRole'));

        $options = array(
                       'created' =>  array('name'   => 'role_created'),
                       'updated' =>  array('name'   => 'role_modified'),
                   );

        $this->actAs('Timestampable', $options);

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

    public function hasParent() {
        if ($this->get('role_parent')) {
            return true;
        }

        return false;
    }

    public function getParent() {
        if ($this->hasParent()) {
            return $this->parent;
        }

        return null;
    }

    public function getChildren() {
        if ($this->children === null) {
            $this->children = AppKitDoctrineUtil::createQuery()
                              ->select('r.*')
                              ->from("NsmRole r INDEXBY r.role_id")
                              ->where("r.role_parent = ?",$this->get("role_id"))
                              ->execute();
        }

        return $this->children;
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


    /**
     * Return all principals belonging to this
     * role
     * @return Doctrine_Collection
     */
    public function getPrincipals() {

        if ($this->principals === null) {

            $this->principals = AppKitDoctrineUtil::createQuery()
                                ->select('p.*')
                                ->from('NsmPrincipal p INDEXBY p.principal_id')
                                ->andWhere('p.principal_type = ? AND p.principal_role_id = ?',array('role',$this->get("role_id")))

                                ->execute();

        }

        return $this->principals;

    }

    /**
     * Returns a DQL providing the role targets
     * @param string $type
     * @return Doctrine_Query|null
     */
    protected function getTargetsQuery($type=null) {
        $principals = $this->getPrincipalsList();
        if (empty($principals)) {
            // We may have no principals yet if an external authentication method was used
            return null;
        }
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

    /*
     * Return all targets belonging to thsi user
     * @param string $type
     * @return Doctrine_Collection|null
     */
    public function getTargets($type=null) {
        $q = $this->getTargetsQuery($type);
        return $q === null ? null : $q->execute();
    }


    /**
     * Returns true if a target exists
     * @param string $name
     * @return boolean
     */
    public function hasTarget($name) {
        $q = $this->getTargetsQuery();
        if ($q === null) {
            return false;
        }
        $q->andWhere('t.target_name=?', array($name));

        if ($q->execute()->count() > 0) {
            return true;
        }

        return false;
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
    protected function getTargetValuesQuery($target_name) {
        $q = AppKitDoctrineUtil::createQuery()
             ->select('tv.*')
             ->from('NsmTargetValue tv')
             ->innerJoin('tv.NsmPrincipalTarget pt')
             ->innerJoin('pt.NsmTarget t with t.target_name=?', $target_name)
             ->andWhereIn('pt.pt_principal_id', $this->getPrincipalsList());
        return $q;
    }

    /**
     * Return all target values as Doctrine_Collection
     * @param string $target_name
     * @return Doctrine_Collection
     */
    public function getTargetValues($target_name) {
        return $this->getTargetValuesQuery($target_name)->execute();
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

        if (count(self::$targetValuesCache) == 0) {
            $tc = AppKitDoctrineUtil::createQuery()
                  ->select('t.target_name, t.target_id')
                  ->from('NsmTarget t')
                  ->innerJoin('t.NsmPrincipalTarget pt')
                  ->andWhereIn('pt.pt_principal_id', $this->getPrincipalsList())
                  ->execute();

            $out = array();

            foreach($tc as $t) {
                $out[ $t->target_name ] = array();

                $ptc = AppKitDoctrineUtil::createQuery()
                       ->from('NsmPrincipalTarget pt')
                       ->innerJoin('pt.NsmTargetValue tv')
                       ->andWhereIn('pt.pt_principal_id', $this->getPrincipalsList())
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

            self::$targetValuesCache =& $out;

        }


        return self::$targetValuesCache;
    }
}
