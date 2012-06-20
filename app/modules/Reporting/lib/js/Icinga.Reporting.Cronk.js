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

Ext.ns('Icinga.Reporting');

Icinga.Reporting.Cronk = Ext.extend(Ext.TabPanel, {
    
    border : false,
    
    defaults : {
        border: false
    },
    
    constructor : function(c) {
        Icinga.Reporting.Cronk.superclass.constructor.call(this, c);
    },
    
    initComponent : function() {
        Icinga.Reporting.Cronk.superclass.initComponent.call(this);
        
        this.on('added', function() {
            this.activate(0);
        }, this, { single : true });
        
        if (this.enable_onthefly == true) {
            
            this.appOnTheFly = new Icinga.Reporting.util.OnTheFly({
                treeloader_url: this.treeloader_url,
                parampanel_url: this.parampanel_url,
                creator_url : this.creator_url,
                parentCmp : this
            });
            
            this.add({
                title : _('On-the-fly'),
                tabTip : _('Go here to create reports on the fly'),
                items : this.appOnTheFly,
                iconCls : 'icinga-icon-report-run'
                
            });
        }
        
        if (this.enable_repository == true) {
            
            this.appRepository = new Icinga.Reporting.util.Repository({
                treeloader_url : this.treeloader_url,
                resource_url : this.resource_url,
                parentCmp : this
            });
            
            this.add({
                title: _('Repository'),
                tabTip: _('Explore the server-repository'),
                items : this.appRepository,
                iconCls : 'icinga-icon-bricks'
            });
        }
        
        /**
         * @Todo: Implement this (80%)
         */
        if (this.enable_scheduling == true) {
            
            this.enableScheduling = new Icinga.Reporting.util.ReportScheduling({
                treeloader_url : this.treeloader_url,
                scheduler_list_url : this.scheduler_list_url,
                scheduler_get_url : this.scheduler_get_url,
                scheduler_edit_url : this.scheduler_edit_url,
                scheduler_delete_url : this.scheduler_delete_url,
                parentCmp : this
            });
            
            this.add({
                title: _('Scheduling'),
                tabTip: _('Report scheduling'),
                items : this.enableScheduling,
                iconCls : 'icinga-icon-alarm-clock'
            });
        }
    }
});
