<?php

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
