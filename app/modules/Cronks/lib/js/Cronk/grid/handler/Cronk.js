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
     * @static
     * Handlers for working with URLs
     */
    Cronk.grid.handler.Cronk = {
        
        /**
         * Create a cronk from configuration and add them to the tabpanel. If
         * the configuration has an id and cronk is already on tabPanel, do 
         * nothing (or activate if configured)
         * 
         * @cfg {Object} params Cronk parameters e.g. in cronks.xml
         * @cfg {String} crname Name of the cronk
         * @cfg {Boolean} autoRefresh indicator for the js programm to refresh
         * @cfg {String} iconCls Css class name of the icon icinga-cronk-icon-*
         * @cfg {String} title Title of the cronk, can be templated on row
         * @cfg {Object|String} state Initial state of the cronk 
         *  (See cronkbuilder/expert mode for details). This is the internal cronk
         *  configuration
         * @cfg {String} id Object id of the component
         * @cfg {String} parentid Compat version of id
         * @cfg {Boolean} activateOnClick Jump into the cronk if handler is clicked
         */
        open: function() {
            var tabPanel = Ext.getCmp("cronk-tabs");
            var cronk = null;
            var args = this.getHandlerArgsTemplated();
            var cronkConfig = Ext.apply({}, Ext.copyTo(args, [
                "params", "crname", "autoRefresh",
                "iconCls", "title", "state", "id", "parentid"
            ]), {
                iconCls: "icinga-cronk-icon-pointer",
                title: "Cronk"
            });
            
            cronkConfig.closable = true; // This is fix!
            
            if (args.state) {
                if (Ext.isObject(args.state)===true) {
                    args.state = Ext.encode(args.state);
                }
                
                cronkConfig.params.state = args.state;
            }
            
            if (args.parentid && !args.id) {
                args.id = args.parentid;
            }
            
            cronk = Cronk.factory(cronkConfig);

            tabPanel.add(cronk);

            tabPanel.doLayout();
            
            if (args.activateOnClick) {
                tabPanel.setActiveTab(cronk);
            }
        }
    };
    
})();
