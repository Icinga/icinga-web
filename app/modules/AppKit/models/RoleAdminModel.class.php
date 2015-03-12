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
 * Working with roles
 * @author mhein
 *
 */
class AppKit_RoleAdminModel extends AppKitBaseModel {

    private static $editableAttributes = array(
        'role_name', 'role_description', 'role_disabled', 'role_parent'
    );

    /**
     * Human query to restrict groups
     * @var string
     */
    private $query;

    private function applyQueryToDoctrineQuery(Doctrine_Query $query) {
        if ($this->getQuery()) {
            $searchQuery = '%'. $this->getQuery(). '%';
            $searchParams = array($searchQuery, $searchQuery);
            $query->andWhere('(role_name LIKE ? OR role_description LIKE ?)', $searchParams);
        }
    }

    /**
     * Returns query for roles
     * @param bool|int $disabled
     * @return Doctrine_Query
     * @author Marius Hein
     */
    public function getRoleQuery($disabled = 0) {
        $roles = AppKitDoctrineUtil::createQuery()
                 ->from('NsmRole')
                 ->orderBy('role_name ASC');

        if ($disabled == 0) {
            $roles->andWhere('role_disabled = 0');
        }

        $this->applyQueryToDoctrineQuery($roles);

        return $roles;
    }

    /**
     *
     * @param boolean $disabled
     * @return Doctrine_Collection
     * @author Marius Hein
     */
    public function getRoleCollection($disabled = false) {
        return $this->getRoleQuery($disabled)->execute();
    }


    /**
     * Creates a collection of NsmRole objects within the range $start and $limit and optionally
     * sorts it by param $sort
     * @param boolean $disabled
     * @param integer $start
     * @param integer $limit
     * @param string $sort
     * @param boolean $asc
     * @param boolean $own
     *
     * @return Doctrine_Collection
     * @author Jannis Mosshammer
     */
    public function getRoleCollectionInRange($disabled=false,$start = 0,$limit=25,$sort= null,$asc = true,$own=false) {
        $query = AppKitDoctrineUtil::createQuery()
                 ->select('r.*')
                 ->from("NsmRole r")
                 ->limit($limit)
                 ->offset($start);

        if ($sort) {
            $query->orderBy('r.' . $sort." ".($asc ? 'ASC' : 'DESC'));
        }

        if ($disabled === false) {
            $query->andWhere('r.role_disabled=?', array(0));
        }

        if ($own == true) {
            $query->innerJoin('r.NsmUser u WITH user_id=?', $this->getContext()->getUser()->getNsmUser()->user_id);
        }

        $this->applyQueryToDoctrineQuery($query);

        return $query->execute();
    }

    public function getRoleCount($disabled=false) {
        $query = AppKitDoctrineUtil::createQuery()
                 ->select("COUNT(u.role_id) count")
                 ->from("NsmRole u");

        if ($disabled === false) {
            $query->andWhere('role_disabled=?', array(0));
        }

        return $query->execute()->getFirst()->get("count");

    }
    /**
     * Load a role record by id
     * @param integer $role_id
     * @return NsmRole
     * @throws AppKitDoctrineException
     * @author Marius Hein
     */
    public function getRoleById($role_id) {
        $role = Doctrine::getTable('NsmRole')->find($role_id);

        if (!$role instanceof NsmRole) {
            throw new AppKitDoctrineException('Role not found with this id');
        }

        return $role;
    }

    /**
     * Updates the simple role data
     * @param NsmRole $role
     * @param AgaviRequestDataHolder $rd
     * @return boolean
     * @author Marius Hein
     */
    public function updateRoleData(NsmRole &$role, AgaviRequestDataHolder &$rd) {
        AppKitDoctrineUtil::updateRecordsetFromArray($role, $rd->getParameters(), self::$editableAttributes);

        // Checking the principal
        if (!$role->NsmPrincipal->principal_id) {
            $role->NsmPrincipal->principal_type = NsmPrincipal::TYPE_ROLE;
        }

        if($role->role_id === null) {
            // insert a new role
            $role->save();
        }
        else { // update role
            $parts = array();
            $params = array();
            foreach($role as $property=>$value) {
                if($property == "role_id" || !in_array($property,self::$editableAttributes))
                    continue;

                if($value === null)
                    $parts[] = "$property = NULL";
                else {
                    $parts[] = "$property = ? ";
                    $params[] = $value;
                }
            }
            $params[] = $role->role_id;
            $dql = "UPDATE NsmRole SET ".implode(",",$parts)." WHERE role_id = ?";
            AppKitLogger::warn("Test: %s - %s", $dql, var_export($params, true));
            $query = new Doctrine_Query();
            $query->setConnection(AppKitDoctrineUtil::getConnection());
            $query->parseDqlQuery($dql);
            $query->execute($params);
        }
        return true;
    }

    /**
     * Toggles the role status
     * @param NsmRole $role
     * @return boolean
     * @throws AppKitException
     * @author Marius Hein
     */
    public function toggleActivity(NsmRole &$role) {
        AppKitDoctrineUtil::toggleRecordValue($role);
        $role->save();
        return true;
    }

    /**
     * @todo: this is not really nice
     *
     * @param NsmRole $role
     */
    public function removeRole(NsmRole &$role) {

        $targets = $role->getTargets();
        if ($targets === null) {
            return;
        }
        foreach($targets as $target) {
            $vals = $role->getTargetValues($target->get("target_name"));
            foreach($vals as $value) {

                $value->delete();
            }
        }

        $principals = $role->NsmPrincipal;

        if (!$principals instanceof NsmPrincipal) {
            foreach($principals as $pr) {
                if ($pr->NsmPrincipalTarget) {
                    foreach($pr->NsmPrincipalTarget as $pr_t) {
                        $pr_t->delete();
                    }
                }

                $pr->delete();
            }
        } else {
            if ($principals->NsmPrincipalTarget) {
                foreach($principals->NsmPrincipalTarget as $pr_t) {
                    $pr_t->delete();
                }
            }

            $principals->delete();
        }

        $this->rechainChildren($role);
        $role->delete();
    }

    public function rechainChildren(NsmRole &$role) {
        $parent = $role->hasParent() ? $role->getParent() : null;
        $children = $role->getChildren();
        foreach($children as $child) {
            $child->set("role_parent",$parent);
            $child->save();
        }
    }

    /**
     * Setter for a group query
     * @param string $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * Getter for group query
     * @return string
     */
    public function getQuery()
    {
        if (strlen($this->query) >= 3) {
            return $this->query;
        }

        return null;
    }
}
