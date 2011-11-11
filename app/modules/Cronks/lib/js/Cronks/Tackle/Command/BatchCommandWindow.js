

Ext.ns('Icinga.Cronks.Tackle.Command').BatchCommandWindow = Ext.extend(Ext.Window,{

    layout: 'border',
    
    
    
    constructor: function(cfg) {
        cfg = cfg || {};
        cfg.buttons = this.buttons;
        cfg.width = Ext.getBody().getWidth()*0.7;
        cfg.height = Ext.getBody().getHeight()*0.7;
        this.setInitialValues();
        this.id = Ext.id();
        this.buildPreviewGrid(cfg);
        this.buildView(cfg);
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
            showDowntimes: true
        })
    },

    buildPreviewGrid: function(cfg) {
        this.recipientStore = new Icinga.Api.RESTStore({
            target: 'service',
            limit: 50,
            offset: 0,
            countColumn: true,
            columns: [
                'INSTANCE_NAME',
                'HOST_ID',
                'HOST_NAME',
                'SERVICE_NAME',
                'HOST_CURRENT_STATE',
                'SERVICE_CURRENT_STATE'
            ],
            listeners : {
                beforeload: function() {

                    this.updateFilter();
                    return true;
                },
                scope:this
            }
        });
        this.gridBbar = new Ext.PagingToolbar({
            store: this.recipientStore,
            displayInfo:true
        });
        this.previewGrid = new Ext.grid.GridPanel({
            title: 'Batch target',
           
            cm: new Ext.grid.ColumnModel({
                columns:[{
                    dataIndex: 'SERVICE_NAME',
                    header: _('Service name'),
                    menuDisabled: true,
                    hidden: true
                },{
                    dataIndex: 'HOST_NAME',
                    menuDisabled: true,
                    header: _('Host name')
                },{
                    dataIndex: 'HOST_CURRENT_STATE',
                    header: ('Host status'),
                    menuDisabled: true,
                    renderer: Cronk.grid.ColumnRenderer.hostStatus()
                },{
                    dataIndex: 'SERVICE_CURRENT_STATE',
                    header: ('Service status'),
                    renderer: Cronk.grid.ColumnRenderer.serviceStatus(),
                    menuDisabled: true,
                    hidden:true
                }]
            }),
            bbar: this.gridBbar,
            store: this.recipientStore
        });
    },
    
    buildView: function(cfg) {
        


        var svcCommands = new Icinga.Cronks.Tackle.Command.Panel({
            type: 'service',
            title: 'Service commands'
        });
        var hostCommands = new Icinga.Cronks.Tackle.Command.Panel({
            type: 'host',
            title: 'Host commands'
        });

        cfg.items = [new Ext.TabPanel({
            region: 'north',
            activeTab: 0,
            items: [
                hostCommands,
                svcCommands
            ],
            listeners: {
                tabChange: function(panel,tab) {
                    if(tab == svcCommands) {
                        this.previewGrid.getColumnModel().setHidden(0,false);
                        this.previewGrid.getColumnModel().setHidden(3,false);
                        this.recipientStore.setGroupBy(null);
                    } else {
                        this.previewGrid.getColumnModel().setHidden(0,true);
                        this.previewGrid.getColumnModel().setHidden(3,true);
                        this.recipientStore.setGroupBy("HOST_NAME");
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
        this.previewGrid.getStore().load();
    },
    toggleHostState: function(state,val) {
        this.hostStates[state] = val;
        this.previewGrid.getStore().load();
    },
    setHostFilter: function(txt) {
        this.hostFilter = txt.replace("*","%");
        this.previewGrid.getStore().load();
    },
    setServiceFilter: function(txt) {
        this.serviceFilter = txt.replace("*","%");
        this.previewGrid.getStore().load();
    },
    setHostgroupFilter: function(txt) {
        this.hostgroupFilter = txt.replace("*","%");
        this.previewGrid.getStore().load();
    },
    toggleDowntime: function(bool) {
        this.showDowntime = bool;
        this.previewGrid.getStore().load();
    },
    toggleAck : function(bool) {
        this.showAcknowledged = bool;
        this.previewGrid.getStore().load();
    },


    updateFilter: function() {
        var filter = [];
        for(var i in this.svcStates) {
            if(this.svcStates[i] == true)
                continue;
            filter.push({
                type: 'atom',
                field: ['SERVICE_CURRENT_STATE'],
                method: ['!='],
                value: [i]
            });
        };
        
        for(var i in this.hostStates) {
            if(this.hostStates[i] == true)
                continue;
            filter.push({
                type: 'atom',
                field: ['HOST_CURRENT_STATE'],
                method: ['!='],
                value: [i]
            });
        };
        if(this.hostFilter != "") {
            filter.push({
                type: 'atom',
                field: ['HOST_NAME'],
                method: ['LIKE'],
                value: [this.hostFilter]
            });
        }
        if(this.hostgroupFilter != "") {
            filter.push({
                type: 'atom',
                field: ['HOSTGROUP_NAME'],
                method: ['LIKE'],
                value: [this.hostgroupFilter]
            });
        }
        if(this.serviceFilter != "") {
            filter.push({
                type: 'atom',
                field: ['SERVICE_NAME'],
                method: ['LIKE'],
                value: [this.serviceFilter]
            });
        }
        if(this.showDowntime === false) {
            filter.push({
                type: 'atom',
                field: ['HOST_SCHEDULED_DOWNTIME_DEPTH'],
                method: ['='],
                value: [0]
            });
        }
        if(this.showAcknowledged === false) {
            filter.push({
                type: 'atom',
                field: ['HOST_PROBLEM_IS_ACKNOWLEDGED'],
                method: ['='],
                value: [0]
            });
        }
        AppKit.log(filter);
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
            fieldLabel: 'Other flags',
            xtype: 'buttongroup',
            defaults: {
                enableToggle: true,
                width:25
            },

            items: [{
                iconCls: 'icinga-icon-info-problem-acknowledged',
                tooltip: _('Show acknowledged items'),
                listeners: {
                    toggle: function(cmp,val) {
                        this.toggleAck(val);
                    },scope:this
                }
            },{
                iconCls: 'icinga-icon-info-downtime',
                tooltip: _('Show items in downtime'),
                listeners: {
                    toggle: function(cmp,val) {
                        this.toggleDowntime(val);
                    },scope:this
                }
            }]
        }];
    },

    buttons: [{
        text: _('Send commands'),
        iconCls: 'icinga-icon-accept'
    },{
        text: _('Cancel'),
        iconCls: 'icinga-icon-cancel'
    }]



});