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

/* 
 * Little widget that displays the currents servertime
 * 
 */
Ext.ns('AppKit.util');

AppKit.util.Servertime = Ext.extend(Ext.menu.BaseItem, {
    TIME_URL: '/modules/appkit/servertime',
    updateClock: function () {
        this.getEl().getUpdater().showLoadIndicator = false;
       
        this.getEl().load({
            url:AppKit.c.path+this.TIME_URL
        });
    },
    constructor: function(cfg) {
        cfg = cfg || {};
        Ext.menu.BaseItem.prototype.constructor.call(this,cfg);
        
   },
   
   initComponent : function() {
        AppKit.util.Servertime.superclass.initComponent.call(this);
        
        this.on('afterrender', function() {
            if (this.getEl()) {
                AppKit.getTr().start({
                    run: this.updateClock,
                    interval: 60000,
                    scope:this
                });
            }
        }, this, { single : true  });
   },
   
   // private
   onRender : function(container, position){
        Ext.menu.BaseItem.superclass.onRender.apply(this, arguments);
    }
    
});

