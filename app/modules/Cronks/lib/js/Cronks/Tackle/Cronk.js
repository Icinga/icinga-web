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

/*global Ext: false, Icinga: false, _: false */
Ext.ns('Icinga.Cronks.Tackle');

(function () {
    "use strict";

    Icinga.Cronks.Tackle.Cronk = Ext.extend(Ext.Panel, {

        constructor: function (config) {

            config = Ext.apply(config || {}, {
                layout: 'border',
                border: false,
                defaults: {
                    border: false
                }
            });

            Icinga.Cronks.Tackle.Cronk.superclass.constructor.call(this, config);
        },

        initComponent: function () {
            Icinga.Cronks.Tackle.Cronk.superclass.initComponent.call(this);
            
            var type = 'host';
            this.tabItems = {
                host: {},
                service: {}
            };
            this.objectGrid = new Icinga.Cronks.Tackle.ObjectGrid({
                region: 'center'
            });
            

            this.infoTabs = new Icinga.Cronks.Tackle.InfoTabPanel();
            
            for(var i in this.tabItems) {
                this.tabItems[i].head = new Icinga.Cronks.Tackle.Information.Head({
                    type: i
                    
                });
                this.tabItems[i].comments = new Icinga.Cronks.Tackle.Comment.Panel({
                    type: i
                });
                this.tabItems[i].commands = new Icinga.Cronks.Tackle.Command.Panel({
                    type: i
                });
                
                this.tabItems[i].relation = new Icinga.Cronks.Tackle.Relation.Head({
                	type : i
                });
                this.tabItems[i].sla = new Icinga.Cronks.Tackle.SLAChartPanel({
                	type : i
                });
                // add all items and hide service items
                for(var x in this.tabItems[i]) {
                    this.infoTabs.add(this.tabItems[i][x]);
                   
                }
            }
            
            this.collapsibleFrame = new Ext.Panel({
                layout: 'fit',
                iconCls: 'icinga-icon-universal',
                region: 'south',
                height: 300,
                minSize: 300,
                maxSize: 600,
                collapsible: true,
                split: true,
                border: false,
                items: this.infoTabs
            });

            this.add([this.collapsibleFrame, this.objectGrid]);
            this.initInternalEvents();
            this.infoTabs.on("afterrender",function() {
                this.toggleTabView('host');
            },this,{single: true});
        },

        toggleTabView: function(type) {
            var show = type;
            var hide = (type === "host") ? "service" : "host"

            for(var i in this.tabItems[show]) {
                this.infoTabs.unhideTabStripItem(this.tabItems[show][i]);
            }
            for(var i in this.tabItems[hide]) {
                this.infoTabs.hideTabStripItem(this.tabItems[hide][i]);
            }
            this.infoTabs.setActiveTab(this.tabItems[show].head);
            this.currentView = type;

        },

        currentView: 'none',

        initInternalEvents: function () {
            /**
             * TODO: these objects should just need the record and be able to deal with it
             */
            this.objectGrid.on("hostSelected", function(record) {
                if(this.currentView != 'host') {
                    this.toggleTabView('host');
                }
                this.tabItems.host.head.loadDataForObjectId(record.data.HOST_OBJECT_ID);
                this.tabItems.host.relation.loadDataForObjectId(record.data.HOST_OBJECT_ID);
                this.tabItems.host.sla.updateRecord(record);
                this.tabItems.host.comments.grid.recordUpdated(record);

                this.tabItems.host.comments.form.setObjectData({
                    objectName : record.data.HOST_NAME,
                    objectId : record.data.HOST_ID,
                    record: record,
                    objectInstance : record.data.INSTANCE_NAME
                });
                
                this.tabItems.host.commands.form.setRecord(record);
                
                if (this.collapsibleFrame.collapsed === true) {
                    this.collapsibleFrame.expand(true);
                }
            },this);

            this.objectGrid.on("serviceSelected", function(record) {
                if(this.currentView != 'service') {
                    this.toggleTabView('service');
                }
                if(!record.data)
                    return;
             
                this.tabItems.service.head.loadDataForObjectId(record.data.SERVICE_OBJECT_ID);
                this.tabItems.service.relation.loadDataForObjectId(record.data.SERVICE_OBJECT_ID);
                this.tabItems.service.comments.grid.recordUpdated(record);
                this.tabItems.service.sla.updateRecord(record);

                this.tabItems.service.comments.form.setObjectData({
                    objectName : record.data.SERVICE_NAME,
                    objectId : record.data.SERVICE_ID,
                    record: record,
                    objectInstance : record.data.INSTANCE_NAME
                });
                
                this.tabItems.service.commands.form.setRecord(record);
                
                if (this.collapsibleFrame.collapsed === true) {
                    this.collapsibleFrame.expand(true);
                }
                this.currentView = 'service';
            },this);

        }
    });

    Ext.reg('cronks-tackle-cronk', Icinga.Cronks.Tackle.Cronk);

})();
