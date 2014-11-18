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

class Api_Store_Modifiers_StoreGroupingModifierModel extends IcingaBaseModel
    implements IDataStoreModifier {

    protected $mappedParameters = array(
                                      "groupfields" => "groupfields"
                                  );

    protected $groupfields = array();

    public function setGroupfields($field) {
        if (is_array($field)) {
            $field = implode(",",$field);
        }

        $this->groupfields = $field;
    }
    public function getGroupfields() {
        return $this->groupfields;
    }


    /**
    * @see IDataStoreModifier::handleArgument
    **/
    public function handleArgument($name,$value) {
        switch ($name)   {
            case 'groupfields':
                $this->groupfields = $value;
                break;
        }
    }

    /**
    * @see IDataStoreModifier::getMappedArguments();
    **/
    public function getMappedArguments() {
        return $this->mappedParameters;
    }

    /**
    *
    * @see IDataStoreModifier::modify();
    **/
    public function modify(&$o) {
        $this->modifyImpl($o); // type safe call
    }

    /**
    * Typesafe call to modify
    * @access private
    **/
    protected function modifyImpl(Doctrine_Query &$o) {
        if ($this->groupfields) {
            $groups = explode(",",$this->groupfields);
            foreach($groups as $group)
                $o->addGroupBy($group);
        }
    }

    /**
    * @see IDataStoreModifier::getJSDescriptor
    **/
    public function __getJSDescriptor() {
        return array(
                   "type"=>"group",
                   "params" => $this->getMappedArguments()
               );
    }
}


