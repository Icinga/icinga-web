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

Ext.ns("Icinga.Cronks").StatusMapServiceGrid = Ext.extend(Ext.grid.GridPanel, {
    autoDestroy: true,
    autoLoad: true,
    ctCls: 'x-tree-lines',
    stripeRows: true,
    cls: 'icinga-service-subgrid',
    events: ['serviceSelected_sub'],
    selectEV: new Ext.util.DelayedTask(),
    listeners: {
        rowClick: function(grid, idx,event) {
            grid.selectEV.delay(200,function() {
                grid.fireEvent('serviceSelected_sub',grid.getStore().getAt(idx));
            },this);
        },
        scope:this
    },

    constructor : function(config) {
        this.filter = config.filter;
        this.hostId = config.hostId;
        config.store = this.createStore();

        config.bbar = new Ext.PagingToolbar({
            store: config.store,
            displayInfo: true,
            pageSize:25
        });

        Icinga.Cronks.Tackle.ServicesSubGrid.superclass.constructor.call(this, config);
    },

    setHostId: function(id) {
        this.hostId = id;
        this.updateFilter();
    },

    setFilter: function(filter) {
        this.filter = filter;
        this.updateFilter();
    },

    updateFilter: function() {
        var jsonFilter;
        var hostFilter = {
            type: 'atom',
            method: ['='],
            field: ['HOST_ID'],
            value: [this.hostId]
        };
        if(this.filter) {
            jsonFilter = Ext.ux.util.clone (this.filter);
            jsonFilter["field"].push(hostFilter);
        } else {
            jsonFilter =  {
                type: 'AND',
                field: [hostFilter]
            };
        }
        this.store.setFilter(
            jsonFilter
        );
    },
    createStore: function() {

        this.store = new Icinga.Api.RESTStore({
            target: 'service',
            limit: 25,
            offset: 0,
            countColumn: true,
            withSLA: true,
            columns: [
                'INSTANCE_NAME',
                'SERVICE_ID',
                'HOST_NAME',
                'SERVICE_NAME',
                'SERVICE_CURRENT_PROBLEM_STATE',
                'SERVICE_CURRENT_STATE',
                'SERVICE_OBJECT_ID',
                'SERVICE_LAST_CHECK',
                'SERVICE_NEXT_CHECK',
                'SERVICE_PERFDATA',
                'SERVICE_OUTPUT',
                'SERVICE_LONG_OUTPUT',
                'SERVICE_SCHEDULED_DOWNTIME_DEPTH',
                'SERVICE_PROBLEM_HAS_BEEN_ACKNOWLEDGED',
                'SERVICE_ACTIVE_CHECKS_ENABLED',
                'SERVICE_PASSIVE_CHECKS_ENABLED',
                'SERVICE_NOTIFICATIONS_ENABLED',
                'SERVICE_IS_FLAPPING'

            ]

        });
        this.updateFilter();

        return this.store;
    },



    initComponent : function() {

        this.cm = new Ext.grid.ColumnModel({
            columns : [{
                dataIndex: 'SERVICE_CURRENT_STATE',
                renderer: Icinga.Cronks.Tackle.Renderer.StatusColumnRenderer,
                width: 25
            },{
                header: _('Service name'),
                dataIndex : 'SERVICE_NAME',
                width: 180,
                renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                    var state = parseInt(record.get("SERVICE_CURRENT_STATE"),10);

                    switch(state) {
                        case 0:
                            metaData.css = 'icinga-status-up';
                            break;
                        case 1:
                            metaData.css = 'icinga-status-critical';
                            break;
                        case 2:
                            metaData.css = 'icinga-status-critical';
                            break;
                        case 2:
                            metaData.css = 'icinga-status-unreachable';
                            break;
                        case 99:
                            metaData.css = 'icinga-status-pending';
                            break;
                    }

                    return "<span ext:qtip='"+value+"' style='"+((state == 1 || state == 99) ? 'color:#ffffff' : 'color:#000000') +"'>"+value+"</span>";
                }
            },{
                header: _('Output'),
                dataIndex: 'SERVICE_OUTPUT',
                sortable: false,
                width: 180,
                listeners: {
                    scope:this
                },
                renderer: AppKit.renderer.ColumnComponentRenderer(this,{
                    border: false,
                    style: 'cursor: pointer',
                    listeners: {
                        render: function(c) {
                            c.update(c.baseArgs.value);
                            c.getEl().on("click",function(el) {
                                if(!c.getEl())
                                    return;
                                if(c.toggleState && c.toggleState == "open") {
                                    c.getEl().setHeight(c.origHeight);
                                    c.update(c.origValue);
                                    c.toggleState = "closed";
                                } else {
                                    c.origHeight = c.getEl().getHeight();
                                    c.origValue = c.getEl().dom.innerHTML;
                                    c.toggleState = "open";

                                    var html = Ext.DomHelper.markup({
                                        tag: 'div',
                                        children: [
                                            {tag: 'b', html: _('Long output')},
                                            {tag: 'div', html: c.baseArgs.record.get('SERVICE_LONG_OUTPUT')},
                                            {tag: 'b', html: _('<br/>Performance data')},
                                            {tag: 'div', html: c.baseArgs.record.get('SERVICE_PERFDATA')}
                                        ]
                                    });
                                    var height = Ext.util.TextMetrics.createInstance(c.getEl()).getHeight(html);
                                    c.getEl().setHeight(height);
                                    c.update(html);
                                }
                            });
                        },
                        scope:this
                    }
                }),
                scope:this

            }]
        });
        Icinga.Cronks.Tackle.ServicesSubGrid.superclass.initComponent.call(this);
        if(this.autoLoad)
            this.store.load();
        this.preventEventBubbling();
    },

    /**
     * As grids arent supposed to be nested in other grid rows, this requires a little bit
     * of hacking around event bubbling issues. rowSelection and clicks would be called
     * in the child grid and afterwards bubble to the parent grid, which leads to situations
     * like row selection being performed simultaneously on both grids.
     *
     * This stops those events in the child grid before they can reach the parent grid
     *
     * @author jannis.mosshammer<jannis.mosshammer@netways.de>
     */
    preventEventBubbling: function() {
        var methods = [ "onRowOver","onRowOut"];
        Ext.iterate(methods, function(m) {
            this.getView()[m] = function(e) {
                if(e.stopEvent)
                    e.stopEvent();
                return Ext.grid.GridView.prototype[m].apply(this,arguments);

            };
        },this);

        this.processEvent = function(name,e) {
            e.stopEvent();
            return Ext.grid.GridPanel.prototype.processEvent.apply(this,arguments);
        };
    }
});
