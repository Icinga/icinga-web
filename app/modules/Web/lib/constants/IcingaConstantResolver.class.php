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


/**
 * Static class to resolve cryptic nagios constant values into
 * human readable information
 * @author mhein
 *
 */
class IcingaConstantResolver {

    const TEXT_NOT_FOUND    = '(null)';

    static private $state_type = array(
                                     0  => 'SOFT',
                                     1  => 'HARD',
                                 );

    static private $active_check_type = array(
                                            0   => 'ACTIVE',
                                            1   => 'PASSIVE'
                                        );

    static private $boolean_states = array(
                                         0  => 'No',
                                         1  => 'Yes',
                                     );

    static private $notification_types = array(
            0   => 'Host',
            1   => 'Service'
                                         );

    static private $logentry_types = array(
                                         IcingaConstants::NSLOG_RUNTIME_ERROR           => "runtime error",
                                         IcingaConstants::NSLOG_RUNTIME_WARNING         => "runtime warning",
                                         IcingaConstants::NSLOG_VERIFICATION_ERROR      => "verify error",
                                         IcingaConstants::NSLOG_VERIFICATION_WARNING        => "verify warning",
                                         IcingaConstants::NSLOG_CONFIG_ERROR                => "config error",
                                         IcingaConstants::NSLOG_CONFIG_WARNING          => "config warning",
                                         IcingaConstants::NSLOG_PROCESS_INFO                => "process info",
                                         IcingaConstants::NSLOG_EVENT_HANDLER           => "event handler",
                                         IcingaConstants::NSLOG_EXTERNAL_COMMAND            => "external command",
                                         IcingaConstants::NSLOG_HOST_UP                 => "host up",
                                         IcingaConstants::NSLOG_HOST_DOWN               => "host down",
                                         IcingaConstants::NSLOG_HOST_UNREACHABLE            => "host unreachable",
                                         IcingaConstants::NSLOG_SERVICE_OK              => "service OK",
                                         IcingaConstants::NSLOG_SERVICE_UNKNOWN         => "service unknown",
                                         IcingaConstants::NSLOG_SERVICE_WARNING         => "service warning",
                                         IcingaConstants::NSLOG_SERVICE_CRITICAL            => "service critical",
                                         IcingaConstants::NSLOG_PASSIVE_CHECK           => "passive check",
                                         IcingaConstants::NSLOG_INFO_MESSAGE                => "info message",
                                         IcingaConstants::NSLOG_HOST_NOTIFICATION       => "host notification",
                                         IcingaConstants::NSLOG_SERVICE_NOTIFICATION        => "service notification"
                                     );

    static private $logentry_type_icons = array(
            IcingaConstants::NSLOG_RUNTIME_ERROR            => "icons.gear--exclamation",
            IcingaConstants::NSLOG_RUNTIME_WARNING          => "icons.gear",
            IcingaConstants::NSLOG_VERIFICATION_ERROR       => "icons.flask--exclamation",
            IcingaConstants::NSLOG_VERIFICATION_WARNING     => "icons.flask",
            IcingaConstants::NSLOG_CONFIG_ERROR             => "icons.folder--exclamation",
            IcingaConstants::NSLOG_CONFIG_WARNING           => "icons.folder",
            IcingaConstants::NSLOG_PROCESS_INFO             => "icons.information",
            IcingaConstants::NSLOG_EVENT_HANDLER            => "icons.document-attribute-e",
            IcingaConstants::NSLOG_EXTERNAL_COMMAND         => "icons.drill--arrow",
            IcingaConstants::NSLOG_HOST_UP                  => "icons.server",
            IcingaConstants::NSLOG_HOST_DOWN                => "icons.server--exclamation",
            IcingaConstants::NSLOG_HOST_UNREACHABLE         => "icons.server--exclamation",
            IcingaConstants::NSLOG_SERVICE_OK               => "icons.system-monitor",
            IcingaConstants::NSLOG_SERVICE_UNKNOWN          => "icons.system-monitor--exclamation",
            IcingaConstants::NSLOG_SERVICE_WARNING          => "icons.system-monitor--exclamation",
            IcingaConstants::NSLOG_SERVICE_CRITICAL         => "icons.system-monitor--exclamation",
            IcingaConstants::NSLOG_PASSIVE_CHECK            => "icons.socket--arrow",
            IcingaConstants::NSLOG_INFO_MESSAGE             => "icons.newspaper",
            IcingaConstants::NSLOG_HOST_NOTIFICATION        => "icons.bell",
            IcingaConstants::NSLOG_SERVICE_NOTIFICATION     => "icons.bell"
                                          );

    /**
     * Generic resolver, change an integer into an array key
     * @param array $input
     * @param integer $key
     * @return string
     */
    protected static function resolveArrayConstants(array &$input, $key) {
        if (array_key_exists($key, $input)) {
            return $input[$key];
        }

        return self::TEXT_NOT_FOUND;
    }

    /**
     * Changes state types into names
     * @param integer $integer
     * @return string
     */
    public static function stateType($integer) {
        return self::resolveArrayConstants(self::$state_type, $integer);
    }

    /**
     * Converts is_active boolean values into an text check type
     * @param integer $integer
     * @return string
     */
    public static function activeCheckType($integer) {
        return self::resolveArrayConstants(self::$active_check_type, $integer);
    }

    /**
     * Converts boolean states into yes/no
     * @param boolean $boolean
     * @return string
     */
    public static function booleanNames($boolean) {
        $boolean ? $boolean = 1 : $boolean = 0;
        return self::resolveArrayConstants(self::$boolean_states, $boolean);
    }

    /**
     * Return the logentrytype
     * @param $key
     * @return string
     */
    public static function logentryType($key) {
        return self::resolveArrayConstants(self::$logentry_types, $key);
    }

    /**
     * Return the icon for the logentry type
     * @param $key
     * @return string
     */
    public static function logentryTypeIcon($key) {
        return self::resolveArrayConstants(self::$logentry_type_icons, $key);
    }

    /**
     * Returns the notification type
     * @param $key
     * @return unknown_type
     */
    public static function notificationType($key) {
        return self::resolveArrayConstants(self::$notification_types, $key);
    }

}

?>
