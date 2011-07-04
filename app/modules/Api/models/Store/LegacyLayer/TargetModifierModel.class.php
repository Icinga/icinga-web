<?php

class Api_Store_LegacyLayer_TargetModifierModel extends IcingaStoreTargetModifierModel {
    public $columns = array(
        'PROBLEMS_OBJECT_ID'           =>        'op.object_id',
        // Program information
        'PROGRAM_INSTANCE_ID'          =>        'pe.instance_id',
        'PROGRAM_DATE'                 =>        'pe.program_date',
        'PROGRAM_VERSION'              =>        'pe.program_version',

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
        'HOST_FRESHNESS_CHECKS_ENABLED'        =>'hs.freshness_checks_enabled',
        'HOST_FRESHNESS_THRESHOLD'        =>     'hs.freshness_threshold',
        'HOST_PASSIVE_CHECKS_ENABLED'        =>  'hs.passive_checks_enabled',
        'HOST_EVENT_HANDLER_ENABLED'        =>   'hs.event_handler_enabled',
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
        'HOST_IS_PENDING'       =>        '(hs.has_been_checked-hs.should_be_scheduled)*-1 as pending_status',
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
        'SERVICE_EVENT_HANDLER_ENABLED'=> 'ss.event_handler_enabled',
        'SERVICE_ACTIVE_CHECKS_ENABLED'=> 'ss.active_checks_enabled',
        'SERVICE_RETAIN_STATUS_INFORMATION'=>'s.retain_status_information',
        'SERVICE_RETAIN_NONSTATUS_INFORMATION'=>'s.retain_nonstatus_information',
        'SERVICE_OBSESS_OVER_SERVICE'=>   'ss.obsess_over_service',
        'SERVICE_FAILURE_PREDICTION_ENABLED'=>'ss.failure_prediction_enabled',
        'SERVICE_NOTES'        =>       's.notes',
        'SERVICE_NOTES_URL'    =>       's.notes_url',
        'SERVICE_ACTION_URL'   =>       's.action_url',
        'SERVICE_ICON_IMAGE'   =>       's.icon_image',
        'SERVICE_ICON_IMAGE_ALT' =>     's.icon_image_alt',
        'SERVICE_OUTPUT'        =>      'ss.output',
        'SERVICE_LONG_OUTPUT'   =>      'ss.long_output',
        'SERVICE_PERFDATA'      =>      'ss.perfdata',
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
        'SERVICE_IS_PENDING'        =>  '(ss.has_been_checked-ss.should_be_scheduled)*-1 AS pending_status',

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
        'DOWNTIMEHISTORY_ID'        =>  'dthh.downtimehistory_id',
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
    * @deprecated
    * Takes depracted icinga_api target names and sets up this modifier
    * to act like the deprecated icinga api
    *
    * @param    String  The Api target to translate
    * 
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function setupApiTargetFor($target) {
        switch($target) {
            case IcingaApiConstants::TARGET_INSTANCE:
        
            break;
            case IcingaApiConstants::TARGET_HOST:
                $this->mainAlias = "h";
                $this->setTarget("IcingaHosts");
                $this->aliasDefs = array( 
                    "oh"  => array("src" => "h", "relation" => "object"),
                    "hs"  => array("src" => "h", "relation" => "status"),
                    "i"   => array("src" => "h", "relation" => "instance"),
                    "cg"  => array("src" => "h", "relation" => "contactgroups"),
                    "cgm" => array("src" => "h", "relation" => "contacts"),
                    "hg"  => array("src" => "h", "relation" => "hostgroups"),
                    "hgm" => array("src" => "h","relation" => "members"),
                    "cvsc"=> array("src" => "h","relation" => "customvariablestatus")
                ); 
            break;
            case IcingaApiConstants::TARGET_SERVICE:
            break;
            case IcingaApiConstants::TARGET_HOSTGROUP:
            break;
            case IcingaApiConstants::TARGET_SERVICEGROUP:
            break;
            case IcingaApiConstants::TARGET_CONTACTGROUP:
            break;
            case IcingaApiConstants::TARGET_TIMEPERIOD:
            break;
            case IcingaApiConstants::TARGET_CUSTOMVARIABLE:
            break;
            case IcingaApiConstants::TARGET_CONFIG:
            break;
            case IcingaApiConstants::TARGET_PROGRAM:
            break;
            case IcingaApiConstants::TARGET_LOG:
            break;
            case IcingaApiConstants::TARGET_HOST_STATUS_SUMMARY:
            break;
            case IcingaApiConstants::TARGET_SERVICE_STATUS_SUMMARY:
            break;
            case IcingaApiConstants::TARGET_HOST_STATUS_HISTORY:
            break;
            case IcingaApiConstants::TARGET_SERVICE_STATUS_HISTROY:
            break;
            case IcingaApiConstants::TARGET_HOST_PARENTS:
            break;
            case IcingaApiConstants::TARGET_NOTIFICATIONS:
            break;
            case IcingaApiConstants::TARGET_HOSTGROUP_SUMMARY:
            break;
            case IcingaApiConstants::TARGET_SERVICEGROUP_SUMMARY:
            break;
            case IcingaApiConstants::TARGET_COMMENT:
            break;
            case IcingaApiConstants::TARGET_HOST_SERVICE:
            break;   
            case IcingaApiConstants::TARGET_DOWNTIMEHISTORY:
            break;
            case IcingaApiConstants::TARGET_DOWNTIME:
            break;
        }
    }
    public function setSearchTarget($target) {
        $this->setupApiTargetFor($target); 
    } 
    protected $aliasDefs = array( 
     
        "i"     => array("src" => "my", "relation" => "instance"),
        "hs"    => array("src" => "my", "relation" => "status"),
        "chco"  => array("src" => "my", "relation" => "checkCommand"),
        "s"     => array("src" => "my", "relation" => "services"),
        "ss"    => array("src" => "s", "relation" => "status")
    ); 
}
