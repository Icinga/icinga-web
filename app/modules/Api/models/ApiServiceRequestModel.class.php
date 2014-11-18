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


class Api_ApiServiceRequestModel extends ApiDataRequestBaseModel {

    public function limitToHosts(Doctrine_Query $q,array $hosts = array()) {
        if (empty($hosts)) {
            return $q;
        }

        $byName = array();
        $byId  = array();
        foreach($hosts as $host) {
            if (is_string($host)) {
                $byName[] = $host;
            } else if (is_int($host)) {
                $byId[] = $host;
            } else if ($host instanceof IcingaHosts) {
                $byId[] = $host->host_id;
            }
        }
        $q->innerJoin('s.host h')->whereIn("h.display_name",$byName)->orWhereIn("h.host_id",$byId);
    }
    public function getServices(array $hosts = array()) {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from('IcingaServices s');

        $this->limitToHosts($desc,$hosts);
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getServicesByName(array $names,array $hosts = array(),$ignoreWildCards = false) {
        $useLike = false;
        foreach($names as $name) {
            if (strpos($name,'%') !== false) {
                $useLike = true;
                break;
            }
        }
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from("IcingaServices s");

        if (!$useLike || $ignoreWildCards) {
            $desc->whereIn("s.display_name",$names);
        } else {
            $first = true;
            foreach($names as $name) {
                if ($first) {
                    $desc->addWhere("s.display_name LIKE ?",array($name));
                } else {
                    $desc->orWhere("s.display_name LIKE ?",array($name));
                }

                $first = false;
            }
        }

        $this->limitToHosts($desc,$hosts);
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getServicesById(array $ids,array $hosts = array()) {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from('IcingaServices s');
        $desc->whereIn("s.id",$ids);

        $this->limitToHosts($desc,$hosts);
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getServicesByObjectId(array $ids,array $hosts = array()) {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from('IcingaServices s');
        $desc->whereIn("s.service_object_id",$ids);

        $this->limitToHosts($desc,$hosts);
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getServicesByServicegroupNames(array $names,array $hosts = array()) {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from('IcingaServices s');
        $desc->innerJoin("s.servicegroups sg")->whereIn("sg.alias",$names);

        $this->limitToHosts($desc,$hosts);
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getServicesByServicegroupIds(array $ids,array $hosts = array()) {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from('IcingaServices s');
        $desc->innerJoin("s.servicegroups sg")->whereIn("sg.servicegroup_id",$ids);

        $this->limitToHosts($desc,$hosts);
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getServicesByInstanceIds(array $ids,array $hosts = array()) {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from('IcingaServices s')->whereIn("s.instance_id",$ids);

        $this->limitToHosts($desc,$hosts);
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getServicesByState(array $statesToShow,array $hosts = array()) {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from('IcingaServices s');
        $desc->innerJoin("s.status stat")->whereIn("stat.current_state",$statesToShow);

        $this->limitToHosts($desc,$hosts);
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getServicesByCustomVars(array $keyVals,array $hosts = array()) {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from("IcingaServices h")->innerJoin("h.customvariables cv");
        $first =true;
        foreach($keyVals as $key=>$val) {
            if ($first) {
                $desc->addWhere("cv.varname LIKE ? AND cv.varvalue LIKE ?",array($key,$val));
            } else {
                $desc->orWhere("cv.varname LIKE ? AND cv.varvalue LIKE ?",array($key,$val));
            }

            $first = false;
        }
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);

    }
}
