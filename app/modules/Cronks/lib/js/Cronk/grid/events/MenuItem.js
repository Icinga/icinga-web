// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-present Icinga Developer Team.
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

Ext.ns("Cronk.grid.events");

(function () {
    
    "use strict";
    
    /**
     * Class to use as iconized button that can handle dom events and used
     * in the JsonActionPanel
     * 
     * @class
     * @augments Ext.Container
     * @augments Cronk.grid.events.EventMixin
     */
    Cronk.grid.events.MenuItem = Ext.extend(Ext.menu.Item, {
        constructor: function(config) {
            Cronk.grid.events.MenuItem.superclass.constructor.call(this, config);
        },
        
        initComponent: function() {
            Cronk.grid.events.MenuItem.superclass.initComponent.call(this);
            
            this.initEventMixin(function() {
                return this;
            });
        }
    });
    
    // Applying mixin for the event system
    Ext.override(Cronk.grid.events.MenuItem, Cronk.grid.events.EventMixin);
    
    // Register as xtype
    Ext.reg("grideventmenuitem", Cronk.grid.events.MenuItem);
    
})();