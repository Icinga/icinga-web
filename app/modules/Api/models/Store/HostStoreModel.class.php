<?php
class HostTargetModifier extends IcingaStoreTargetModifierModel {
    public function setTarget($target) {
        if (strpos($target,"Icinga") !== 0) {
            $target = "Icinga".$target;
        }

        parent::setTarget($target);
    }

    protected $allowedFields = array(
                                   "Icon"                  => "icon_image",
                                   "Host id"               => "host_id",
                                   "Instance"              => "i.instance_name",
                                   "Host name"             => "display_name",
                                   // Status definitions
                                   "Status"                => "hs.current_state",
                                   "Output"                => "hs.output",
                                   "Long output"           => "hs.long_output",
                                   "Perfdata"              => "hs.perfdata",
                                   "Last check"            => "hs.status_update_time",
                                   "Has been checked"      => "hs.has_been_checked",
                                   "Should be checked"     => "hs.should_be_checked",
                                   "Current check attempt" => "hs.current_check_attempt",
                                   "Last state change"     => "hs.last_state_change",
                                   "Last hard state change"=> "hs.last_hard_state_change",
                                   "Last hard state"       => "hs.last_hard_state",
                                   "Last time up"          => "hs.last_time_up",
                                   "Last time down"        => "hs.last_time_down",
                                   "Last time unreachable" => "hs.last_time_unreachable",
                                   "Notifications on"      => "hs.notifications_enabled",
                                   "Acknowledged"          => "hs.problem_has_been_acknowledged",
                                   "Acknowledge type"      => "hs.acknowledgment_type",
                                   "Alias"                 => "alias",
                                   "IPv4 Address"          => "address",
                                   "IPv6 Address"          => "address6",
                                   "Check command"         => "chco.command_line",
                                   "Check args"            => "check_command_args",
                                   "Check interval"        => "check_interval",
                                   "Retry Interval"        => "retry_interval",
                                   "Max check attempts"    => "max_check_attempts",
                                   "Notes url"             => "notes_url",
                                   "Notes"                 => "notes",
                                   "Action url"            => "action_url",
                                   "Freshness checks on"   => "freshness_checks_enabled",
                                   "Passive checks on"     => "passive_checks_enabled",
                                   "Event handler on"      => "active_checks_enabled",
                                   "Notifications on"      => "notifications_enabled",
                                   "Flap detection on"     => "flap_detection_enabled",
                                   "Service name"          => "s.display_name",
                                   "Service status"        => "ss.current_state",
                                   "Service output"        => "ss.output",
                                   "Service long output"   => "ss.long_output",
                                   "Service status"        => "ss.current_state",
                                   "Service last check"    => "ss.status_update_time"
                               );

    protected $sortFields = array(
                                "host_id",
                                "display_name",
                                "hs.current_state",
                                "hs_status_update_time"
                            );

    protected $defaultFields = array(
                                   "icon_image",
                                   "host_id",
                                   "display_name",
                                   "hs.current_state",
                                   "hs.output",
                                   "hs.long_output",
                                   "hs.perfdata",
                                   "hs.status_update_time"

                               );
    protected $aliasDefs = array(
                               "i"     => array("src" => "my", "relation" => "instance"),
                               "hs"    => array("src" => "my", "relation" => "status"),
                               "chco"  => array("src" => "my", "relation" => "checkCommand"),
                               "s"     => array("src" => "my", "relation" => "services"),
                               "ss"    => array("src" => "s", "relation" => "status")
                           );
}
class HostFilterModifier extends IcingaStoreFilterModifierModel {
    protected $filterClasses = array(
                                   'ApiStoreFilterGroup',
                                   'HostFilter'

                               );
}

class HostFilter extends ApiStoreFilter {
    public function initFieldDefinition() {
        $hostnameFilter = new StoreFilterField();
        $hostnameFilter->displayName    = "Hostname";
        $hostnameFilter->name = "display_name";
        $hostnameFilter->possibleValues = array(new StoreFilterFieldApiValues(
                "host","HOST_NAME", array("ANY")
                                                ));

        $statusFilter = new StoreFilterField();
        $statusFilter->displayName = "Status";
        $statusFilter->name = "hs.current_state";
        $statusFilter->possibleValues = array(
                                            "0" => "Up",
                                            "1" => "Down",
                                            "2" => "Unreachable"
                                        );

        $serviceContainFilter = new StoreFilterField();
        $serviceContainFilter->displayName    = "Contains service";
        $serviceContainFilter->name = "s.display_name";
        $serviceContainFilter->possibleValues = array(new StoreFilterFieldApiValues(
                "service","SERVICE_NAME", array("ANY")
                                                ));

        $this->addFilterField($hostnameFilter);
        $this->addFilterField($statusFilter);
        $this->addFilterField($serviceContainFilter);

    }
}


class Api_Store_HostStoreModel extends IcingaApiDataStoreModel {
    protected function setupModifiers() {
        $this->registerStoreModifier(new HostTargetModifier());
        $this->registerStoreModifier(new HostFilterModifier());
        parent::setupModifiers();
    }

}
