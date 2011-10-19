<?php

class IcingaDataCommandRestrictionPrincipalTarget extends IcingaDataPrincipalTarget {

    public function __construct() {

        parent::__construct();

        $this->setType('IcingaDataTarget');

        $this->setDescription('Limit data access to commands');

    }
}