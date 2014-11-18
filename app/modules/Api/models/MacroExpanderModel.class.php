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

/**
 * Macro expander
 *
 * Expands a small list of macros and all custom vars
 * to strings
 */
class Api_MacroExpanderModel extends IcingaApiBaseModel {

    /**
     * Expands by object id
     *
     * @param   integer $oid
     * @param   string $string
     *
     * @return  string
     */
    public function expandByObjectId($oid, $string) {
        $object = Doctrine_Manager::getInstance()
            ->getConnection(IcingaDoctrineDatabase::CONNECTION_ICINGA)
            ->getTable('IcingaObjects')
            ->findOneBy('object_id', $oid);

        $data = $this->collectData($object);

        return $this->substituteData($string, $data);
    }

    /**
     * Substitute a tokenized string with array data
     * @param   string  $string
     * @param   array   $data
     *
     * @return  string
     */
    private function substituteData($string, array $data) {
        $m = array();
        while(preg_match('/\$([^\$]+)\$/', $string, $m)) {
            $name = $m[1];
            if (array_key_exists($name, $data)) {
                $replacement = $data[$name];
            } else {
                $replacement = '[NOT FOUND: '. $name. ']';
            }

            $string = str_replace('$'. $name. '$', $replacement, $string);
        }

        return $string;
    }

    /**
     * Collects all data from an IcingaObjects structure
     * @param IcingaObjects $o
     *
     * @return array
     */
    private function collectData(IcingaObjects $o) {
        $data = array();
        if ($o->objecttype_id == 1) {
            $this->appendHostData($o->host, $data);
            $data['HOSTNAME'] = $o->name1;
        } elseif ($o->objecttype_id == 2) {
            $this->appendServiceData($o->service, $data);
            $this->appendHostData($o->service->host, $data);
            $data['HOSTNAME'] = $o->name1;
            $data['SERVICEDESC'] = $o->name2;
        }

        return $data;
    }

    /**
     * Appends service data to data array
     *
     * @param IcingaServices $service
     * @param array          $data
     */
    private function appendServiceData(IcingaServices $service, array &$data) {
        $data['SERVICEDISPLAYNAME'] = $service->display_name;
        $data['SERVICESTATEID']     = $service->status->current_state;

        $this->appendCustomVariables($service->customvariables, '_SERVICE', $data);
    }

    /**
     * Appends host data to data array
     *
     * @param IcingaHosts $host
     * @param array       $data
     */
    private function appendHostData(IcingaHosts $host, array &$data) {
        $data['HOSTADDRESS']    = $host->address;
        $data['HOSTALIAS']      = $host->alias;
        $data['DISPLAYNAME']    = $host->display_name;
        $data['HOSTSTATEID']    = $host->status->current_state;

        $this->appendCustomVariables($host->customvariables, '_HOST', $data);
    }

    /**
     * Appends custom variables to data array
     *
     * @param Doctrine_Collection $variables
     * @param string              $prefix
     * @param array               $data
     */
    private function appendCustomVariables(Doctrine_Collection $variables, $prefix, array &$data) {
        foreach ($variables as $variable) {
            $data[$prefix. strtoupper($variable->varname)] = $variable->varvalue;
        }
    }
}