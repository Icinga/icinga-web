// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2015 Icinga Developer Team.
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
/*global Ext: false, Icinga: false, AppKit: false, _: false, Cronk: false */

Ext.ns("Cronk.grid.handler");

(function () {
    "use strict";

    /**
     * Static handler function
     * @class
     * @static
     */
    Cronk.grid.handler.Info = {

        /**
         * Show info box for host
         */
        host: function() {
            this.setHandlerArgs({type: "host"});
            Cronk.grid.handler.Info.show.apply(this, arguments);
        },

        /**
         * Show info box for service
         */
        service: function() {
            this.setHandlerArgs({type: "service"});
            Cronk.grid.handler.Info.show.apply(this, arguments);
        },

        /**
         * Abstract show call
         *
         * @private
         */
        show: function() {
            var field = this.getHandlerArgs().objectid_field || "object_id";
            var type = this.getHandlerArgs().type;
            var record = this.getRecord();
            var object_id = record.get(field);
            var titleSuffix = "";

            if (record.get('host_name')) {
                titleSuffix += record.get('host_name');
            }

            if (record.get('service_name') && type === 'service') {
                titleSuffix += ' / ' + record.get('service_name');
            }
            
            if (Ext.isEmpty(type)) {
                throw new Error("type must be one of host or service");
            }
            
            if (Ext.isEmpty(object_id)) {
                throw new Error("Could not get object_id, please configure objectid_field properly");
            }
            
            Cronk.grid.components.ObjectInfo.showObjectInfo(
                type,
                object_id,
                this.getGrid().selectedConnection,
                titleSuffix
            );
        }
    };
    
})();
