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



Ext.ns('Icinga.Cronks.Tackle.Command').BatchCommandWindow = Ext.extend(Ext.Window,{

    layout: 'border',
    
    constructor: function(cfg) {
        cfg = cfg || {};

        cfg.width = Ext.getBody().getWidth()*0.7;
        cfg.height = Ext.getBody().getHeight()*0.9;
        this.setInitialValues();
        this.id = Ext.id();
        this.buildPreviewGrid(cfg);
        this.buildView(cfg);
        cfg.buttons = this.getSubmitButtons();
        Ext.Window.prototype.constructor.call(this, cfg);
        
    },

    setInitialValues: function() {
        Ext.apply(this,{
            svcStates: {0:true,1:true,2:true,3:true, 99:true},
            hostStates: {0:true,1:true,2:true, 99:true},
            hostFilter: "",
            serviceFilter: "",
            hostgroupFilter: "",
            showAcknowledged: true,
            showDowntimes: true,
            showFlapping: false,
            showDisabledNotifications: false,
            showPassiveOnly: false,
            showDisabled: false
        });
    },

    buildPreviewGrid: function(cfg) {
        var scope = this;
        this.recipientStore = new Icinga.Api.RESTStore({
            target: 'host',
            limit: 30,
            offset: 0,
            countColumn: true,
            columns: [
                'INSTANCE_NAME',
                'HOST_ID',
                'HOST_NAME',
                'SERVICE_NAME',
                'HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED',
                'SERVICE_PROBLEM_HAS_BEEN_ACKNOWLEDGED',
                'HOST_SCHEDULED_DOWNTIME_DEPTH',
                'SERVICE_SCHEDULED_DOWNTIME_DEPTH',
                'HOST_ACTIVE_CHECKS_ENABLED',
                'SERVICE_ACTIVE_CHECKS_ENABLED',
                'HOST_PASSIVE_CHECKS_ENABLED',
                'SERVICE_PASSIVE_CHECKS_ENABLED',
                'HOST_NOTIFICATIONS_ENABLED',
                'SERVICE_NOTIFICATIONS_ENABLED',
                'HOST_IS_FLAPPING',
                'SERVICE_IS_FLAPPING',
                'HOST_CURRENT_STATE',
                'SERVICE_CURRENT_STATE'
            ]
           
        });
        this.gridBbar = new Ext.PagingToolbar({
            store: this.recipientStore,
            pageSize:30,
            displayInfo:true
        });
        this.previewGrid = new Ext.grid.GridPanel({
            title: 'Batch target',
           
            cm: new Ext.grid.ColumnModel({
                defaults: {
                   menuDisabled: true
                },
                columns:[{
                    dataIndex: 'SERVICE_NAME',
                    header: _('Service name'),
                    hidden: true
                },{
                    dataIndex: 'HOST_NAME',
                    header: _('Host name')
                },{
                    dataIndex: 'HOST_CURRENT_STATE',
                    header: ('Host status'),
                    renderer: Cronk.grid.ColumnRenderer.hostStatus()
                },{
                    dataIndex: 'SERVICE_CURRENT_STATE',
                    header: ('Service status'),
                    renderer: Cronk.grid.ColumnRenderer.serviceStatus(),
                    hidden:true
                }, {
                    dataIndex: 'HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED',
                    header: _('Host flags'),
                    renderer: Icinga.Cronks.Tackle.Renderer.FlagIconColumnRenderer('host')
                },{
                    dataIndex: 'HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED',
                    header: _('Service flags'),
                    renderer: Icinga.Cronks.Tackle.Renderer.FlagIconColumnRenderer('service'),
                    hidden: true
                }]

            }),
            bbar: this.gridBbar,
            store: this.recipientStore
        });
    },
    
    buildView: function(cfg) {
        


        var svcCommands = new Icinga.Cronks.Tackle.Command.Panel({
            type: 'service',
            title: 'Service commands',
            isStandaloneComponent: false,
            listeners: {
                render: function(cmp) {
                    cmp.dataview.on("click",function() {this.submitBtn.setDisabled(false);},this);
                },
                scope:this
            }
        });
        var hostCommands = new Icinga.Cronks.Tackle.Command.Panel({
            type: 'host',
            title: 'Host commands',
            isStandaloneComponent: false,
            listeners: {
                render: function(cmp) {
                    cmp.dataview.on("click",function() {this.submitBtn.setDisabled(false);},this);
                },
                scope:this
            }
        });

        cfg.items = [new Ext.TabPanel({
            region: 'south',
            activeTab: 0,
            items: [
                hostCommands,
                svcCommands
            ],
            listeners: {
                tabChange: function(panel,tab) {
                    if(tab === svcCommands) {
                        this.previewGrid.getColumnModel().setHidden(0,false);
                        this.previewGrid.getColumnModel().setHidden(3,false);
                        this.previewGrid.getColumnModel().setHidden(5,false);
                        this.recipientStore.setTarget("service");
                        this.type = 'service';
                    } else {
                        this.previewGrid.getColumnModel().setHidden(0,true);
                        this.previewGrid.getColumnModel().setHidden(3,true);
                        this.previewGrid.getColumnModel().setHidden(5,true);
                        this.recipientStore.setTarget("host");
                        this.type = 'host';
                    }
                    this.previewGrid.getStore().load();
                },
                scope:this
            },
            height: 300
        }),{
            title: _('Filter command recipients'),
            region: 'west',
            layout: 'fit',
            width: 400,
            items: {
                layout: 'form',
                padding: 5,
                items: this.getFilterForm()
            }
        },{
            region: 'center',
            layout: 'fit',
            items: this.previewGrid
        }];
        
    },
    toggleServiceState: function(state,val) {
        this.svcStates[state] = val;
        this.updateFilter();
        this.previewGrid.getStore().load();
    },
    toggleHostState: function(state,val) {
        this.hostStates[state] = val;
        this.updateFilter();
        this.previewGrid.getStore().load();
    },
    setHostFilter: function(txt) {
        this.hostFilter = txt.replace("*","%");
        this.updateFilter();
        this.previewGrid.getStore().load();
    },
    setServiceFilter: function(txt) {
        this.serviceFilter = txt.replace("*","%");
        this.updateFilter();
        this.previewGrid.getStore().load();
    },
    setHostgroupFilter: function(txt) {
        this.hostgroupFilter = txt.replace("*","%");
        this.updateFilter();
        this.updateFilter();
        this.previewGrid.getStore().load();
    },
    toggleDowntime: function(bool) {
        this.showDowntime = bool;
        this.updateFilter();
        this.previewGrid.getStore().load();
    },
    toggleAck : function(bool) {
        this.showAcknowledged = bool;
        this.updateFilter();
        this.previewGrid.getStore().load();
    },
    toggleNotification : function(bool) {
        this.showDisabledNotifications = bool;
        this.updateFilter();
        this.previewGrid.getStore().load();
    },
    toggleFlapping : function(bool) {
        this.showFlapping = bool;
        this.updateFilter();
        this.previewGrid.getStore().load();
    },
    togglePassive : function(bool) {
        this.showPassive = bool;
        this.updateFilter();
        this.previewGrid.getStore().load();
    },
    toggleDisabled : function(bool) {
        this.showDisabled = bool;
        this.updateFilter();
        this.previewGrid.getStore().load();
    },
    updateFilter: function() {
        var filter = [];
        for(var i in this.svcStates) {
            if(this.svcStates[i] === true) {
                continue;
            }
            filter.push({
                type: 'atom',
                field: ['SERVICE_CURRENT_STATE'],
                method: ['!='],
                value: [i]
            });
        }
        
        for(var i in this.hostStates) {
            if(this.hostStates[i] === true) {
                continue;
            }
            filter.push({
                type: 'atom',
                field: ['HOST_CURRENT_STATE'],
                method: ['!='],
                value: [i]
            });
        }
        if(this.hostFilter !== "") {
            filter.push({
                type: 'atom',
                field: ['HOST_NAME'],
                method: ['LIKE'],
                value: [this.hostFilter]
            });
        }
        if(this.hostgroupFilter !== "") {
            filter.push({
                type: 'atom',
                field: ['HOSTGROUP_NAME'],
                method: ['LIKE'],
                value: [this.hostgroupFilter]
            });
        }
        if(this.serviceFilter !== "") {
            filter.push({
                type: 'atom',
                field: ['SERVICE_NAME'],
                method: ['LIKE'],
                value: [this.serviceFilter]
            });
        }
        var t = this.type;
        var flags = {
            showDowntime : [t+'_SCHEDULED_DOWNTIME_DEPTH',0,false],
            showAcknowledged : [t+'_PROBLEM_HAS_BEEN_ACKNOWLEDGED',0,false],
            showDisabledNotifications: [t+'_NOTIFICATIONS_ENABLED',0,true],
            showFlapping: [t+'_IS_FLAPPING',1,true]
        
        };

        for(var i in flags) {
            if(this[i] === flags[i][2]) {
                filter.push({
                    type: 'atom',
                    field: [flags[i][0]],
                    method: ['='],
                    value: [flags[i][1]]
                });
            }
        }
        
        if(this.showPassive === true) {
            filter.push({
                type:'AND',
                field: [{
                    type: 'atom',
                    field: [t+"_PASSIVE_CHECKS_ENABLED"],
                    method: ['='],
                    value: [1]
                },{
                    type: 'atom',
                    field: [t+"_ACTIVE_CHECKS_ENABLED"],
                    method: ['='],
                    value: [0]
                }]
            });
        }
       if(this.showDisabled === true) {
            filter.push({
                type:'AND',
                field: [{
                    type: 'atom',
                    field: [t+"_PASSIVE_CHECKS_ENABLED"],
                    method: ['='],
                    value: [0]
                },{
                    type: 'atom',
                    field: [t+"_ACTIVE_CHECKS_ENABLED"],
                    method: ['='],
                    value: [0]
                }]
            });
        }
        this.recipientStore.setFilter({
            type: 'AND',
            field: filter
        });
        
    },

    getFilterForm: function() {
        return [{
            fieldLabel: 'Host status',
            xtype: 'buttongroup',
            defaults: {
                enableToggle:true,
                xtype: 'button',
                width: 25,
                listeners: {
                    toggle: function(cmp,enabled) {
                        this.toggleHostState(cmp.state,enabled);
                    },
                    scope: this
                }

            },

            items: [{
                pressed: true,
                //text: _('Up'),
                ctCls: 'tackle_qbtn state_up',
                state: '0'
            },{
                pressed: true,
                //text: _('Down'),
                ctCls: 'tackle_qbtn state_down',
                state: '1'
            },{
                pressed: true,
                //text: _('Unreachable'),
                ctCls: 'tackle_qbtn state_unreachable',
                state: '2'
            },{
                pressed: true,
                //text: _('Pending'),
                ctCls: 'tackle_qbtn state_pending',
                state: '99'
            }]
        }, {
            fieldLabel: 'Service status',
            xtype: 'buttongroup',
            defaults: {
                enableToggle:true,
                xtype: 'button',
                width: 25,
                listeners: {
                    toggle: function(cmp,enabled) {
                        this.toggleServiceState(cmp.state,enabled);
                    },
                    scope: this
                }
            },
            items: [{
                pressed: true,
                //text: _('Ok'),
                ctCls: 'tackle_qbtn state_up',
                state: '0'
            },{
                pressed: true,
                //text: _('Warning'),
                ctCls: 'tackle_qbtn state_warning',
                state: '1'
            },{
                pressed: true,
                //text: _('Critical'),
                ctCls: 'tackle_qbtn state_down',
                state: '2'
            },{
                pressed: true,
                //text: _('Unknown'),
                ctCls: 'tackle_qbtn state_unreachable',
                state: '3'
            },{
                pressed: true,
                //text: _('Pending'),
                ctCls: 'tackle_qbtn state_pending',
                state: '99'
            }]
        },{
            fieldLabel: 'Hostfilter',
            xtype: 'container',
            items: {
                xtype: 'IcingaHostComboBox',
                defaultValue: '*',
                value: '*',
                listeners: {
                    change: function(cmp,value) {
                        this.setHostFilter(value);
                    },
                    scope: this
                }

            }
        },{
            fieldLabel: 'Servicefilter',
            xtype: 'container',
            items: {
                xtype: 'IcingaServiceComboBox',
                defaultValue: '*',
                value: '*',
                listeners: {
                    change: function(cmp,value) {
                        this.setServiceFilter(value);
                    },
                    scope: this
                }
            }

        },{
            fieldLabel: 'Hostgroupfilter',
            xtype: 'container',
            items: {
                xtype: 'IcingaHostgroupComboBox',
                fieldLabel: 'Hostgroup',
                defaultValue: '*',
                value: '*',
                listeners: {
                    change: function(cmp,value) {
                        this.setHostgroupFilter(value);
                    },
                    scope: this
                }
            }
        },{
            fieldLabel: _('Also show:'),
            xtype: 'buttongroup',
            defaults: {
                enableToggle: true,
                width:25,
                pressed:true
            },

            items: [{
                iconCls: 'icinga-icon-info-problem-acknowledged',
                tooltip: _('Include acknowledged items'),
                listeners: {
                    toggle: function(cmp,val) {
                        this.toggleAck(val);
                    },scope:this
                }
            },{
                iconCls: 'icinga-icon-info-downtime',
                tooltip: _('Include items in downtime'),
                listeners: {
                    toggle: function(cmp,val) {
                        this.toggleDowntime(val);
                    },scope:this
                }
            }]
        },{
            fieldLabel: _('<b/>Only</b> show:'),
            xtype: 'buttongroup',
            defaults: {
                enableToggle: true,
                width: 25,
                pressed: false
            },
            items: [{
                iconCls: 'icinga-icon-info-notifications-disabled',
                tooltip: _('Include items with disabled notifications'),
                listeners: {
                    toggle: function(cmp,val) {
                        this.toggleNotification(val);
                    },scope:this
                }
            },{
                iconCls: 'icinga-icon-info-flapping',
                tooltip: _('Include flapping objects'),
                listeners: {
                    toggle: function(cmp,val) {
                        this.toggleFlapping(val);
                    },scope:this
                }
            },{
                iconCls: 'icinga-icon-info-passive',
                tooltip: _('Include passive items'),
                listeners: {
                    toggle: function(cmp,val) {
                        this.togglePassive(val);
                    },scope:this
                }
            },{
                iconCls: 'icinga-icon-info-disabled',
                tooltip: _('Include disabled items'),
                listeners: {
                    toggle: function(cmp,val) {
                        this.toggleDisabled(val);
                    },scope:this
                }
            }]
        }];
    },
    submitCommand: function() {
        var submitStore = new Icinga.Api.RESTStore({
            target: this.type,
            columns: ['HOST_NAME','INSTANCE_NAME',this.type.toUpperCase()+'_NAME']
        });

        submitStore.setFilter(this.recipientStore.getFilter());
        submitStore.on("load", function(store,records) {
            var targets = [];
            Ext.each(records,function(record) {
                targets.push({
                    host : record.get('HOST_NAME'),
                    service : record.get('SERVICE_NAME'),
                    instance: record.get('INSTANCE_NAME')
                });
            },this);
            this.findByType('tabpanel')[0].getActiveTab().submit(targets);

        },this,{single:true});
        submitStore.load();


    },
    getSubmitButtons: function() {

       this.submitBtn = new Ext.Button({
            text: _('Send commands'),
            iconCls: 'icinga-icon-accept',
            disabled:true,
            handler: function(cmp) {
                var count = this.recipientStore.getTotalCount();
                Ext.Msg.confirm(
                    _("Submitting commands to multiple targets"),
                    _("This command will be send to "+count+" "+this.type+"s, proceed?"),
                    function(btn) {
                        if(btn !== "yes") {
                            return false;
                        }
                        this.submitCommand();
                    },
                    this
                );
            },
            scope:this
        });
        return [this.submitBtn,{
            text: _('Cancel'),
            iconCls: 'icinga-icon-cancel',
            handler: function(cmp) {
                cmp.ownerCt.ownerCt.close();
            },
            scope:this
        }];

    }



});