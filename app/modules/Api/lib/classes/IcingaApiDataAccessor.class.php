<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author jmosshammer
 */
interface IcingaApiDataAccessor {
    const TABLE_ACKNOWLEDGEMENTS = "acknowledgements";
    const TABLE_COMMANDS = "commands";
    const TABLE_COMMENTHISTORY = "commenthistory";
    const TABLE_COMMENTS = "comments";
    const TABLE_CONFIGFILES = "configfiles";
    const TABLE_CONFIGFILEVARIABLES = "configfilevariables";
    const TABLE_CONNINFO = "conninfo";
    const TABLE_CONTACT_ADDRESSES = "contact_addresses";
    const TABLE_CONTACT_NOTIFICATIONCOMMANDS = "contact_notificationcommands";
    const TABLE_CONTACTGROUP_MEMBERS = "contactgroup_members";
    const TABLE_CONTACTGROUPS = "contactgroups";
    const TABLE_CONTACTNOTIFICATIONMETHODS = "contactnotificationmethods";
    const TABLE_CONTACTNOTIFICATIONS = "contactnotifications";
    const TABLE_CONTACTS = "contacts";
    const TABLE_CONTACTSTATUS = "contactstatus";
    const TABLE_CUSTOMVARIABLES = "customvariables";
    const TABLE_CUSTOMVARIABLESTATUS = "customvariablestatus";
    const TABLE_DBVERSION = "dbversion";
    const TABLE_DOWNTIMEHISTORY = "downtimehistory";
    const TABLE_EVENTHANDLERS = "eventhandlers";
    const TABLE_EXTERNALCOMMANDS = "externalcommands";
    const TABLE_FLAPPINGHISTORY = "flappinghistory";
    const TABLE_HOST_CONTACTGROUPS = "host_contactgroups";
    const TABLE_HOST_CONTACTS = "host_contacts";
    const TABLE_HOST_PARENTHOSTS = "host_parenthosts";
    const TABLE_HOSTCHECKS = "hostchecks";
    const TABLE_HOSTDEPENDENCIES = "hostdependencies";
    const TABLE_HOSTESCALATION_CONTACTGROUPS = "hostescalation_contactgroups";
    const TABLE_HOSTESCALATION_CONTACTS = "hostescalation_contacts";
    const TABLE_HOSTESCALATIONS = "hostescalations";
    const TABLE_HOSTGROUP_MEMBERS = "hostgroup_members";
    const TABLE_HOSTGROUPS = "hostgroups";
    const TABLE_HOSTS = "hosts";
    const TABLE_HOSTSTATUS = "hoststatus";
    const TABLE_INSTANCES = "instances";
    const TABLE_LOGENTRIES = "logentries";
    const TABLE_NOTIFICATIONS = "notifications";
    const TABLE_OBJECTS = "objects";
    const TABLE_PROCESSEVENTS = "processevents";
    const TABLE_PROGRAMSTATUS = "programstatus";
    const TABLE_RUNTIMEVARIABLES = "runtimevariables";
    const TABLE_SCHEDULEDDOWNTIME = "scheduleddowntime";
    const TABLE_SERVICE_CONTACTGROUPS = "service_contactgroups";
    const TABLE_SERVICE_CONTACTS = "service_contacts";
    const TABLE_SERVICECHECKS = "servicechecks";
    const TABLE_SERVICEDEPENDENCIES = "servicedependencies";
    const TABLE_SERVICEESCALATION_CONTACTGROUPS = "serviceescalation_contactgroups";
    const TABLE_SERVICEESCALATION_CONTACTS = "serviceescalation_contacts";
    const TABLE_SERVICEESCALATIONS = "serviceescalations";
    const TABLE_SERVICEGROUP_MEMBERS = "servicegroup_members";
    const TABLE_SERVICEGROUPS = "servicegroups";
    const TABLE_SERVICES = "services";
    const TABLE_SERVICESTATUS = "servicestatus";
    const TABLE_STATEHISTORY = "statehistory";
    const TABLE_SYSTEMCOMMANDS = "systemcommands";
    const TABLE_TIMEDEVENTQUEUE = "timedeventqueue";
    const TABLE_TIMEDEVENTS = "timedevents";
    const TABLE_TIMEPERIOD_TIMERANGES = "timeperiod_timeranges";
    const TABLE_TIMEPERIODS = "timeperiods";

}
?>
