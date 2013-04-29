<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2013 Icinga Developer Team.
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


class Api_DBALMetaManagerModel extends IcingaApiBaseModel {
    public static $icingaTableIdentifiers = array(
            'IcingaAcknowledgements',
            'IcingaCommands',
            'IcingaCommentHistory',
            'IcingaComments',
            'IcingaConfigFiles',
            'IcingaConfigvariables',
            'IcingaConninfo',
            'IcingaContactAddresses',
            'IcingaContactNotificationcommands',
            'IcingaContactgroupMembers',
            'IcingaContactgroups',
            'IcingaContactnotificationmethods',
            'IcingaContactnotifications',
            'IcingaContacts',
            'IcingaContactstatus',
            'IcingaCustomvariables',
            'IcingaCustomVariablestatus',
            'IcingaDbversion',
            'IcingaDowntimehistory',
            'IcingaEventhandlers',
            'IcingaExternalcommands',
            'IcingaFlappinghistory',
            'IcingaHostContactgroups',
            'IcingaHostContacts',
            'IcingaHostParenthosts',
            'IcingaHostchecks',
            'IcingaHostdependencies',
            'IcingaHostescalationContactgroups',
            'IcingaHostescalationContacts',
            'IcingaHostescalations',
            'IcingaHostgroupMembers',
            'IcingaHostgroups',
            'IcingaHosts',
            'IcingaHoststatus',
            'IcingaInstances',
            'IcingaLogentries',
            'IcingaNotifications',
            'IcingaObjects',
            'IcingaProcessevents',
            'IcingaProgramstatus',
            'IcingaRuntimevariables',
            'IcingaScheduleddowntime',
            'IcingaServiceContactgroups',
            'IcingaServiceContacts',
            'IcingaServicechecks',
            'IcingaServicedependencies',
            'IcingaServiceescalationContactgroups',
            'IcingaServiceescalationContacts',
            'IcingaServiceescalations',
            'IcingaServicegroupMembers',
            'IcingaServicegroups',
            'IcingaServices',
            'IcingaServicestatus',
            'IcingaStatehistory',
            'IcingaSystemcommands',
            'IcingaTimedeventqueue',
            'IcingaTimedevents',
            'IcingaTimeperiodTimeranges',
            'IcingaTimeperiods'
                                            );

    public function switchIcingaDatabase($connName) {
        $manager = Doctrine_Manager::getInstance();
        foreach(self::$icingaTableIdentifiers as $table)
            $manager->bindComponent($table, $connName);
    }

}

?>
