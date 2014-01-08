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


class Api_ApiHostRequestModel extends ApiDataRequestBaseModel {


    public function getHosts() {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from("IcingaHosts h");
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getHostsByName(array $names, $ignoreWildCards = false) {
        $useLike = false;
        foreach($names as $name) {
            if (strpos($name,'%') !== false) {
                $useLike = true;
                break;
            }
        }

        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from("IcingaHosts h");

        if (!$useLike || $ignoreWildCards) {
            $desc->whereIn("h.display_name",$names);
        } else {
            $first = true;
            foreach($names as $name) {
                if ($first) {
                    $desc->addWhere("h.display_name LIKE ?",array($name));
                } else {
                    $desc->orWhere("h.display_name LIKE ?",array($name));
                }

                $first = false;
            }
        }

        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getHostsById(array $ids) {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from("IcingaHosts h")->whereIn("h.host_id",$ids);
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getHostsByObjectId(array $ids) {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from("IcingaHosts h")->whereIn("h.host_object_id",$ids);
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getHostsByHostgroupNames(array $hostgroups) {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from("IcingaHosts h")->innerJoin("h.hostgroups hg")->whereIn("hg.alias",$hostgroups);
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getHostsByHostgroupIds(array $hostgroups) {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from("IcingaHosts h")->innerJoin("h.hostgroups hg")->whereIn("hg.hostgroup_id",$hostgroups);
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getHostsByInstanceIds(array $instanceIds) {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from("IcingaHosts h")->whereIn("h.instance_id",$instanceIds);
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getHostsByInstances(array $instance) {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from("IcingaHosts h")->innerJoin("h.instance i")->whereIn("i.instance_name",$instance);

        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getHostsByState(array $statesToShow) {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from("IcingaHosts h")->innerJoin("h.status s")->whereIn("s.current_state",$statesToShow);
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getHostsByAddress(array $addresses) {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from("IcingaHosts h")->whereIn("h.addresses",$addresses);
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getHostsByCustomVars(array $keyVals) {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from("IcingaHosts h")->innerJoin("h.customvariables cv");
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
