<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
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


class IcingaDataPrincipalTarget extends AppKitPrincipalTarget {
    protected $defaultTarget = '';
    protected $api_mapping_fields = array();

    public function getApiMappingFields() {
        return $this->api_mapping_fields;
    }

    public function setDefaultTarget($target) {
        $this->defaultTarget = $target;
    }

    public function getDefaultTarget() {
        return $this->defaultTarget;
    }

    protected function setApiMappingFields(array $a) {
        $this->api_mapping_fields = $a;
    }

    public function getApiMappingField($field) {
        if (array_key_exists($field, $this->api_mapping_fields)) {
            return $this->api_mapping_fields[$field];
        }

        return null;
    }

    public function getMapArray(array $arr) {
        $p = array();
        foreach($arr as $k=>$v) {
            $p[] = sprintf('${%s} LIKE \'%s\'', $this->getApiMappingField($k), $v);
        }

        return '('. join(' AND ', $p). ')';
    }

    public function getCustomMap() {
        return false;
    }

}

?>
