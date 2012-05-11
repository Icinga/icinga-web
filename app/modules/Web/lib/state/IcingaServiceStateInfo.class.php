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


class IcingaServiceStateInfo extends IcingaStateInfo {

    /**
     * List of status id's with corresponding
     * status names
     *
     * @var array
     */
    protected $state_list = array(
                                IcingaConstants::STATE_OK       => 'OK',
                                IcingaConstants::STATE_WARNING  => 'WARNING',
                                IcingaConstants::STATE_CRITICAL => 'CRITICAL',
                                IcingaConstants::STATE_UNKNOWN  => 'UNKNOWN',
                                IcingaConstants::STATE_PENDING  => 'PENDING'
                            );

    protected $colors = array(
                            IcingaConstants::STATE_OK       => '00cc00',
                            IcingaConstants::STATE_WARNING  => 'ffff00',
                            IcingaConstants::STATE_CRITICAL => 'ff0000',
                            IcingaConstants::STATE_UNKNOWN  => 'ff8000',
                            IcingaConstants::STATE_PENDING  => 'aa77ff'
                        );

    /**
     * Shortcut to create an object instance on the fly
     *
     * @param mixed $type
     * @return IcingaHostStateInfo
     */
    public static function Create($type=99) {
        $class = __CLASS__;
        return new $class($type);
    }

}

?>
