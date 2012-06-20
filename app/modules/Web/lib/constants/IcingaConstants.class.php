<?php
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


interface IcingaConstants {

    // Host states
    const HOST_UP                           = 0;
    const HOST_DOWN                         = 1;
    const HOST_UNREACHABLE                  = 2;
    const HOST_PENDING                      = 99;

    // Service states
    const STATE_OK                          = 0;
    const STATE_WARNING                     = 1;
    const STATE_CRITICAL                    = 2;
    const STATE_UNKNOWN                     = 3;
    const STATE_PENDING                     = 99;

    // Logentry types
    const NSLOG_RUNTIME_ERROR               = 1;
    const NSLOG_RUNTIME_WARNING             = 2;
    const NSLOG_VERIFICATION_ERROR          = 4;
    const NSLOG_VERIFICATION_WARNING        = 8;
    const NSLOG_CONFIG_ERROR                = 16;
    const NSLOG_CONFIG_WARNING              = 32;
    const NSLOG_PROCESS_INFO                = 64;
    const NSLOG_EVENT_HANDLER               = 128;
    /* const NSLOG_NOTIFICATION             = 256 */ // (deprecated, not used)
    const NSLOG_EXTERNAL_COMMAND            = 512;
    const NSLOG_HOST_UP                     = 1024;
    const NSLOG_HOST_DOWN                   = 2048;
    const NSLOG_HOST_UNREACHABLE            = 4096;
    const NSLOG_SERVICE_OK                  = 8192;
    const NSLOG_SERVICE_UNKNOWN             = 16384;
    const NSLOG_SERVICE_WARNING             = 32768;
    const NSLOG_SERVICE_CRITICAL            = 65536;
    const NSLOG_PASSIVE_CHECK               = 131072;
    const NSLOG_INFO_MESSAGE                = 262144;
    const NSLOG_HOST_NOTIFICATION           = 524288;
    const NSLOG_SERVICE_NOTIFICATION        = 1048576;

    // Notifications reasons
    const NOTIFICATION_NORMAL               = 0;
    const NOTIFICATION_ACKNOWLEDGEMENT      = 1;
    const NOTIFICATION_FLAPPINGSTART        = 2;
    const NOTIFICATION_FLAPPINGSTOP         = 3;
    const NOTIFICATION_FLAPPINGDISABLED     = 4;
    const NOTIFICATION_DOWNTIMESTART        = 5;
    const NOTIFICATION_DOWNTIMEEND          = 6;
    const NOTIFICATION_DOWNTIMECANCELLED    = 7;
    const NOTIFICATION_CUSTOM               = 99;

    // Comments
    const HOST_COMMENT                      = 1;
    const SERVICE_COMMENT                   = 2;

    const USER_COMMENT                      = 1;
    const DOWNTIME_COMMENT                  = 2;
    const FLAPPING_COMMENT                  = 3;
    const ACKNOWLEDGEMENT_COMMENT           = 4;
    
    // Types
    const TYPE_HOST                         = 1;
    const TYPE_SERVICE                      = 2;
}

?>
