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
 * Working with principals and user credentials
 * bound to users or groups
 * @author mhein
 *
 */
class AppKit_PrincipalAdminModel extends AppKitBaseModel {

    public function __construct() {

    }

    public function getTargetArray() {

        $out = array();

        foreach(Doctrine::getTable('NsmTarget')->findAll() as $r) {

            $out[$r->target_name] = array(
                                        'id'            => $r->target_id,
                                        'name'          => $r->target_name,
                                        'description'   => $r->target_description,
                                        'type'          => $r->target_type,
                                        'fields'        => array()
                                    );

            if ($r->target_type == 'icinga') {
                foreach($r->getTargetObject()->getFields() as $fname=>$fdesc) {
                    $out[$r->target_name]['fields'][$fname] =
                        array("description"=> $fdesc,
                              "field"=>$r->getTargetObject()->getApiMappingField($fname),
                              "target" => $r->getTargetObject()->getDefaultTarget()
                             );

                }

            }

        }

        return $out;
    }

    public function getSelectedValues($principal_id) {
        $r = AppKitDoctrineUtil::createQuery()
             ->select('pt.pt_principal_id, tv.*, t.*')
             ->from('NsmPrincipalTarget pt')
             ->leftJoin('pt.NsmTargetValue tv')
             ->innerJoin('pt.NsmTarget t')
             ->andWhere('pt.pt_principal_id=?', array($principal_id))
             ->execute();

        $out = array();

        foreach($r as $pt) {
            $out[$pt->NsmTarget->target_name][$pt->pt_id] = array();

            $v = array();
            foreach($pt->NsmTargetValue as $tv) {
                $v[$tv->tv_key] = $tv->tv_val;
            }
            $out[$pt->NsmTarget->target_name][$pt->pt_id] = $v;
        }

        return $out;
    }

    public function updatePrincipalValueData(NsmPrincipal &$p, array $pt, array $pv) {

        // First delete all entries we create new ones
        /*
         * @todo This is not really pretty
         */
        $this->deleteAllPrincipalTargetEntries($p);

        foreach($pt as $id=>$principalToSet) {
            if (isset($principalToSet['set'])) {
                foreach($principalToSet['set'] as $aid=>$pt_set) {
                    if ($pt_set == 1) {

                        $target = Doctrine::getTable('NsmTarget')->findOneBy("target_name",$principalToSet['name']);
                        $target_id = $target->target_id;
                        $principal_target = new NsmPrincipalTarget();
                        $principal_target->NsmPrincipal = $p;
                        $principal_target->NsmTarget = $target;
                        if (isset($pv[$id])) {
                            foreach($pv[$id] as $pv_key => $pv_data) {
                                $pv_val = null;

                                if (isset($pv_data[$aid])) {
                                    $pv_val = $pv_data[$aid];
                                }

                                $target_value = new NsmTargetValue();
                                $target_value->tv_key = $pv_key;
                                $target_value->tv_val = $pv_val;

                                $principal_target->NsmTargetValue[] = $target_value;

                            }
                        }

                        $principal_target->save();
                    }
                }
            }
        }
    }

    private function deleteAllPrincipalTargetEntries(NsmPrincipal &$p) {

        AppKitDoctrineUtil::getConnection()->beginTransaction();

        foreach($p->NsmPrincipalTarget as $pt) {
            $pt->NsmTargetValue->delete();
            $pt->delete();
        }

        AppKitDoctrineUtil::getConnection()->commit();

        return true;
    }

}
