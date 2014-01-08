<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-present Icinga Developer Team.
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
* Modifier that handles Sorting by a field and extends the DataStore by the
* setOffset and setLimit functions
*
* @package Icinga_Api
* @category DataStore
*
* @author Jannis Moßhammer <jannis.mosshammer@netways.de>
**/
class Api_Store_Modifiers_StoreSortModifierModel extends IcingaBaseModel
    implements IDataStoreModifier {

    protected $mappedParameters = array(
                                      "sortfield" => "sortfield",
                                      "dir" => "dir"
                                  );

    protected $sortfield ;
    protected $dir = "DESC";

    public function setSortfield($field) {
        $this->handleArgument("sortfield",$field);
    }
    public function setDir($dir) {
        $this->handleArgument("dir",$dir);
    }
    public function getSortfield() {
        return $this->sortfield;
    }
    public function getDir() {
        return $this->dir;
    }

    /**
    * @see IDataStoreModifier::handleArgument
    **/
    public function handleArgument($name,$value) {
        switch ($name)   {
            case 'sortfield':
                $this->sortfield = $value;
                break;

            case 'dir':
                if ($value == "ASC" || $value == "DESC") {
                    $this->dir = $value;
                } else {
                    throw new InvalidArgumentException("Sort direction $value is not allowed");
                }

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
        if ($this->sortfield) {
            $o->orderBy($this->sortfield." ".$this->dir);
        }
    }

    /**
    * @see IDataStoreModifier::getJSDescriptor
    **/
    public function __getJSDescriptor() {
        return array(
                   "type"=>"sort",
                   "params" => $this->getMappedArguments()
               );
    }
}

?>
