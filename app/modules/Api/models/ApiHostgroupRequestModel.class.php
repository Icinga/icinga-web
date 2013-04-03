<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2013 Icinga Developer Team.
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


class Api_ApiHostgroupRequestModel extends ApiDataRequestBaseModel {
    public function getHostgroups() {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from('IcingaHostgroups hg');

        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getHostgroupsByHostIds(array $hosts) {

        $desc = $this->createRequestDescriptor();
        $desc->select('*')->distinct()->from('IcingaHostgroups hg');
        $desc->innerJoin("hg.members h")->where("h.instance_id = hg.instance_id");
        $desc->andWhereIn("h.host_id",$hosts);
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getHostgroupsByHostNames(array $hosts) {

        $desc = $this->createRequestDescriptor();
        $desc->select('*')->distinct()->from('IcingaHostgroups hg');
        $desc->innerJoin("hg.members h")->where("h.instance_id = hg.instance_id");
        $desc->andWhereIn("h.display_name",$hosts);
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getHostgroupsByStates(array $states) {
        $desc = $this->createRequestDescriptor();
        $desc->select('hg.*')->distinct()->from('IcingaHostgroups hg');
        $desc->innerJoin("hg.members h")->where("h.instance_id = hg.instance_id");
        $desc->innerJoin("h.status s")->where("s.instance_id = h.instance_id AND s.current_state IN ('".join("','",$states)."')");
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getHostgroupsByServices(array $services) {
        $desc = $this->createRequestDescriptor();
        $desc->select('hg.*')->distinct()->from('IcingaHostgroups hg');
        $desc->innerJoin('hg.members h')->where("h.instance_id = hg.instance_id");
        $desc->innerJoin('h.services s')->where("s.instance_id = h.instance_id")->andWhere("s.display_name IN('".join("','",$services)."')");

        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getHostgroupsByNames(array $names, $ignoreWildCards = false) {
        $useLike = false;
        foreach($names as $name) {
            if (strpos($name,'%') !== false) {
                $useLike = true;
                break;
            }
        }
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from("IcingaHostgroups h");

        if (!$useLike || $ignoreWildCards) {
            $desc->whereIn("h.alias",$names);
        } else {
            $first = true;
            foreach($names as $name) {
                if ($first) {
                    $desc->addWhere("h.alias LIKE ?",array($name));
                } else {
                    $desc->orWhere("h.alias LIKE ?",array($name));
                }

                $first = false;
            }
        }

        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getHostgroupsByInstances(array $instance_ids) {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from("IcingaHostgroups h")->whereIn("h.instance_id",$instance_ids);

        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }
}
