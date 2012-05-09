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

/*global Ext: false, Icinga: false, _: false, AppKit: false */
Ext.ns('AppKit.util');

(function() {
    "use strict";
    
    /**
     * ExtJS version of true HTTP logout (Sorry but this also does not work on every browser ;-))
     * 
     * Thanks to http://trac-hacks.org/wiki/TrueHttpLogoutPatch for the idea
     */
    AppKit.util.LogoutMachine = Ext.extend(Object, {
        
        auth_realm: null,
        doRedirect: false,
        httpBasic: false,
        sendHeader: false,
        url: null,
        current: null,
        
        constructor: function(c) {
            Ext.apply(this, c);
            AppKit.util.LogoutMachine.superclass.constructor.call(this);
        },
        
        doLogout: function() {
            if (this.httpBasic) {
                if (Ext.isIE) {
                    this.tryMSIELogout();
                } else {
                    this.tryHTTPHeader(this.current + '&unauthorized=1');
                }
            }
            
            if (this.doRedirect && this.url) {
                this.redirectToStart(this.url);
            }
        },
        
        tryMSIELogout: function() {
            document.execCommand("ClearAuthenticationCache");
        },
        
        tryHTTPHeader: function(url) {
            var c = new Ext.data.Connection();
            
            var request = c.request({
                url: url,
                username: '__anon_logout',
                password: '__anon_logout'
            });
            
            c.abort(c);
        },
        
        redirectToStart: function(url) {
            (function() {
                window.location = url;
            }).defer(3000);
        }
    });
    
})();