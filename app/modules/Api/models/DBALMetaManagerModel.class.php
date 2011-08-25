<?php

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
            'IcingaLogentres',
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
