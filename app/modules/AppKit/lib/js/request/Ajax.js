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

Ext.Ajax.request = function(o) {
    var req = null;
    if(!o.icingaAction || !o.icingaModule) {
        req = this.directRequest(o);
    } else {
        req = this.dispatchRequest(o);
    }
    if(Ext.isObject(o.cancelOn)) {
        o.cancelOn.component.on(o.cancelOn.event,function() {
            Ext.data.Connection.prototype.abort.call(this,req);
        });
    }
    Ext.EventManager.on(window,"beforeunload",function() {
        Ext.data.Connection.prototype.abort.call(this,req);
    });
    return req;
};

Ext.Ajax.directRequest = function(o) {
    if(!o.allowBatch) {
        return Ext.data.Connection.prototype.request.call(this,o);
    } else {
        return Ext.data.Connection.prototype.request.call(this,o);
    }
};

Ext.Ajax.dispatchRequest = function(o) {
   
    if(!o.url)
        o.url = AppKit.c.path+'/modules/appkit/dispatch';
    var p = o.params;
    o.params = {};
    o.params.module = o.icingaModule;
    o.params.action = o.icingaAction;
    if(o.output_type)
        o.params.output_type = o.output_type;
    
    o.params.params = Ext.encode(p);
    return Ext.data.Connection.prototype.request.call(this,o);
};
