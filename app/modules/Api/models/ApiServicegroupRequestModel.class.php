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


class Api_ApiServicegroupRequestModel extends ApiDataRequestBaseModel {
    public function getServicegroups() {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from('IcingaServicegroups hg');

        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getServicegroupsByServiceIds(array $services)  {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->distinct()->from('IcingaServicegroups sg');
        $desc->innerJoin("sg.members s")->where("s.instance_id = sg.instance_id");
        $desc->andWhereIn("s.service_id",$services);
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getServicegroupsByServiceNames(array $services)  {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->distinct()->from('IcingaServicegroups sg');
        $desc->innerJoin("sg.members s")->where("s.instance_id = sg.instance_id");
        $desc->andWhereIn("s.display_name",$services);
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getServicegroupsByStates(array $states) {
        $desc = $this->createRequestDescriptor();
        $desc->select('sg.*')->distinct()->from('IcingaServicegroups sg');
        $desc->innerJoin("sg.members s")->where("s.instance_id = sg.instance_id");
        $desc->innerJoin("s.status st")->where("st.instance_id = s.instance_id AND st.current_state IN ('".join("','",$states)."')");
        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }


    public function getServicegroupsByNames(array $names, $ignoreWildCards = false) {
        $useLike = false;
        foreach($names as $name) {
            if (strpos($name,'%') !== false) {
                $useLike = true;
                break;
            }
        }
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from("IcingaServicegroups s");

        if (!$useLike || $ignoreWildCards) {
            $desc->whereIn("s.alias",$names);
        } else {
            $first = true;
            foreach($names as $name) {
                if ($first) {
                    $desc->addWhere("s.alias LIKE ?",array($name));
                } else {
                    $desc->orWhere("s.alias LIKE ?",array($name));
                }

                $first = false;
            }
        }

        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

    public function getServicegroupsByInstances(array $instance_ids) {
        $desc = $this->createRequestDescriptor();
        $desc->select('*')->from("IcingaServicegroups s")->whereIn("s.instance_id",$instance_ids);

        return $desc->execute(NULL,Doctrine_Core::HYDRATE_RECORD);
    }

}
