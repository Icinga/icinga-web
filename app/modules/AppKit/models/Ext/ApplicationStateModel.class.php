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


/**
 * Interface to read and write application states
 * @author mhein
 *
 */
class AppKit_Ext_ApplicationStateModel extends AppKitBaseModel implements AgaviISingletonModel {

    const PREFNS = 'org.icinga.ext.appstate';

    public function stateAvailable() {
        if ($this->getContext()->getUser()->isAuthenticated()) {
            return true;
        }

        return false;
    }

    public function readState() {
        $data = null;

        if ($this->stateAvailable()) {
            $data = $this->getContext()->getUser()->getPrefVal(self::PREFNS, null, true);
        }

        return $data;
    }

    public function writeState($data) {
        $merge = array();

        if ($this->stateAvailable()) {
            $existing = json_decode($this->readState());

            if (is_array($existing)) foreach($existing as $v) {
                $merge[$v->name] = $v->value;
            }

            foreach(json_decode($data) as $v) {
                $merge[$v->name] = $v->value;
            }
            $data = array();
            foreach($merge as $k => $v) {
                $data[] = (object) array(
                              'name' => $k,
                              'value' => $v
                          );
            }
            $this->getContext()->getUser()->setPref(self::PREFNS, json_encode($data), true, true);
        }
    }
}
