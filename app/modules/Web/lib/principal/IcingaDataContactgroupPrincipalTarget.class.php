<?php

class IcingaDataContactgroupPrincipalTarget extends IcingaDataPrincipalTarget {

    public function __construct() {

        parent::__construct();

        $this->setType('IcingaDataTarget');

        $this->setDescription('Limit data access to hostgroups');

    }

    public function getCustomMap() {
        $user = AgaviContext::getInstance()->getUser()->getNsmUser();

        return sprintf('${CONTACT_NAME} = \'%s\'', $user->user_name);
    }

}