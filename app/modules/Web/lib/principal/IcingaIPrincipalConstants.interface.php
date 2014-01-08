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
 * Possible security / credential / principal target
 * for enhance the standard icinga object<->client model
 * @author mhein
 * @package IcingaWeb
 * @subpackage Web
 */
interface IcingaIPrincipalConstants {
    /**
     * Views based on hostgroup membership
     * @var string
     */
    const TYPE_HOSTGROUP = 'IcingaHostgroup';
    
    /**
     * Security layer for hosts based on customvars
     * @var string
     */
    const TYPE_CUSTOMVAR_HOST = 'IcingaHostCustomVariablePair';
    
    /**
     * Restrict service based views with customvars
     * @var string
     */
    const TYPE_CUSTOMVAR_SERVICE = 'IcingaServiceCustomVariablePair';
    
    /**
     * Restriction for services based on servicegroups
     * @var string
     */
    const TYPE_SERVICEGROUP = 'IcingaServicegroup';
    
    /**
     * Native Icinga/Nagios(tm) security model. Show belongings
     * based on contact/contactgroup membership
     * @var string
     */
    const TYPE_CONTACTGROUP = 'IcingaContactgroup';
    
    /**
     * Restriction on hosts only
     * @var string
     * @since 1.8.0
     */
    const TYPE_HOST = 'IcingaHost';
    
    /**
     * Restriction on services only
     * @var string
     * @since 1.8.0
     */
    const TYPE_SERVICE = 'IcingaService';
}