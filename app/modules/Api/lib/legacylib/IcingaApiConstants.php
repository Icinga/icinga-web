<?php

/**
 * Intermediate solution for compatibility with the old icinga-api
 *
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
interface IcingaApiConstants {

    // CONNECTION TYPES
    const CONNECTION_IDO = 'Ido';
    const CONNECTION_IDO_ABSTRACTION = 'IdoAbstraction';
    const CONNECTION_FILE = 'File';
    const CONNECTION_LIVESTATUS = 'Livestatus';

    // CONTACT SOURCES
    const CONTACT_SOURCE_PHP_AUTH_USER = 'PHP_AUTH_USER';

    // DEBUGGING
    const DEBUG_OVERALL_TIME = 'overall time';
    const DEBUG_LEVEL_ALL = 0xff;
    const DEBUG_LEVEL_ERROR = 0x01;
    const DEBUG_LEVEL_WARNING = 0x02;
    const DEBUG_LEVEL_DEBUG = 0x08;

    // FILE SOURCES
    const FILE_OBJECTS = 'objects';
    const FILE_RETENTION = 'retention';
    const FILE_STATUS = 'status';

    // TARGET TYPES
    const TARGET_INSTANCE = 'instance';
    const TARGET_HOST = 'host';
    const TARGET_SERVICE = 'service';
    const TARGET_HOSTGROUP = 'hostgroup';
    const TARGET_SERVICEGROUP = 'servicegroup';
    const TARGET_CONTACT = 'contact';
    const TARGET_CONTACTGROUP = 'contactgroup';
    const TARGET_TIMEPERIOD = 'timeperiod';
    const TARGET_HOSTSTATUS = 'hoststatus';
    const TARGET_SERVICESTATUS = 'servicestatus';
    const TARGET_CUSTOMVARIABLE = 'customvariable';
    const TARGET_HOST_TIMES = 'hosttimes';
    const TARGET_SERVICE_TIMES = 'servicetimes';
    const TARGET_CONFIG = 'config';
    const TARGET_PROGRAM = 'program';
    const TARGET_LOG = 'log';
    const TARGET_HOST_STATUS_SUMMARY = 'host_status_summary';
    const TARGET_SERVICE_STATUS_SUMMARY = 'service_status_summary';
    const TARGET_HOST_STATUS_SUMMARY_STRICT = 'host_status_summary_strict';
    const TARGET_SERVICE_STATUS_SUMMARY_STRICT = 'service_status_summary_strict';
    const TARGET_HOST_STATUS_HISTORY = 'host_status_history';
    const TARGET_SERVICE_STATUS_HISTORY = 'service_status_history';
    const TARGET_HOST_PARENTS = 'host_parents';
    const TARGET_NOTIFICATIONS = 'notifications';
    const TARGET_HOSTGROUP_SUMMARY = 'hostgroup_summary';
    const TARGET_SERVICEGROUP_SUMMARY = 'servicegroup_summary';
    const TARGET_COMMAND = 'command';	// livestatus only
    const TARGET_DOWNTIME = 'downtime';
    const TARGET_DOWNTIMEHISTORY = 'downtimehistory';
    const TARGET_COMMENT = 'comment';
    const TARGET_HOST_COMMENT = 'hostcomment';
    const TARGET_SERVICE_COMMENT = 'servicecomment';
    const TARGET_STATUS = 'status';		// livestatus only
    const TARGET_HOST_SERVICE = 'host_service';
    // SEARCH TYPES
    const SEARCH_TYPE_COUNT = 'count';

    // SEARCH AGGREGATORS
    const SEARCH_OR = 'or';
    const SEARCH_AND = 'and';

    // MATCH TYPES
    const MATCH_EXACT = '=';
    const MATCH_NOT_EQUAL = '!=';
    const MATCH_LIKE = 'like';
    const MATCH_NOT_LIKE = 'not like';
    const MATCH_GREATER_THAN = '>';
    const MATCH_GREATER_OR_EQUAL = '>=';
    const MATCH_LESS_THAN = '<';
    const MATCH_LESS_OR_EQUAL = '<=';

    // RESULT TYPES
    const RESULT_OBJECT = 'object';
    const RESULT_ARRAY = 'array';

    // HOST STATES
    const HOST_STATE_OK = 0;
    const HOST_STATE_UNREACHABLE = 1;
    const HOST_STATE_DOWN = 2;

    // SERVICE STATES
    const SERVICE_STATE_OK = 0;
    const SERVICE_STATE_WARNING = 1;
    const SERVICE_STATE_CRITICAL = 2;
    const SERVICE_STATE_UNKNOWN = 3;

    // COMMAND INTERFACES
    const COMMAND_PIPE = 'Pipe';
    const COMMAND_SSH = 'Ssh';

    // COMMAND FIELDS
    const COMMAND_INSTANCE = 'instance';
    const COMMAND_HOSTGROUP = 'hostgroup';
    const COMMAND_SERVICEGROUP = 'servicegroup';
    const COMMAND_HOST = 'host';
    const COMMAND_SERVICE = 'service';
    const COMMAND_ID = 'id';
    const COMMAND_AUTHOR = 'author';
    const COMMAND_COMMENT = 'comment';
    const COMMAND_STARTTIME = 'starttime';
    const COMMAND_ENDTIME = 'endtime';
    const COMMAND_STICKY = 'sticky';
    const COMMAND_PERSISTENT = 'persistent';
    const COMMAND_NOTIFY = 'notify';
    const COMMAND_RETURN_CODE = 'return_code';
    const COMMAND_CHECKTIME = 'checktime';
    const COMMAND_FIXED = 'fixed';
    const COMMAND_OUTPUT = 'output';
    const COMMAND_PERFDATA = 'perfdata';
    const COMMAND_DURATION = 'duration';
    const COMMAND_DATA = 'data';
    const COMMAND_NOTIFICATION_OPTIONS = 'notification_options';
    const COMMAND_DOWNTIME_ID = 'downtime_id';



}
?>
