/*global Ext: false, Icinga: false, _: false */
Ext.ns('Icinga.Cronks.Tackle.Filter');

Icinga.Cronks.Tackle.Filter.TackleMainFilterTbar = Ext.extend(Ext.Toolbar, {
    autoRefreshEnabled: true,
    autoRefreshInterval: 30000,

    constructor: function(config) {
        "use strict";
        this.parentId = config.id;
        this.store = config.store;
        this.autoRefreshInterval = AppKit.getPrefVal('org.icinga.grid.refreshTime')*100 || 30000
		var filterUpdateTask = new Ext.util.DelayedTask(this.updateFilterImpl, this);
        this.updateFilter = function(t) {
            filterUpdateTask.delay(200,null);
        };
        Ext.Toolbar.prototype.constructor.call(this,{
            items: this.createTbar(config),
            autoDestroy: true
        });
        
        this.on("destroy", this.stopAutoRefresh.createCallback(true), this);
        this.on("show", this.startAutoRefresh, this);
    },

    startAutoRefresh: function() {
        if(this.arTask)
            return;
       this.autoRefreshEnabled = true;
       this.arTask = new Ext.util.TaskRunner();
       this.arTask.start({
            run: this.updateFilterImpl,
            interval: this.autoRefreshInterval,
            scope:this
        });
    },
    stopAutoRefresh: function(onDestroy) {
        // prevents restart of the autorefresh when view is destroyed before the
        // initial delay restart delay has passed
        if(!this.arTask)
            return;
        this.autoRefreshEnabled = false;
        this.arTask.stopAll()
        if(onDestroy)
            this.arTask = true;
        else
            delete(this.arTask);

    },
    updateFilterImpl: function() {
       try {
           if(!this.isVisible())
                return;

            var jsonFilter = this.buildFilter();
            this.store.setFilter(jsonFilter);

            this.ownerCt.bottomToolbar.doLoad();
            if(this.autoRefreshEnabled && !this.arTask) {
                this.startAutoRefresh.defer(this.autoRefreshInterval,this);
            }
       } catch(e) {
           this.stopAutoRefresh(); // prevent error madness 
       }
    },
    getSVCFilter : function() {
        if(!Ext.getCmp('filterbuttons_filter_svc_'+this.parentId).pressed)
            return false;
        return this.buildFilter()
    },

    buildFilter: function() {
        if(!Ext.getCmp('filterbuttons_host_state_up_'+this.parentId))
            return null;
        var filter = {
            states: {
                0 : Ext.getCmp('filterbuttons_host_state_up_'+this.parentId).pressed,
                1 : Ext.getCmp('filterbuttons_host_state_down_'+this.parentId).pressed,
                2 : Ext.getCmp('filterbuttons_host_state_unreachable_'+this.parentId).pressed,
                99: Ext.getCmp('filterbuttons_host_state_pending_'+this.parentId).pressed
            },

            ack : Ext.getCmp('filterbuttons_host_ack_'+this.parentId).pressed,
            dtime : Ext.getCmp('filterbuttons_host_downtime_'+this.parentId).pressed,
            text : Ext.getCmp('filtertxt_search_'+this.parentId).getValue()
        };
        var jsonFilter = {
            type: 'AND',
            field: []
        };
        for(var i in filter.states) {
            var state = filter.states[i];
            if(state)
                continue;
            jsonFilter.field.push({
                type: 'atom',
                field: ["HOST_CURRENT_STATE"],
                method: ["!="],
                value: [i]
            });
        }

        if(!filter.ack) {
            jsonFilter.field.push({
                type: 'atom',
                field: ["HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED"],
                method: ["="],
                value: ["0"]
            });
        }
        if(!filter.dtime) {
            jsonFilter.field.push({
                type: 'atom',
                field: ["HOST_SCHEDULED_DOWNTIME_DEPTH"],
                method: ["="],
                value: ["0"]
            });
        }
        if(filter.text) {
           jsonFilter.field.push({
               type: 'OR',
               field: [{
                   type: 'atom',
                   field: ['SERVICE_DISPLAY_NAME'],
                   method: ["LIKE"],
                   value: [filter.text]
               },{
                   type: 'atom',
                   field: ['HOSTGROUP_NAME'],
                   method: ["LIKE"],
                   value: [filter.text]
               },{
                   type: 'atom',
                   field: ['HOST_ALIAS'],
                   method: ["LIKE"],
                   value: [filter.text]
               },{
                   type: 'atom',
                   field: ['HOST_DISPLAY_NAME'],
                   method: ["LIKE"],
                   value: [filter.text]
               },{
                   type: 'atom',
                   field: ['HOST_NAME'],
                   method: ["LIKE"],
                   value: [filter.text]
               }]
           });
        }
       
        return jsonFilter;
    },

    createTbar : function(config) {
        var id = config.id;
        return [{
            xtype: 'button',
            text: _('Refresh'),
            iconCls: 'icinga-icon-arrow-refresh',
            handler: function() {
                this.ownerCt.bottomToolbar.doLoad();
            },
            scope: this
        },{
            xtype: 'button',
            iconCls: 'icinga-icon-application-edit',
            text: _('Settings'),
            menu: [{
                text: _('Autorefresh'),
                xtype: 'menucheckitem',
                checked: this.autoRefreshEnabled,
                handler: function(state) {
                    this.stopAutoRefresh();
                },
                scope:this
            }]
        },{
            xtype: 'tbspacer',
            width: 20
        },{
            xtype: 'buttongroup',
            defaults: {
                enableToggle: true,
                width:25,
                bubbleEvents: ['toggle']
            },
            events: ['toggle'],

            listeners: {
                toggle: function() {
                    this.updateFilter();
                },
                scope:this
            },

            items: [{
                iconCls: 'icinga-icon-info-problem-acknowledged',
                id: 'filterbuttons_host_ack_'+id,
                tooltip: _('Show acknowledged items')
            },{
                iconCls: 'icinga-icon-info-downtime',
                id: 'filterbuttons_host_downtime_'+id,
                tooltip: _('Show items in downtime')
            }]
        },{
            xtype: 'tbspacer',
            width: 20
        },{
            xtype: 'button',
            iconCls: 'icinga-icon-bricks',
            text: _('Batch commands'),
            handler: function() {
                (new Icinga.Cronks.Tackle.Command.BatchCommandWindow()).show(document.body);
            },
            hidden: config.noBatch
        },{
            xtype:'tbspacer',
            width: 20
        },{
            xtype: 'button',
            iconCls: 'icinga-icon-exclamation-red',
            tooltip: _('Only show open problems'),

            handler: function() {
                var toDisable = ['host_state_up','host_state_pending','host_downtime','host_ack','host_downtime'];
                Ext.iterate(toDisable,function(i) {
                    var el = Ext.getCmp("filterbuttons_"+i+"_"+id);
                    if(!el) {
                        AppKit.log('Tried to disable unknown button '+i+"_"+id);
                        return true;
                    }
                    el.toggle(false);
                    return true;
                });

            }
        },{
            xtype: 'buttongroup',
            id: 'filterbuttons_'+id,
            events:['toggle'],

            listeners: {
                toggle: function() {
                    this.updateFilter();
                },
                scope:this
            },

            defaults: {
                enableToggle: true,
                width:60,
                bubbleEvents: ['toggle']
            },
            items: [{
                ctCls: 'tackle_qbtn state_up',
                id: 'filterbuttons_host_state_up_'+id,
                pressed: true,
                text: _('Up')
            },{
                ctCls: 'tackle_qbtn state_down',
                id: 'filterbuttons_host_state_down_'+id,
                pressed: true,
                text: _('Down')
            },{
                ctCls: 'tackle_qbtn state_unreachable',
                id: 'filterbuttons_host_state_unreachable_'+id,
                pressed: true,
                tooltip: _('Unreachable'),
                text: _('Unreach.')
            },{
                ctCls: 'tackle_qbtn state_pending',
                id: 'filterbuttons_host_state_pending_'+id,
                pressed: true,
                text: 'Pending'
            }]
        },{
            xtype: 'textfield',
            emptyText: 'Type to search',
            id: 'filtertxt_search_'+id,
            listeners: {
                focus: function() {
                    Ext.getCmp('filterbuttons_clear_filter_'+id).setDisabled(false);
                },
                change: function(btn,v) {
                    if(v !== "")
                        Ext.getCmp('filterbuttons_clear_filter_'+id).setDisabled(false);
                    else
                        Ext.getCmp('filterbuttons_clear_filter_'+id).setDisabled(true);
                    this.updateFilter();
                },
                scope:this
            }
        }, {
            xtype: 'button',
            iconCls: 'icinga-icon-cancel',
            id: 'filterbuttons_clear_filter_'+id,
            handler: function(btn) {
                Ext.getCmp('filtertxt_search_'+id).reset();
                btn.setDisabled(true);
            },

            style: 'position:relative;margin-left:-25px',
            disabled:true
        },{
            xtype: 'button',
            iconCls: 'icinga-icon-service',
            id: 'filterbuttons_filter_svc_'+id,
            tooltip: 'Filter service results, too',
            enableToggle: true,
            listeners: {
                toggle: function(btn) {
                    this.updateFilter();
                },
               scope:this
            }
        }];
    }
});
