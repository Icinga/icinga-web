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

/*global Ext: false, Icinga: false, _: false */
Ext.ns('Icinga.Cronks.Tackle');

(function () {
    "use strict";

    Icinga.Cronks.Tackle.Translation = new(Ext.extend(Object, {
        __map: {
            object_action_url: _("Action URL"),
            object_active_checks_enabled: _("Active checks enabled"),
            object_address: _("IPv4 Address"),
            object_address6: _("IPv6 Address"),
            object_alias: _("Alias"),
            object_check_type: _("Check type"),
            object_config_type: _("Config type"),
            object_current_check_attempt: _("Check attempt"),
            object_current_state: _("State"),
            object_display_name: _("Display name"),
            object_event_handler_enabled: _("Eventhandler enabled"),
            object_execution_time: _("Execution time"),
            object_execution_time_avg: _("Execution time (avg)"),
            object_execution_time_max: _("Execution time (max)"),
            object_execution_time_min: _("Execution time (min)"),
            object_failure_prediction_enabled: _("Failure prediction enabled"),
            object_flap_detection_enabled: _("Flap detection enabled"),
            object_freshness_checks_enabled: _("Freshness checks enabled"),
            object_freshness_threshold: _("Freshness threshold"),
            object_has_been_checked: _("Was checked"),
            object_icon_image: _("Icon image"),
            object_icon_image_alt: _("Icon image description"),
            object_id: _("Object ID"),
            object_instance_id: _("Object instance ID"),
            object_is_active: _("IS active"),
            object_is_flapping: _("Is flapping"),
            object_last_check: _("Last check"),
            object_last_hard_state_change: _("Last hard state"),
            object_last_notification: _("Last notification"),
            object_last_state_change: _("Last state change"),
            object_latency: _("Latency"),
            object_latency_avg: _("Latency (avg)"),
            object_latency_max: _("Latency (max)"),
            object_latency_min: _("Latency (min)"),
            object_long_output: _("Long output"),
            object_max_check_attempts: _("Max check attempts"),
            object_name: _("Name"),
            object_next_check: _("Next check"),
            object_notes: _("Notes"),
            object_notes_url: _("Notes URL"),
            object_notifications_enabled: _("Notifications enabled"),
            object_object_id: _("Object ID"),
            object_obsess_over_host: _("Obsessing enabled"),
            object_output: _("Output"),
            object_passive_checks_enabled: _("Passive enabled"),
            object_perfdata: _("Perfdata"),
            object_problem_has_been_acknowledged: _("Problem acknowledgement"),
            object_process_performance_data: _("Process performance data"),
            object_retain_nonstatus_information: _("Retain non-status information"),
            object_retain_status_information: _("Retain status information"),
            object_scheduled_downtime_depth: _("In Downtime"),
            object_should_be_scheduled: _("Should be scheduled"),
            object_state_type: _("State type"),
            object_status_update_time: _("Status time statamp")
        },

        get: function (key) {
            if (Ext.isEmpty(this.__map[key]) === false) {
                return this.__map[key];
            }

            return key;
        }

    }))();

})();