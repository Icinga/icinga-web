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
/*global Ext: false, Icinga: false, AppKit: false, _: false, Cronk: false */

Ext.ns("Cronk.grid.handler");

(function () {
    
    Cronk.grid.handler.Info = {
        host: function() {
            this.setHandlerArgs({type: "host"});
            Cronk.grid.handler.Info.show.apply(this, arguments);
        },
        
        service: function() {
            this.setHandlerArgs({type: "service"});
            Cronk.grid.handler.Info.show.apply(this, arguments);
        },
        
        show: function() {
            var field = this.getHandlerArgs().objectid_field || "object_id";
            var object_id = this.getRecord().get(field);
            var type = this.getHandlerArgs().type;
            
            if (Ext.isEmpty(type)) {
                throw new Error("type must be one of host or service");
            }
            
            if (Ext.isEmpty(object_id)) {
                throw new Error("Could not get object_id, please configure objectid_field properly");
            }
            
            Cronk.grid.components.ObjectInfo.showObjectInfo(type, object_id, this.getGrid().selectedConnection);
        }
    };
    
})();