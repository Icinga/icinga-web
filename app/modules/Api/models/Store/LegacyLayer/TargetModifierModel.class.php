<?php

class Api_Store_LegacyLayer_TargetModifierModel extends IcingaStoreTargetModifierModel {
    public function resolveColumnAlias($alias) {
        if (isset($this->columns[$alias])) {
            return $this->columns[$alias];
        } else {
            return $alias;
        }
    }

    protected $ignoreIds = false;
    protected $retainedAlias = false;
    public $columns = array(
            'PROBLEMS_OBJECT_ID'           =>        'op.object_id',

            // Program information
            // 'PROGRAM_INSTANCE_ID'          =>        'pe.instance_id',
            // 'PROGRAM_DATE'                 =>        'pe.program_date',
            // 'PROGRAM_VERSION'              =>        'pe.program_version',

            'PROGRAMSTATUS_ID' => 'ps.programstatus_id',
            'PROGRAMSTATUS_INSTANCE_ID' => 'ps.instance_id',
            'PROGRAMSTATUS_STATUS_UPDATE_TIME' => 'ps.status_update_time',
            'PROGRAMSTATUS_PROGRAM_START_TIME' => 'ps.program_start_time',
            'PROGRAMSTATUS_PROGRAM_END_TIME' => 'ps.program_end_time',
            'PROGRAMSTATUS_IS_CURRENTLY_RUNNING' => 'ps.is_currently_running',
            'PROGRAMSTATUS_PROCESS_ID' => 'ps.process_id',
            'PROGRAMSTATUS_DAEMON_MODE' => 'ps.daemon_mode',
            'PROGRAMSTATUS_LAST_COMMAND_CHECK' => 'ps.last_command_check',
            'PROGRAMSTATUS_LAST_LOG_ROTATION' => 'ps.last_log_rotation',
            'PROGRAMSTATUS_NOTIFICATIONS_ENABLED' => 'ps.notifications_enabled',
            'PROGRAMSTATUS_ACTIVE_SERVICE_CHECKS_ENABLED' => 'ps.active_service_checks_enabled',
            'PROGRAMSTATUS_PASSIVE_SERVICE_CHECKS_ENABLED' => 'ps.passive_service_checks_enabled',
            'PROGRAMSTATUS_ACTIVE_HOST_CHECKS_ENABLED' => 'ps.active_host_checks_enabled',
            'PROGRAMSTATUS_PASSIVE_HOST_CHECKS_ENABLED' => 'ps.passive_host_checks_enabled',
            'PROGRAMSTATUS_EVENT_HANDLERS_ENABLED' => 'ps.event_handlers_enabled',
            'PROGRAMSTATUS_FLAP_DETECTION_ENABLED' => 'ps.flap_detection_enabled',
            'PROGRAMSTATUS_FAILURE_PREDICTION_ENABLED' => 'ps.failure_prediction_enabled',
            'PROGRAMSTATUS_PROCESS_PERFORMANCE_DATA' => 'ps.process_performance_data',
            'PROGRAMSTATUS_OBSESS_OVER_HOSTS' => 'ps.obsess_over_hosts',
            'PROGRAMSTATUS_OBSESS_OVER_SERVICES' => 'ps.obsess_over_services',
            'PROGRAMSTATUS_MODIFIED_HOST_ATTRIBUTES' => 'ps.modified_host_attributes',
            'PROGRAMSTATUS_MODIFIED_SERVICE_ATTRIBUTES' => 'ps.modified_service_attributes',
            'PROGRAMSTATUS_GLOBAL_HOST_EVENT_HANDLER' => 'ps.global_host_event_handler',
            'PROGRAMSTATUS_GLOBAL_SERVICE_EVENT_HANDLER' => 'ps.global_service_event_handler',


            // Instance things
            'INSTANCE_ID'                  =>        'i.instance_id',
            'INSTANCE_NAME'                =>        'i.instance_name',
            'INSTANCE_DESCRIPTION'         =>        'i.instance_description',

            // Hostgroup data
            'HOSTGROUP_ID'                 =>        'hg.hostgroup_id',
            'HOSTGROUP_OBJECT_ID'          =>        'ohg.object_id',
            'HOSTGROUP_INSTANCE_ID'        =>        'hg.instance_id',
            'HOSTGROUP_NAME'               =>        'ohg.name1',
            'HOSTGROUP_ALIAS'              =>        'hg.alias',

            // Servicegroup data
            'SERVICEGROUP_ID'              =>        'sg.servicegroup_id',
            'SERVICEGROUP_OBJECT_ID'        =>       'osg.object_id',
            'SERVICEGROUP_INSTANCE_ID'        =>     'sg.instance_id',
            'SERVICEGROUP_NAME'            =>        'osg.name1',
            'SERVICEGROUP_ALIAS'           =>        'sg.alias',

            // Contactgroup data
            'CONTACTGROUP_ID'              =>        'cg.contactgroup_id',
            'CONTACTGROUP_OBJECT_ID'        =>       'ocg.object_id',
            'CONTACTGROUP_INSTANCE_ID'        =>     'cg.instance_id',
            'CONTACTGROUP_NAME'            =>        'ocg.name1',
            'CONTACTGROUP_ALIAS'           =>        'cg.alias',

            // Contact data
            'CONTACT_NAME'                 =>        'oc.name1',
            'CONTACT_CUSTOMVARIABLE_NAME'        =>  'cvsc.varname',
            'CONTACT_CUSTOMVARIABLE_VALUE'        => 'cvsc.varvalue',
            'CONTACT_CONTACT_ID'     =>      'cgm.contact_id',
            'CONTACT_INSTANCE_ID'    =>      'cgm.instance_id',
            'CONTACT_CONFIG_TYPE'    =>      'cgm.config_type',
            'CONTACT_CONTACT_OBJECT_ID'      =>      'cgm.contact_object_id',
            'CONTACT_ALIAS'  =>      'cgm.alias',
            'CONTACT_EMAIL_ADDRESS'  =>      'cgm.email_address',
            'CONTACT_PAGER_ADDRESS'  =>      'cgm.pager_address',
            'CONTACT_HOST_TIMEPERIOD_OBJECT_ID'      =>      'cgm.host_timeperiod_object_id',
            'CONTACT_SERVICE_TIMEPERIOD_OBJECT_ID'   =>      'cgm.service_timeperiod_object_id',
            'CONTACT_HOST_NOTIFICATIONS_ENABLED'     =>      'cgm.host_notifications_enabled',
            'CONTACT_SERVICE_NOTIFICATIONS_ENABLED'  =>      'cgm.service_notifications_enabled',
            'CONTACT_CAN_SUBMIT_COMMANDS'    =>      'cgm.can_submit_commands',
            'CONTACT_NOTIFY_SERVICE_RECOVERY'        =>      'cgm.notify_service_recovery',
            'CONTACT_NOTIFY_SERVICE_WARNING'         =>      'cgm.notify_service_warning',
            'CONTACT_NOTIFY_SERVICE_UNKNOWN'         =>      'cgm.notify_service_unknown',
            'CONTACT_NOTIFY_SERVICE_CRITICAL'        =>      'cgm.notify_service_critical',
            'CONTACT_NOTIFY_SERVICE_FLAPPING'        =>      'cgm.notify_service_flapping',
            'CONTACT_NOTIFY_SERVICE_DOWNTIME'        =>      'cgm.notify_service_downtime',
            'CONTACT_NOTIFY_HOST_RECOVERY'   =>      'cgm.notify_host_recovery',
            'CONTACT_NOTIFY_HOST_DOWN'       =>      'cgm.notify_host_down',
            'CONTACT_NOTIFY_HOST_UNREACHABLE'        =>      'cgm.notify_host_unreachable',
            'CONTACT_NOTIFY_HOST_FLAPPING'   =>      'cgm.notify_host_flapping',
            'CONTACT_NOTIFY_HOST_DOWNTIME'   =>      'cgm.notify_host_downtime',
            
            // Timeperiod data
            'TIMEPERIOD_ID'                =>        'tp.timeperiod_id',
            'TIMEPERIOD_OBJECT_ID'         =>        'otp.object_id',
            'TIMEPERIOD_INSTANCE_ID'        =>       'tp.instance_id',
            'TIMEPERIOD_NAME'              =>        'otp.name1',
            'TIMEPERIOD_ALIAS'             =>        'tp.alias',
            'TIMEPERIOD_DAY'               =>        'tptr.day',
            'TIMEPERIOD_STARTTIME'         =>        'tptr.start_sec',
            'TIMEPERIOD_ENDTIME'           =>        'tptr.end_sec',

            // Customvariable data
            'CUSTOMVARIABLE_ID'            =>        'cv.customvariable_id',
            'CUSTOMVARIABLE_OBJECT_ID'        =>     'cv.object_id',
            'CUSTOMVARIABLE_INSTANCE_ID'        =>   'cv.instance_id',
            'CUSTOMVARIABLE_NAME'          =>        'cv.varname',
            'CUSTOMVARIABLE_VALUE'         =>        'cv.varvalue',
            'CUSTOMVARIABLE_MODIFIED'        =>      'cvs.has_been_modified',
            'CUSTOMVARIABLE_UPDATETIME'        =>    'cvs.status_update_time',

            // Host data
            'HOST_ID'                      =>        'h.host_id',
            'HOST_OBJECT_ID'               =>        'oh.object_id',
            'HOST_INSTANCE_ID'             =>        'h.instance_id',
            'HOST_NAME'                    =>        'oh.name1',
            'HOST_ALIAS'                   =>        'h.alias',
            'HOST_DISPLAY_NAME'            =>        'h.display_name',
            'HOST_ADDRESS'                 =>        'h.address',
            'HOST_ADDRESS6'                =>        'h.address6',
            'HOST_ACTIVE_CHECKS_ENABLED'        =>   'h.active_checks_enabled',
            'HOST_CONFIG_TYPE'             =>        'h.config_type',
            'HOST_FLAP_DETECTION_ENABLED'        =>  'hs.flap_detection_enabled',
            'HOST_PROCESS_PERFORMANCE_DATA'        =>'hs.process_performance_data',
            'HOST_FRESHNESS_CHECKS_ENABLED'        =>'h.freshness_checks_enabled',
            'HOST_FRESHNESS_THRESHOLD'        =>     'h.freshness_threshold',
            'HOST_PASSIVE_CHECKS_ENABLED'        =>  'hs.passive_checks_enabled',
            'HOST_EVENT_HANDLER_ENABLED'        =>   'h.event_handler_enabled',
            'HOST_ACTIVE_CHECKS_ENABLED'        =>   'hs.active_checks_enabled',
            'HOST_RETAIN_STATUS_INFORMATION'        =>'h.retain_status_information',
            'HOST_RETAIN_NONSTATUS_INFORMATION'        =>'h.retain_nonstatus_information',
            'HOST_NOTIFICATIONS_ENABLED'        =>   'hs.notifications_enabled',
            'HOST_OBSESS_OVER_HOST'        =>        'h.obsess_over_host',
            'HOST_FAILURE_PREDICTION_ENABLED'        =>        'hs.failure_prediction_enabled',
            'HOST_NOTES'                   =>        'h.notes',
            'HOST_NOTES_URL'               =>        'h.notes_url',
            'HOST_ACTION_URL'              =>        'h.action_url',
            'HOST_ICON_IMAGE'              =>        'h.icon_image',
            'HOST_ICON_IMAGE_ALT'          =>        'h.icon_image_alt',
            'HOST_IS_ACTIVE'               =>        'oh.is_active',
            'HOST_OUTPUT'                  =>        'hs.output',
            'HOST_LONG_OUTPUT'             =>        'hs.long_output',
            'HOST_PERFDATA'                =>        'hs.perfdata',
            'HOST_CURRENT_STATE'           =>        'hs.current_state',
            'HOST_CURRENT_CHECK_ATTEMPT'        =>   'hs.current_check_attempt',
            'HOST_MAX_CHECK_ATTEMPTS'        =>      'hs.max_check_attempts',
            'HOST_LAST_CHECK'              =>        'hs.last_check',
            'HOST_LAST_STATE_CHANGE'        =>       'hs.last_state_change',
            'HOST_CHECK_TYPE'              =>        'hs.check_type',
            'HOST_LATENCY'                 =>        'hs.latency',
            'HOST_EXECUTION_TIME'          =>        'hs.execution_time',
            'HOST_NEXT_CHECK'              =>        'hs.next_check',
            'HOST_HAS_BEEN_CHECKED'        =>        'hs.has_been_checked',
            'HOST_LAST_HARD_STATE_CHANGE'        =>  'hs.last_hard_state_change',
            'HOST_LAST_NOTIFICATION'        =>       'hs.last_notification',
            'HOST_PROCESS_PERFORMANCE_DATA'        =>       'h.process_performance_data',
            'HOST_STATE_TYPE'              =>        'hs.state_type',
            'HOST_IS_FLAPPING'             =>        'hs.is_flapping',
            'HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED'        => 'hs.problem_has_been_acknowledged',
            'HOST_SCHEDULED_DOWNTIME_DEPTH'        =>'hs.scheduled_downtime_depth',
            'HOST_SHOULD_BE_SCHEDULED'         =>     'hs.should_be_scheduled',
            'HOST_STATUS_UPDATE_TIME'          =>    'hs.status_update_time',
            'HOST_EXECUTION_TIME_MIN'          =>    'min(hs.execution_time)',
            'HOST_EXECUTION_TIME_AVG'          =>    'avg(hs.execution_time)',
            'HOST_EXECUTION_TIME_MAX'        =>    'max(hs.execution_time)',
            'HOST_LATENCY_MIN'               =>    'min(hs.latency)',
            'HOST_LATENCY_AVG'               =>    'avg(hs.latency)',
            'HOST_LATENCY_MAX'               =>    'max(hs.latency)',
            'HOST_ALL'                       =>    'h.*',
            'HOST_STATUS_ALL'                =>      'hs.*',
            'HOST_STATE'                     =>    'hs.current_state',
            'HOST_STATE_COUNT'               =>    'count(hs.current_state)',
            'HOST_PARENT_OBJECT_ID'          =>    'ohp.object_id',
            'HOST_PARENT_NAME'               =>    'ohp.name1',
            'HOST_CHILD_OBJECT_ID'           =>    'oh.object_id',
            'HOST_CHILD_NAME'                =>      'oh.name1',
            'HOST_CUSTOMVARIABLE_NAME'         =>        'cvsh.varname',
            'HOST_CUSTOMVARIABLE_VALUE'        =>        'cvsh.varvalue',
            'HOST_CURRENT_PROBLEM_STATE'   =>  '(hs.current_state*(hs.problem_has_been_acknowledged-1)*(hs.scheduled_downtime_depth-1))',
            'HOST_IS_PENDING'       =>        '(hs.has_been_checked-1)*-1',
            // Service data

            'SERVICE_ID'            =>        's.service_id',
            'SERVICE_INSTANCE_ID'   =>        's.instance_id',
            'SERVICE_CONFIG_TYPE'   =>        's.config_type',
            'SERVICE_IS_ACTIVE'     =>        'os.is_active',
            'SERVICE_OBJECT_ID'     =>        'os.object_id',
            'SERVICE_NAME'          =>        'os.name2',
            'SERVICE_DISPLAY_NAME'  =>        's.display_name',
            'SERVICE_NOTIFICATIONS_ENABLED'=> 'ss.notifications_enabled',
            'SERVICE_FLAP_DETECTION_ENABLED'=>'ss.flap_detection_enabled',
            'SERVICE_PASSIVE_CHECKS_ENABLED'=>'ss.passive_checks_enabled',
            'SERVICE_EVENT_HANDLER_ENABLED'=> 's.event_handler_enabled',
            'SERVICE_ACTIVE_CHECKS_ENABLED'=> 'ss.active_checks_enabled',
            'SERVICE_RETAIN_STATUS_INFORMATION'=>'s.retain_status_information',
            'SERVICE_RETAIN_NONSTATUS_INFORMATION'=>'s.retain_nonstatus_information',
            'SERVICE_OBSESS_OVER_SERVICE'=>   's.obsess_over_service',
            'SERVICE_FAILURE_PREDICTION_ENABLED'=>'s.failure_prediction_enabled',
            'SERVICE_NOTES'        =>       's.notes',
            'SERVICE_NOTES_URL'    =>       's.notes_url',
            'SERVICE_ACTION_URL'   =>       's.action_url',
            'SERVICE_ICON_IMAGE'   =>       's.icon_image',
            'SERVICE_ICON_IMAGE_ALT' =>     's.icon_image_alt',
            'SERVICE_OUTPUT'        =>      'ss.output',
            'SERVICE_LONG_OUTPUT'   =>      'ss.long_output',
            'SERVICE_PERFDATA'      =>      'ss.perfdata',
            'SERVICE_PROCESS_PERFORMANCE_DATA'        =>       's.process_performance_data',
            'SERVICE_CURRENT_STATE' =>      'ss.current_state',
            'SERVICE_CURRENT_CHECK_ATTEMPT'=>'ss.current_check_attempt',
            'SERVICE_MAX_CHECK_ATTEMPTS'=>  'ss.max_check_attempts',
            'SERVICE_LAST_CHECK'    =>      'ss.last_check',
            'SERVICE_LAST_STATE_CHANGE'=>   'ss.last_state_change',
            'SERVICE_CHECK_TYPE'    =>      'ss.check_type',
            'SERVICE_LATENCY'       =>      'ss.latency',
            'SERVICE_EXECUTION_TIME' =>     'ss.execution_time',
            'SERVICE_NEXT_CHECK'    =>      'ss.next_check',
            'SERVICE_HAS_BEEN_CHECKED'=>    'ss.has_been_checked',
            'SERVICE_LAST_HARD_STATE'=>     'ss.last_hard_state',
            'SERVICE_LAST_HARD_STATE_CHANGE'=>'ss.last_hard_state_change',
            'SERVICE_LAST_NOTIFICATION'=>   'ss.last_notification',
            'SERVICE_STATE_TYPE'     =>     'ss.state_type',
            'SERVICE_IS_FLAPPING'    =>     'ss.is_flapping',
            'SERVICE_PROBLEM_HAS_BEEN_ACKNOWLEDGED'=>'ss.problem_has_been_acknowledged',
            'SERVICE_SCHEDULED_DOWNTIME_DEPTH' =>        'ss.scheduled_downtime_depth',
            'SERVICE_SHOULD_BE_SCHEDULED'=> 'ss.should_be_scheduled',
            'SERVICE_STATUS_UPDATE_TIME'=>  'ss.status_update_time',
            'SERVICE_EXECUTION_TIME_MIN' => 'min(ss.execution_time)',
            'SERVICE_EXECUTION_TIME_AVG' => 'avg(ss.execution_time)',
            'SERVICE_EXECUTION_TIME_MAX' => 'max(ss.execution_time)',
            'SERVICE_LATENCY_MIN'        => 'min(ss.latency)',
            'SERVICE_LATENCY_AVG'        => 'avg(ss.latency)',
            'SERVICE_LATENCY_MAX'        => 'max(ss.latency)',
            'SERVICE_ALL'               =>  's.*',
            'SERVICE_STATUS_ALL'        =>  'ss.*',
            'SERVICE_CUSTOMVARIABLE_NAME'=> 'cvss.varname',
            'SERVICE_CUSTOMVARIABLE_VALUE'=>'cvss.varvalue',
            'SERVICE_STATE_COUNT'        => 'count(ss.current_state)',
            'SERVICE_CURRENT_PROBLEM_STATE'   =>  '(ss.current_state*(ss.problem_has_been_acknowledged-1)*(ss.scheduled_downtime_depth-1))',
            'SERVICE_IS_PENDING'        =>  '(ss.has_been_checked-1)*-1',

            // Config vars
            'CONFIG_VAR_ID'             =>  'cfv.configfilevariable_id',
            'CONFIG_VAR_INSTANCE_ID'    =>  'cfv.instance_id',
            'CONFIG_VAR_NAME'           =>  'cfv.varname',
            'CONFIG_VAR_VALUE'          =>  'cfv.varvalue',

            // Logentries
            'LOG_ID'                    =>  'le.logentry_id',
            'LOG_INSTANCE_ID'           =>  'le.instance_id',
            'LOG_TIME'                  =>  'le.logentry_time',
            'LOG_ENTRY_TIME'            =>  'le.entry_time',
            'LOG_ENTRY_TIME_USEC'       =>  'le.entry_time_usec',
            'LOG_TYPE'                  =>  'le.logentry_type',
            'LOG_DATA'                  =>  'le.logentry_data',
            'LOG_REALTIME_DATA'         =>  'le.realtime_data',
            'LOG_INFERRED_DATA'         =>  'le.inferred_data_extracted',

            // Commands
            'COMMAND_NAME'              =>  'oco.name1',
            'COMMAND_ID'                =>  'co.command_id',
            'COMMAND_INSTANCE_ID'       =>  'co.instance_id',
            'COMMAND_CONFIG_TYPE'       =>  'co.config_type',
            'COMMAND_OBJECT_ID'         =>  'oco.object_id',
            'COMMAND_COMMAND_LINE'      =>  'co.command_line',

            // Statehistory
            'STATEHISTORY_ID'           =>  'sh.statehistory_id',
            'STATEHISTORY_INSTANCE_ID'  =>  'sh.instance_id',
            'STATEHISTORY_STATE_TIME'   =>  'sh.state_time',
            'STATEHISTORY_STATE_TIME_USEC'=>'sh.state_time_used',
            'STATEHISTORY_OBJECT_ID'    =>  'sh.object_id',
            'STATEHISTORY_STATE_CHANGE' =>  'sh.state_change',
            'STATEHISTORY_STATE'        =>  'sh.state',
            'STATEHISTORY_STATE_TYPE'   =>  'sh.state_type',
            'STATEHISTORY_CURRENT_CHECK_ATTEMPT'=> 'sh.current_check_attempt',
            'STATEHISTORY_MAX_CHECK_ATTEMPTS'   => 'sh.max_check_attempts',
            'STATEHISTORY_LAST_STATE'           => 'sh.last_state',
            'STATEHISTORY_LAST_HARD_STATE'      => 'sh.last_hard_state',
            'STATEHISTORY_OUTPUT'               => 'sh.output',
            'STATEHISTORY_LONG_OUTPUT'          => 'sh.long_output',

            // Notifications
            'NOTIFICATION_ID'           =>  'n.notification_id',
            'NOTIFICATION_INSTANCE_ID'  =>  'n.instance_id',
            'NOTIFICATION_TYPE'         =>  'n.notification_type',
            'NOTIFICATION_CONTACT'      =>  'nc.alias',
            'NOTIFICATION_REASON'       =>  'n.notification_reason',
            'NOTIFICATION_STARTTIME'    =>  'n.start_time',
            'NOTIFICATION_STARTTIME_USEC'=> 'n.start_time_usec',
            'NOTIFICATION_ENDTIME'        =>'n.end_time',
            'NOTIFICATION_ENDTIME_USEC' =>  'n.end_time_usec',
            'NOTIFICATION_STATE'        =>  'n.state',
            'NOTIFICATION_OUTPUT'       =>  'n.output',
            'NOTIFICATION_LONG_OUTPUT'  =>  'n.long_output',
            'NOTIFICATION_ESCALATED'    =>  'n.escalated',
            'NOTIFICATION_NOTIFIED'     =>  'n.contacts_notified',
            'NOTIFICATION_OBJECT_ID'    =>  'on.object_id',
            'NOTIFICATION_OBJECTTYPE_ID'=>  'on.objecttype_id',

            // Summary queries
            'HOSTGROUP_SUMMARY_COUNT'   =>  'count(oh.object_id)', //'count(%s)',
            'SERVICEGROUP_SUMMARY_COUNT'=>  'count(ss.current_state)', //'count(%s)',

            // Comments
            'COMMENT_ID'        =>          'co.comment_id',
            'COMMENT_INSTANCE_ID'       =>  'co.instance_id',
            'COMMENT_ENTRY_TIME'        =>  'co.entry_time',
            'COMMENT_ENTRY_TIME_USEC'   =>  'co.entry_time_usec',
            'COMMENT_TYPE'              =>  'co.comment_type',
            'COMMENT_ENTRY_TYPE'        =>  'co.entry_type',
            'COMMENT_OBJECT_ID'         =>  'co.object_id',
            'COMMENT_TIME'              =>  'co.comment_time',
            'COMMENT_INTERNAL_ID'       =>  'co.internal_comment_id',
            'COMMENT_AUTHOR_NAME'       =>  'co.author_name',
            'COMMENT_DATA'              =>  'co.comment_data',
            'COMMENT_IS_PERSISTENT'     =>  'co.is_persistent',
            'COMMENT_SOURCE'            =>  'co.comment_source',
            'COMMENT_EXPIRES'           =>  'co.expires',
            'COMMENT_EXPIRATION_TIME'   =>  'co.expiration_time',

            // Downtimehistory
            'DOWNTIMEHISTORY_ID'        =>  'dth.downtimehistory_id',
            'DOWNTIMEHISTORY_INSTANCE_ID'=> 'dth.instance_id',
            'DOWNTIMEHISTORY_DOWNTIME_TYPE'=>'dth.downtime_type',
            'DOWNTIMEHISTORY_OBJECT_ID' =>  'dth.object_id',
            'DOWNTIMEHISTORY_ENTRY_TIME'=>  'dth.entry_time',
            'DOWNTIMEHISTORY_AUTHOR_NAME'=> 'dth.author_name',
            'DOWNTIMEHISTORY_COMMENT_DATA'=>'dth.comment_data',
            'DOWNTIMEHISTORY_INTERNAL_DOWNTIME_ID'=>'dth.internal_downtime_id',
            'DOWNTIMEHISTORY_TRIGGERED_BY_ID'=> 'dth.triggered_by_id',
            'DOWNTIMEHISTORY_IS_FIXED'  =>  'dth.is_fixed',
            'DOWNTIMEHISTORY_DURATION'  =>  'dth.duration',
            'DOWNTIMEHISTORY_SCHEDULED_START_TIME'  =>  'dth.scheduled_start_time',
            'DOWNTIMEHISTORY_SCHEDULED_END_TIME'    =>  'dth.scheduled_end_time',
            'DOWNTIMEHISTORY_WAS_STARTED'           =>  'dth.was_started',
            'DOWNTIMEHISTORY_ACTUAL_START_TIME'     =>  'dth.actual_start_time',
            'DOWNTIMEHISTORY_ACTUAL_START_TIME_USEC'=>  'dth.actual_start_time_usec',
            'DOWNTIMEHISTORY_ACTUAL_END_TIME'       =>  'dth.actual_end_time',
            'DOWNTIMEHISTORY_ACTUAL_END_TIME_USEC'  =>  'dth.actual_end_time_usec',
            'DOWNTIMEHISTORY_WAS_CANCELLED'         =>  'dth.was_cancelled',

            // Downtime
            'DOWNTIME_ID'                   =>        'dt.scheduleddowntime_id',
            'DOWNTIME_INSTANCE_ID'          =>        'dt.instance_id',
            'DOWNTIME_DOWNTIME_TYPE'        =>        'dt.downtime_type',
            'DOWNTIME_OBJECT_ID'            =>        'dt.object_id',
            'DOWNTIME_ENTRY_TIME'           =>        'dt.entry_time',
            'DOWNTIME_AUTHOR_NAME'          =>        'dt.author_name',
            'DOWNTIME_COMMENT_DATA'         =>        'dt.comment_data',
            'DOWNTIME_INTERNAL_DOWNTIME_ID' =>        'dt.internal_downtime_id',
            'DOWNTIME_TRIGGERED_BY_ID'      =>        'dt.triggered_by_id',
            'DOWNTIME_IS_FIXED'             =>        'dt.is_fixed',
            'DOWNTIME_DURATION'             =>        'dt.duration',
            'DOWNTIME_SCHEDULED_START_TIME' =>        'dt.scheduled_start_time',
            'DOWNTIME_SCHEDULED_END_TIME'   =>        'dt.scheduled_end_time',
            'DOWNTIME_WAS_STARTED'          =>        'dt.was_started',
            'DOWNTIME_ACTUAL_START_TIME'    =>        'dt.actual_start_time',
            'DOWNTIME_ACTUAL_START_TIME_USEC'=>       'dt.actual_start_time_usec'
    );
    /**
     * @see StoreTargetModifierModel::defaultJoinType
     *
     **/
    protected $defaultJoinType = "inner";
    protected $additionalSelects = array();
    protected $forceGroup = array();
    protected $resultColumns = array();

    public function reset() {
        $this->setDistinct(true);
        $this->defaultJoinType = "inner";
        $this->additionalSelects = array();
        $this->forceGroup = array();
        $this->ignoreIds = false;
        $this->resultColumns = array();
        parent::reset();
    }

    public function setIgnoreIds($boolean) {
        $this->ignoreIds = $boolean;
    }

    /**
     * @deprecated
     * Takes depracted icinga_api target names and sets up this modifier
     * to act like the deprecated icinga api
     *
     * @param    String  The Api target to translate
     *
     * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
     **/
    protected function setupApiTargetFor($target) {
        $this->reset();
        $this->retainedAlias = false;
        switch ($target) {
            case IcingaApiConstants::TARGET_INSTANCE:
                $this->mainAlias = "i";
                $this->setTarget("IcingaInstances");
                $this->aliasDefs = array(
                        "h" => array("src"=>"i", "relation" => "hosts"),
                        "s" => array("src"=>"i", "relation" => "services"),
                        "cg" => array("src" => "i", "relation"=>"contactgroups"),
                        "cgm" => array("src" => "i", "relation"=>"contacts"),
                        "oc"  => array("src" => "cgm","relation" => "object"),
                        "ocg"  => array("src" => "cg","relation" => "object"),
                        "ps" => array("src" => "i", "relation" => "programstatus", "alwaysJoin" => true)
                );
                break;

            case IcingaApiConstants::TARGET_HOST:
                $this->mainAlias = "oh";
                $this->setTarget("IcingaObjects");
                $this->retainedAlias = "h";

                $this->aliasDefs = array(
                        "h"  => array("src" => "oh", "relation" => "host", "alwaysJoin" => true),
                        "hs"  => array("src" => "h", "relation" => "status","alwaysJoin" => true),
                        "i"   => array("src" => "h", "relation" => "instance"),
                        "cg"  => array("src" => "h", "relation" => "contactgroups", "type"=>"left"),
                        "cgm" => array("src" => "cg", "relation" => "members", "type"=>"left"),
                        "hg"  => array("src" => "h", "relation" => "hostgroups", "type"=>"left"),
                        'ohg' => array('src' => 'hg', "relation" => "object", "type"=>"left"),
                        "hgm" => array("src" => "hg","relation" => "members", "type"=>"left"),
                        "oc"  => array("src" => "cgm","relation" => "object"),
                        "ocg"  => array("src" => "cg","relation" => "object"),
                        "os"     => array("src" => "s", "relation" => "object"),
                        "dt"   => array("src" => "h", "relation" => "scheduledDowntimes"),
                        "cvsh"=> array("src" => "h","relation" => "customvariablestatus"),
                        "cvsc"=> array("src" => "cgm","relation" => "customvariablestatus"),
                        "s" => array("src" => "h", "relation" => "services"),
                        "ss" => array("src" => "s", "relation" => "status"),

                        "os" => array("src" => "h", "relation" => "object")
                );
                break;

            case IcingaApiConstants::TARGET_SERVICE:
                $this->mainAlias = "os";
                $this->setTarget("IcingaObjects");
                $this->retainedAlias = "s";
                $this->aliasDefs = array(
                        "s"  => array("src" => "os", "relation" => "service", "alwaysJoin" => true),
                        "i"  => array("src" => "s", "relation" => "instance"),
                        "h"  => array("src" => "s","relation" => "host"),
                        "hs" => array("src" => "h","relation" => "status","alwaysJoin" => true, "type"=>"left"),
                        "oh" => array("src" => "h","relation" => "object"),
                        /*                                       "hcg" => array("src" => "h", "relation" => "contactgroups"),
                                       "hcgm" => array("src" => "h", "relation" => "contacts"),*/
                        "cg" => array("src" => "s", "relation" => "contactgroups"),
                        "cgm" => array("src"=> "cg", "relation" => "members"),
                        "cvsh"=> array("src" => "s","relation" => "customvariablestatus"),
                        "cvsc"=> array("src" => "cgm","relation" => "customvariablestatus"),
                        "ss" => array("src" => "s","relation" => "status","alwaysJoin" => true, "type"=>"left"),

                        "sg" => array(
                                "src" => "s",
                                "relation" => "servicegroups",
                                "type" => "left"
                        ),
                        "sgm" => array(
                                "src" => "sg",
                                "relation" => "members",
                                "type"=>"left"
                        ),
                        "osg" => array(
                                "src" => "sg",
                                "relation" => "object",
                                "type"=>"left"
                        ),
                        "oc"  => array("src" => "cgm","relation" => "object"),
                        "ocg"  => array("src" => "cg","relation" => "object"),
                        "hg"  => array("src" => "h", "relation" => "hostgroups", "type"=>"left"),
                        "hgm" => array("src" => "hg","relation" => "members", "type"=>"left"),
                        "ohg" => array("src" => "hg","relation" => "object"),
                        "cvsh" => array("src" => "h","relation"=> "customvariablestatus"),
                        "cvss"=> array("src" => "s","relation" => "customvariablestatus"),
                        "cvsc"=> array("src" => "cgm","relation" => "customvariablestatus")
                );
                break;

            case IcingaApiConstants::TARGET_HOSTGROUP:
                $this->mainAlias = "hg";
                $this->setTarget("IcingaHostgroups");

                $this->aliasDefs = array(
                        "ohg"   => array("src" => "hg", "relation" => "object"),
                        "hgm"   => array("src" => "hg", "relation" => "members"),
                        "h"     => array("src" => "hg", "relation" => "members"),
                        "s"     => array("src" => "h", "relation" => "services"),
                        "sg"    => array("src" => "s", "relation" => "servicegroups"),
                        "hs"    => array("src" => "hgm", "relation" => "status"),
                        "oh"    => array("src" => "hgm", "relation" => "object"),
                        "cg" => array("src" => "hgm", "relation" => "contactgroups"),
                        "cgm" => array("src"=> "cg", "relation" => "members"),
                        "oc"  => array("src" => "cgm","relation" => "object"),
                        "ocg"  => array("src" => "cg","relation" => "object")
                );
                break;

            case IcingaApiConstants::TARGET_SERVICEGROUP:
                $this->mainAlias = "sg";
                $this->setTarget("IcingaServicegroups");

                $this->aliasDefs = array(
                        "osg"   => array("src" => "sg", "relation" => "object"),
                        "sgm"   => array("src" => "sg", "relation" => "members"),
                        "s"     => array("src" => "sg", "relation" => "members"),
                        "hg"    => array("src" => "h", "relation"=>"hostgroups"),
                        "h"     => array("src" => "s", "relation" => "host"),
                        "os"    => array("src" => "sgm", "relation" => "object"),
                        "cg" => array("src" => "sgm", "relation" => "contactgroups"),
                        "cgm" => array("src"=> "cg", "relation" => "members"),
                        "oc"  => array("src" => "cgm","relation" => "object"),
                        "ocg"  => array("src" => "cg","relation" => "object")
                );
                break;
        
            case IcingaApiConstants::TARGET_CONTACT:
                $this->mainAlias = "cgm";
                $this->setTarget("IcingaContacts");

                $this->aliasDefs = array(
                        "ocg"   => array("src" => "cg", "relation" => "object"),
                        "cg"   => array("src" => "cgm", "relation" => "contactgroups","join"=>"left"),
                        "cvsc"    => array("src" => "cgm", "relation" => "customvariablestatus"),
                        "oc"  => array("src" => "cgm","relation" => "object")
                );
                break;

            case IcingaApiConstants::TARGET_CONTACTGROUP:
                $this->mainAlias = "cg";
                $this->setTarget("IcingaContactgroups");

                $this->aliasDefs = array(
                        "ocg"   => array("src" => "cg", "relation" => "object"),
                        "cgm"   => array("src" => "cg", "relation" => "members"),
                        "cvsc"    => array("src" => "cgm", "relation" => "customvariablestatus"),
                        "oc"  => array("src" => "cgm","relation" => "object")
                );
                break;

            case IcingaApiConstants::TARGET_TIMEPERIOD:
                $this->mainAlias = "tp";
                $this->setTarget("IcingaTimeperiods");

                $this->aliasDefs = array(
                        "otp"   => array("src" => "tp", "relation" => "object"),
                        "tptr"   => array("src" => "tp", "relation" => "timeranges")
                );
                break;

            case IcingaApiConstants::TARGET_CUSTOMVARIABLE:
                $this->mainAlias = "cv";
                $this->setTarget("IcingaCustomvariables");

                $this->aliasDefs = array(
                        "cvs"   => array("src" => "cv", "relation" => "customvariablestatus")
                );

                break;

            case IcingaApiConstants::TARGET_CONFIG:
                $this->mainAlias = "cfv";
                $this->setTarget("IcingaConfigvariables");

                break;

            case IcingaApiConstants::TARGET_PROGRAM:
                $this->mainAlias = "pe";
                $this->setTarget("IcingaProcessevents");

                $this->addStaticWhereField("pe.event_type = ?",100);
                break;

            case IcingaApiConstants::TARGET_LOG:
                $this->mainAlias = "le";
                $this->setTarget("IcingaLogentries");

                $this->aliasDefs = array(
                        "i" => array("src" => "le", "relation" => "instance")
                );
                break;
            //
            case IcingaApiConstants::TARGET_HOST_STATUS_SUMMARY_STRICT:
                $this->mainAlias = "h";
                $this->setTarget("IcingaHosts");
                $this->additionalSelects["HOST_STATE"] = "hs.current_state";
                $this->additionalSelects["COUNT"] = "count(DISTINCT h.host_object_id)";
                $this->ignoreIds = true;
                $this->forceGroup[] = "hs.current_state";
                $this->forceGroup[] = "(hs.has_been_checked-hs.should_be_scheduled)*-1";
                $this->retainedAlias = "h";
                $this->aliasDefs = array(
                        "hs"  => array("src" => "h", "relation" => "status","type"=>"inner","alwaysJoin"=>true),
                        "s"  => array("src" => "h", "relation" => "services"),
                        "oh" => array("src" => "h", "relation" => "object", "alwaysJoin" => true, "with"=>"oh.is_active=1"),
                        "i"  => array("src" => "h", "relation" => "instance"),
                        "cg" => array("src" => "h", "relation" => "contactgroups"),
                        "ocg"=> array("src" => "cg", "relation" => "object"),
                        "cgm"=> array("src" => "cg", "relation" => "members"),
                        "oc" => array("src" => "cgm", "relation" => "object"),
                        "hg" => array("src" => "h", "relation" => "hostgroups","type"=>"left"),
                        "hgm" => array("src" => "hg", "relation" => "members","type"=>"left"),
                        "ohg" => array("src" => "hg", "relation" => "object","type"=>"left"),
                        "cvsh"=> array("src" => "h", "relation" => "customvariablestatus"),
                        "cvsc"=> array("src" => "cgm", "relation" => "customvariablestatus"),
                );
                break;

            case IcingaApiConstants::TARGET_SERVICE_STATUS_SUMMARY_STRICT:
                $this->mainAlias = "ss";
                $this->ignoreIds = true;
                $this->setTarget("IcingaServicestatus");
                $this->additionalSelects["SERVICE_STATE"] = "ss.current_state";
                $this->additionalSelects["COUNT"] = "count(DISTINCT s.service_object_id)";
                $this->retainedAlias = "s";
                $this->forceGroup[] = "ss.current_state";
                $this->forceGroup[] = "(ss.has_been_checked-ss.should_be_scheduled)*-1";
                $this->aliasDefs = array(
                        "s"  => array("src" => "ss", "relation" => "service", "alwaysJoin" => true, "type"=>"inner"),
                        "os" => array("src" => "ss", "relation" => "serviceobject", "alwaysJoin" => true, "with"=>"os.is_active=1"),
                        "i"  => array("src" => "s", "relation" => "instance"),
                        "cg" => array("src" => "s", "relation" => "contactgroups"),
                        "ocg"=> array("src" => "cg", "relation" => "object"),
                        "cgm"=> array("src" => "cg", "relation" => "members"),
                        "oc" => array("src" => "cgm", "relation" => "object"),
                        "sg" => array("src" => "s", "relation" => "servicegroups"),
                        "h" => array("src" => "s", "relation" => "host"),
                        "hs" => array("src" => "h", "relation" => "status"),
                        "sgm" => array("src" => "sg", "relation" => "members"),
                        "osg" => array("src" => "sg", "relation" => "object"),
                        "cvss"=> array("src" => "s", "relation" => "customvariablestatus"),
                        "cvsh"=> array("src" => "h","relation" => "customvariablestatus"),
                        "oh"   => array("src" => "h", "relation" => "object"),
                        "hg"   => array("src" => "h", "relation" => "hostgroups"),
                        "ohg"   => array("src" => "hg", "relation" => "object"),
                        "cvsc"=> array("src" => "cgm", "relation" => "customvariablestatus")
                );
                break;

            case IcingaApiConstants::TARGET_HOST_STATUS_SUMMARY:
                $this->mainAlias = "h";
                $this->setTarget("IcingaHosts");
                $this->additionalSelects["HOST_STATE"] = "hs.current_state";
                $this->additionalSelects["COUNT"] = "count(DISTINCT h.host_object_id)";
                $this->ignoreIds = true;
                $this->forceGroup[] = "hs.current_state";
                $this->forceGroup[] = "(hs.has_been_checked-hs.should_be_scheduled)*-1";
                $this->retainedAlias = "h";
                $this->aliasDefs = array(
                        "hs"  => array("src" => "h", "relation" => "status","type"=>"left","alwaysJoin"=>true),
                        "s"  => array("src" => "h", "relation" => "services"),
                        "oh" => array("src" => "h", "relation" => "object", "alwaysJoin" => true, "with"=>"oh.is_active=1"),
                        "i"  => array("src" => "h", "relation" => "instance"),
                        "cg" => array("src" => "h", "relation" => "contactgroups"),
                        "ocg"=> array("src" => "cg", "relation" => "object"),
                        "cgm"=> array("src" => "cg", "relation" => "members"),
                        "oc" => array("src" => "cgm", "relation" => "object"),
                        "hg" => array("src" => "h", "relation" => "hostgroups","type"=>"left"),
                        "hgm" => array("src" => "hg", "relation" => "members","type"=>"left"),
                        "ohg" => array("src" => "hg", "relation" => "object","type"=>"left"),
                        "cvsh"=> array("src" => "h", "relation" => "customvariablestatus"),
                        "cvsc"=> array("src" => "cgm", "relation" => "customvariablestatus"),
                );
                break;

            case IcingaApiConstants::TARGET_SERVICE_STATUS_SUMMARY:
                $this->mainAlias = "ss";
                $this->ignoreIds = true;
                $this->setTarget("IcingaServicestatus");
                $this->additionalSelects["SERVICE_STATE"] = "ss.current_state";
                $this->additionalSelects["COUNT"] = "count(DISTINCT s.service_object_id)";
                $this->retainedAlias = "s";
                $this->forceGroup[] = "ss.current_state";
                $this->forceGroup[] = "(ss.has_been_checked-ss.should_be_scheduled)*-1";
                $this->aliasDefs = array(
                        "s"  => array("src" => "ss", "relation" => "service", "alwaysJoin" => true, "type"=>"inner"),
                        "os" => array("src" => "ss", "relation" => "serviceobject", "alwaysJoin" => true, "with"=>"os.is_active=1"),
                        "i"  => array("src" => "s", "relation" => "instance"),
                        "cg" => array("src" => "s", "relation" => "contactgroups"),
                        "ocg"=> array("src" => "cg", "relation" => "object"),
                        "cgm"=> array("src" => "cg", "relation" => "members"),
                        "oc" => array("src" => "cgm", "relation" => "object"),
                        "sg" => array("src" => "s", "relation" => "servicegroups"),
                        "h" => array("src" => "s", "relation" => "host"),
                        "hs" => array("src" => "h", "relation" => "status"),
                        "sgm" => array("src" => "sg", "relation" => "members"),
                        "osg" => array("src" => "sg", "relation" => "object"),
                        "cvss"=> array("src" => "s", "relation" => "customvariablestatus"),
                        "cvsh"=> array("src" => "h","relation" => "customvariablestatus"),
                        "oh"   => array("src" => "h", "relation" => "object"),
                        "hg"   => array("src" => "h", "relation" => "hostgroups"),
                        "ohg"   => array("src" => "hg", "relation" => "object"),
                        "cvsc"=> array("src" => "cgm", "relation" => "customvariablestatus")
                );
                break;

            case IcingaApiConstants::TARGET_HOST_STATUS_HISTORY:
                $this->mainAlias = "sh";
                $this->setTarget("IcingaStatehistory");

                $this->aliasDefs = array(
                        "oh" => array("src" => "sh", "relation" => "object"),
                        "h"  => array("src" => "sh", "relation" => "hosts"),
                        "i"  => array("src" => "h", "relation" => "instance"),
                        "cg" => array("src" => "h", "relation" => "contactgroups"),
                        "ocg"=> array("src" => "cg", "relation" => "object"),
                        "cgm"=> array("src" => "cg", "relation" => "members"),
                        "oc" => array("src" => "cgm", "relation" => "object"),
                        "hg" => array("src" => "h", "relation" => "hostgroups"),
                        "hgm" => array("src" => "hg", "relation" => "members"),
                        "ohg" => array("src" => "hg", "relation" => "object"),
                        "cvsh"=> array("src" => "h", "relation" => "customvariablestatus"),
                        "cvsc"=> array("src" => "cgm", "relation" => "customvariablestatus")
                );
                break;

            case IcingaApiConstants::TARGET_SERVICE_STATUS_HISTORY:
                $this->mainAlias = "sh";
                $this->setTarget("IcingaStatehistory");
                $this->aliasDefs = array(
                        "os" => array("src" => "sh", "relation" => "object"),
                        "s"  => array("src" => "sh", "relation" => "services"),
                        "h"  => array("src" => "s", "relation" => "host"),
                        "oh" => array("src" => "h", "relation" => "object"),
                        "i"  => array("src" => "s", "relation" => "instance"),
                        "cg" => array("src" => "s", "relation" => "contactgroups"),
                        "ocg"=> array("src" => "cg", "relation" => "object"),
                        "cgm"=> array("src" => "cg", "relation" => "members"),
                        "oc" => array("src" => "cgm", "relation" => "object"),
                        "hg" => array("src" => "h", "relation" => "hostgroups"),
                        "hgm" => array("src" => "hg", "relation" => "members"),
                        "ohg" => array("src" => "hg", "relation" => "object"),
                        "cvss"=> array("src" => "s","relation" => "customvariablestatus"),
                        "cvsh"=> array("src" => "h", "relation" => "customvariablestatus"),
                        "cvsc"=> array("src" => "cgm", "relation" => "customvariablestatus")

                );
                break;

            case IcingaApiConstants::TARGET_HOST_PARENTS:
                $this->mainAlias = "h";
                $this->setTarget("IcingaHosts");
                $this->additionalSelects = array(
                        'HOST_PARENT_OBJECT_ID' => 'ohp.object_id',
                        'HOST_PARENT_NAME' => 'ohp.name1',
                        'HOST_CHILD_OBJECT_ID' => 'oh.object_id',
                        'HOST_CHILD_NAME' => 'oh.name1'
                );
                $this->aliasDefs = array(
                        "oh" => array("src" => "h", "relation" => "object"),
                        "hph" => array("src" => "h", "relation" => "parents"),
                        "ohp" => array("src" => "hph", "relation" => "object"),
                        "hg" => array("src" => "h", "relation" => "hostgroups"),
                        "ohg" => array("src" => "hg", "relation" => "object"),
                        "cvsh" => array("src" => "h","relation"=> "customvariablestatus"),
                        "cg" => array("src" => "h", "relation" => "contactgroups"),
                        "cgm" => array("src"=> "cg", "relation" => "members"),
                        "oc"  => array("src" => "cgm","relation" => "object"),
                        "ocg"  => array("src" => "cg","relation" => "object")

                );
                break;

            case IcingaApiConstants::TARGET_NOTIFICATIONS:
                $this->mainAlias = "n";
                $this->setTarget("IcingaNotifications");
                $this->aliasDefs = array(
                        "on" => array(
                                "src" => "n",
                                "relation" => "object",

                                "alwaysJoin" => true
                        ),
                        "s" => array(
                                "src" => "n",
                                "relation" => "services",
                                "type" => "left"
                        ),
                        "h" => array(
                                "src" => "s",
                                "relation" => "host",
                                "on" => "("
                                        . "(n.notification_type = 0 and n.object_id = h.host_object_id)"
                                        . " OR "
                                        . "(n.notification_type = 1 and s.host_object_id = h.host_object_id)"
                                        . ")",
                                "type" => "left"
                        ),
                        "hg" => array(
                                "src" => "h",
                                "relation" => "hostgroups"
                        ),
                        "ohg" => array(
                                "src" => "hg",
                                "relation" => "object"
                        ),
                        "oh" => array(
                                "src" => "h",
                                "relation" => "object",
                                "type" => "left"
                        ),
                        "os" => array(
                                "src" => "s",
                                "relation" => "object",
                                "type" => "left"
                        ),
                        "sg" => array(
                                "src" => "s",
                                "relation" => "servicegroups"
                        ),
                        "osg" => array(
                                "src" => "sg",
                                "relation" => "object"

                        ),
                        "nc" => array("src"=>"n","relation"=>"notificationcontacts"),
//                                        "co" => array("src" => "nc", "relation" => "command"),
//                                        "oco" => array("src" => "co", "relation" => "object"),
                        "nm" => array("src"=>"n","relation"=>"notificationmethods"),
                        "co" => array("src"=>"nm","relation"=>"command"),
                        "oco" => array("src"=>"co","relation"=>"object"),
                        "cg"   => array("src" => "s", "relation" => "contactgroups"),
                        "cvsh" => array("src" => "h","relation"=> "customvariablestatus"),
                        "cvss"=> array("src" => "s","relation" => "customvariablestatus"),
                        "cgm" => array("src"=> "cg", "relation" => "members"),
                        "oc"  => array("src" => "cgm","relation" => "object"),
                        "ocg"  => array("src" => "cg","relation" => "object")
                );
                break;

            case IcingaApiConstants::TARGET_HOSTGROUP_SUMMARY:
                $this->mainAlias = "hg";
                $this->setDistinct(true);
                $this->setTarget("IcingaHostgroups");
                $this->ignoreIds = true;
                $this->aliasDefs = array(
                        "i"   => array("src" => "hg", "relation" => "instance"),
                        "ohg"   => array(
                                "src" => "hg",
                                "alwaysJoin" => true,
                                "relation" => "object",
                                "with" => "ohg.is_active = 1"
                        ),
                        "h" => array("src" => "hg", "relation" => "members"),
                        "hgm"   => array("src" => "hg", "relation" => "members"),
                        "s"   => array("src" => "hgm", "relation" => "services"),
                        "hs"    => array("src" => "hgm", "relation" => "status"),
                        "cg"   => array("src" => "h", "relation" => "contactgroups"),
                        "oh"    => array("src" => "hgm", "relation" => "object"),
                        "cgm" => array("src"=> "cg", "relation" => "members"),
                        "oc"  => array("src" => "cgm","relation" => "object"),
                        "ocg"  => array("src" => "cg","relation" => "object")
                );

                break;

            case IcingaApiConstants::TARGET_SERVICEGROUP_SUMMARY:
                $this->mainAlias = "sg";
                $this->ignoreIds = true;
                $this->setTarget("IcingaServicegroups");
                $this->aliasDefs = array(
                        "i"   => array("src" => "sg", "relation" => "instance"),
                        "osg"   => array("src" => "sg", "relation" => "object"),
                        "sgm"   => array("src" => "sg", "relation" => "members"),
                        "s"   => array("src" => "sg", "relation" => "members"),
                        "h"   => array("src" => "s", "relation" => "host"),
                        "oh"   => array("src" => "h", "relation" => "object"),
                        "hg"   => array("src" => "h", "relation" => "hostgroups"),
                        "ohg"   => array("src" => "hg", "relation" => "object"),
                        "ss"   => array("src" => "sgm", "relation" => "status"),
                        "cg"   => array("src" => "s", "relation" => "contactgroups"),
                        "os"    => array("src" => "sgm", "relation" => "object"),
                        "cgm" => array("src"=> "cg", "relation" => "members"),
                        "oc"  => array("src" => "cgm","relation" => "object"),
                        "ocg"  => array("src" => "cg","relation" => "object")
                );
                break;

            case IcingaApiConstants::TARGET_COMMENT:
                $this->mainAlias = "co";
                $this->setDistinct(false);
                $this->aliasDefs = array(
                        "i"   => array("src" => "co", "relation" => "instance"),
                        "s" => array("src" => "co", "relation" => "service","type"=>"left"),
                        "h" => array("src" => "co", "relation" => "host","type"=>"left"),
                        "sh" => array("src" => "s", "relation" => "object", "type"=>"left"),
                        "oh" => array("src" => "h", "relation" => "object","type"=>"left"),
                        "os" => array("src" => "s", "relation" => "object","type"=>"left"),
                        "hs" => array("src" => "h", "relation" => "status"),
                        "ss" => array("src" => "s", "relation" => "status", "type"=>"left"),
                        "cg" => array("src" => "h", "relation" => "contactgroups"),
                        "cgm" => array("src"=> "cg", "relation" => "members"),
                        "oc"  => array("src" => "cgm","relation" => "object"),
                        "ocg"  => array("src" => "cg","relation" => "object")
                );
                $this->setTarget("IcingaComments");
                break;

            case IcingaApiConstants::TARGET_HOST_COMMENT:
                $this->mainAlias = "co";
                $this->setDistinct(false);
                $this->aliasDefs = array(
                        "i"   => array("src" => "co", "relation" => "instance"),
                        "h" => array("src" => "co", "relation" => "host","type"=>"inner", "alwaysJoin" => true),
                        "s" => array("src" => "h", "relation" => "service","type"=>"left"),
                        "sh" => array("src" => "s", "relation" => "object"),
                        "oh" => array("src" => "h", "relation" => "object","type"=>"left"),
                        "os" => array("src" => "s", "relation" => "object","type"=>"left"),
                        "hs" => array("src" => "h", "relation" => "status"),
                        "ss" => array("src" => "s", "relation" => "status"),
                        "cg" => array("src" => "h", "relation" => "contactgroups"),
                        "cgm" => array("src"=> "cg", "relation" => "members"),
                        "oc"  => array("src" => "cgm","relation" => "object"),
                        "ocg"  => array("src" => "cg","relation" => "object")
                );
                $this->setTarget("IcingaComments");
                break;

            case IcingaApiConstants::TARGET_SERVICE_COMMENT:
                $this->mainAlias = "co";
                $this->setDistinct(false);
                $this->aliasDefs = array(
                        "i"   => array("src" => "co", "relation" => "instance"),
                        "s" => array("src" => "co", "relation" => "service","type"=>"inner", "alwaysJoin"=>true),
                        "h" => array("src" => "s", "relation" => "host","type"=>"left"),
                        "sh" => array("src" => "s", "relation" => "object"),
                        "oh" => array("src" => "h", "relation" => "object","type"=>"left"),
                        "os" => array("src" => "s", "relation" => "object","type"=>"left"),
                        "hs" => array("src" => "h", "relation" => "status"),
                        "ss" => array("src" => "s", "relation" => "status"),
                        "cg" => array("src" => "h", "relation" => "contactgroups"),
                        "cgm" => array("src"=> "cg", "relation" => "members"),
                        "oc"  => array("src" => "cgm","relation" => "object"),
                        "ocg"  => array("src" => "cg","relation" => "object")
                );
                $this->setTarget("IcingaComments");
                break;

            case IcingaApiConstants::TARGET_HOST_SERVICE:
                $this->mainAlias = "op";
                $this->setTarget("IcingaObjects");
                $this->retainedAlias = "h";

                $this->aliasDefs = array(

                        "os"  => array(
                                "src" => "op",
                                "relation" => "object",
                                "type"=>"left",
                                "alwaysJoin" => true,
                                "with" => "op.objecttype_id = 2"
                        ),
                        "s"  => array(
                                "src" => "os",
                                "relation" => "service",
                                "alwaysJoin" => true,
                                "type" => "left"
                        ),
                        "i"  => array(
                                "src" => "s",
                                "relation" => "instance",
                                "type" => "left"
                        ),
                        "cg" => array("src" => "s", "relation" => "contactgroups"),
                        "cgm" => array("src"=> "cg", "relation" => "members"),
                        "oc"  => array("src" => "cgm","relation" => "object"),
                        "ocg"  => array("src" => "cg","relation" => "object"),
                        "cvsh"=> array("src" => "s","relation" => "customvariablestatus"),
                        "cvsc"=> array("src" => "cgm","relation" => "customvariablestatus"),
                        "ss" => array(
                                "src" => "s",
                                "relation" => "status",
                                "type" => "left",
                                "alwaysJoin" => true
                        ),
                        "hs" => array(
                                "src" => "h",
                                "relation" => "status",
                                "type" => "left",
                                "alwaysJoin" => true
                        ),
                        "oh" => array(
                                "src" => "op",
                                "relation" => "object",
                                "type" => "inner",
                                "alwaysJoin" => true,
                                "on" => "(oh.object_id = op.object_id and op.objecttype_id = 1)".
                                        " OR oh.object_id = s.host_object_id"
                        ),
                        "h" => array(
                                "src" => "oh",
                                "relation" => "host",
                                "alwaysJoin" => true,
                                "type"=>"inner"
                        ),
                        "sg" => array(
                                "src" => "s",
                                "relation" => "servicegroups",
                                "type" => "left"
                        ),
                        "sgm" => array(
                                "src" => "sg",
                                "relation" => "members",
                                "type" => "left"
                        ),
                        "osg" => array(
                                "src" => "sg",
                                "relation" => "object",
                                "type" => "left"
                        ),
                        "hg"  => array(
                                "src" => "h",
                                "relation" => "hostgroups",
                                "type" => "left"
                        ),
                        "hgm" => array(
                                "src" => "hg",
                                "relation" => "members",
                                "type" => "left"
                        ),
                        "ohg" => array(
                                "src" => "hg",
                                "relation" => "object",
                                "type" => "left"
                        ),
                        "cvsh" => array("src" => "h","relation"=> "customvariablestatus"),
                        "cvss"=> array("src" => "s","relation" => "customvariablestatus"),
                        "cvsc"=> array("src" => "cgm","relation" => "customvariablestatus")
                );
                break;

            case IcingaApiConstants::TARGET_DOWNTIMEHISTORY:
                $this->mainAlias = "dth";
                $this->setTarget("IcingaDowntimehistory");
                $this->defaultJoinType = "left";
                $this->aliasDefs = array(
                        "i"  => array("src" => "dth", "relation" => "instance"),
                        "os" => array("src" => "dth", "relation" => "object"),
                        "s" => array("src" => "dth", "relation" => "service"),
                        "ss" => array("src" => "s","relation" => "status"),
                        "h" => array("src" => "dth", "relation" => "host"),
                        "sh" => array("src" => "dth","relation" => "object"),
                        "oh" => array("src" => "dth","relation" => "object"),

                        "sg" => array("src" => "s","relation" => "servicegroups"),
                        "sgm" => array("src" => "sg", "relation" => "members"),
                        "osg" => array("src" => "sg", "relation" => "object"),
                        "hg"  => array("src" => "h", "relation" => "hostgroups"),
                        "hgm" => array("src" => "hg","relation" => "members"),
                        "ohg" => array("src" => "hg","relation" => "object"),
                        "hs"  => array("src" => "h", "relation" => "status"),
                        "cg" => array("src" => "h", "relation" => "contactgroups"),
                        "cgm" => array("src"=> "cg", "relation" => "members"),
                        "oc"  => array("src" => "cgm","relation" => "object"),
                        "ocg"  => array("src" => "cg","relation" => "object")
                );
                break;

            case IcingaApiConstants::TARGET_DOWNTIME:
                $this->mainAlias = "dt";
                $this->setTarget("IcingaScheduleddowntime");
                $this->defaultJoinType = "left";
                $this->aliasDefs = array(
                        "i"  => array("src" => "dt", "relation" => "instance"),
                        "os" => array("src" => "dt", "relation" => "object"),
                        "s" => array("src" => "dt", "relation" => "service"),
                        "ss" => array("src" => "s","relation" => "status"),
                        "h" => array("src" => "dt", "relation" => "host"),
                        "sh" => array("src" => "dt","relation" => "object"),
                        "oh" => array("src" => "dt","relation" => "object"),
                        "cg" => array("src" => "h", "relation" => "contactgroups"),
                        "cgm" => array("src" => "cg", "relation" => "members"),
                        "oc" => array("src"=>"cg","relation" => "object"),
                        "ocm" => array("src"=>"cgm","relation" => "object"),
                        "sg" => array("src" => "s","relation" => "servicegroups"),
                        "sgm" => array("src" => "sg", "relation" => "members"),
                        "osg" => array("src" => "sg", "relation" => "object"),
                        "hg"  => array("src" => "h", "relation" => "hostgroups"),
                        "hgm" => array("src" => "hg","relation" => "members"),
                        "ohg" => array("src" => "hg","relation" => "object"),
                        "hs"  => array("src" => "h", "relation" => "status")
                );
                break;
        }

        foreach($this->defaultAliasDefs as $name=>$defs) {
            if (array_key_exists($name,$this->aliasDefs) || $name == $this->mainAlias) {
                continue;
            }

            $this->aliasDefs[$name] = $defs;
        }
    }


    private function checkForPending(&$cols) {
        if(!is_array($cols)) {
            if($cols == "HOST_CURRENT_STATE")
                $cols = array($cols,"HOST_IS_PENDING");
            if($cols == "SERVICE_CURRENT_STATE")
                $cols = array($cols,"SERVICE_IS_PENDING");
        } else {
            if(in_array("HOST_CURRENT_STATE",$cols))
                $cols[] = "HOST_IS_PENDING";
            if(in_array("SERVICE_CURRENT_STATE",$cols))
                $cols[] = "SERVICE_IS_PENDING";
        }
    }

    public function setResultColumns($cols, $replace=false) {
        $this->checkForPending($cols);
        if (is_array($cols)) {
            if ($replace === true) {
                $this->resultColumns = $cols;
            } else {
             $this->resultColumns = array_merge($this->resultColumns,$cols);
            }
        } else {
            $this->resultColumns[] = $cols;
        }

        $this->setFields($cols,true,$replace);
    }

    public function setSearchTarget($target) {
        $this->reset();
        $this->setupApiTargetFor($target);
    }


    public function setSearchFilterAppendix($statement, $searchAggregator = IcingaApiConstants::SEARCH_AND) {
        $match = array();

        while (preg_match("/\\$\{(?<match>.*?)\}/",$statement,$match)) {
            $statement = preg_replace("/\\$\{\w+\}/",$this->columns[$match["match"]],$statement,1);
        }

        $this->addStaticWhereField($statement/*,$searchAggregator*/);
    }

    protected function modifyImpl(Doctrine_Query &$o) {
        if (!$this->ignoreIds) {
            $table = $o->getConnection()->getTable($this->getTarget());
            $keys = $table->getIdentifierColumnNames();
            $o->addSelect(implode($keys));
        }

        foreach($this->additionalSelects as $alias => $select) {
            $o->addSelect($select.(is_numeric($alias) ? "" : " AS ".$alias));

        }
        $db = $this->getContext()->getDatabaseManager()->getDatabase('icinga');
        // check if retained state must be respected
        if(method_exists($db,"useRetained")) {
            /*
             * the core with idomod dumps 2 different config types
             * idomod.cfg:config_output_options
             * 1 = original config => config_type = 0
             * 2 = retained config => config_type = 1
             * 3 = both, both config_types are available
            */
            if($this->retainedAlias) {
                $o->andWhere($this->retainedAlias.".config_type= ?",$db->useRetained() ? "1" : "0");

            }
        }
        if($this->getTarget() == "IcingaObjects")
            $o->andWhere($this->mainAlias.".is_active = 1");

        foreach($this->forceGroup as $group) {
            $o->addGroupBy($group);
        }
        parent::modifyImpl($o);
    }

    public function getResultColumns() {
        $result = array();
        foreach($this->additionalSelects as $alias=>$val) {
            if (!is_numeric($alias)) {
                $result[] = $alias;
            }
        }

        return array_unique(array_merge($result,$this->resultColumns));
    }

    /**
     * returns an array containing the names of all affected columns
     * @return array
     * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
     */
    public function getAffectedColumns() {
        
        $map = array_keys($this->aliasDefs);
        $map[] = $this->mainAlias;
        $affected = array();

        foreach($map as $table) {
            $len = strlen($table);
            foreach($this->columns as $name=>$column) {
                if (substr($column,0,$len) == $table) {
                    $affected[] = $name;
                }
            }
        }
        
        return $affected;
    }


    protected $defaultAliasDefs = array(
            "i"  => array("src" => "dt", "relation" => "instance"),
            "os" => array("src" => "s", "relation" => "object"),
            "ss" => array("src" => "s","relation" => "status"),
            "oh" => array("src" => "h","relation" => "object"),
            "sg" => array("src" => "s","relation" => "servicegroups", "type" => "left"),
            "sgm" => array("src" => "sg", "relation" => "members", "type" => "left"),
            "osg" => array("src" => "sg", "relation" => "object", "type" => "left"),
            "hg"  => array("src" => "h", "relation" => "hostgroups", "type" => "left"),
            "hgm" => array("src" => "hg","relation" => "members", "type" => "left"),
            "ohg" => array("src" => "hg","relation" => "object", "type" => "left"),

            "hs"  => array("src" => "h", "relation" => "status"),
            "cvss"=> array("src" => "s","relation" => "customvariablestatus"),
            "cvsh"=> array("src" => "h", "relation" => "customvariablestatus")

    );

    protected $aliasDefs = array();
}
