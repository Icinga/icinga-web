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


class IcingaDataHostCustomVariablePrincipalTarget extends IcingaDataPrincipalTarget {

    public function __construct() {

        parent::__construct();

        $this->setDefaultTarget('host');

        $this->setFields(array(
                             'cv_name'  => 'Name of the custom variable',
                             'cv_value' => 'Value contained ba the variable'
                         ));

        $this->setApiMappingFields(array(
                                       'cv_name'    => 'HOST_CUSTOMVARIABLE_NAME',
                                       'cv_value'   => 'HOST_CUSTOMVARIABLE_VALUE'
                                   ));

        $this->setType('IcingaDataTarget');

        $this->setDescription('Limit data access to customvariables');
    }

    public function getMapArray(array $arr) {
        $p = array();
        foreach($arr as $set) {
            if(isset($set["cv_name"]) and isset($set["cv_value"])) {
                $p[] = "(".
                    sprintf('${%s} LIKE \'%s\'', $this->getApiMappingField("cv_name"), $set["cv_name"]).
                    " AND ".
                    sprintf('${%s} LIKE \'%s\'', $this->getApiMappingField("cv_value"), $set["cv_value"]).
                ")";
            }
        }
        $p[] = '${HOST_OBJECT_ID} IS NULL';

        return '('. join(' OR ', $p). ')';
    }


}

?>
