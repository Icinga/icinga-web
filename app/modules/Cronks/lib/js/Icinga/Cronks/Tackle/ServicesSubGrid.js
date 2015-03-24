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

Ext.ns('Icinga.Cronks.Tackle');


Icinga.Cronks.Tackle.ServicesSubGrid = Ext.extend(Ext.grid.GridPanel, {
    autoDestroy: true,
    ctCls: 'x-tree-lines',
    stripeRows: true,
    style:'margin-left:25px',
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
        
        config.store = this.createStore(config.hostId,config.filter);

        config.bbar = new Ext.PagingToolbar({
            store: config.store,
            displayInfo: true,
            pageSize:25
        });
        
        Icinga.Cronks.Tackle.ServicesSubGrid.superclass.constructor.call(this, config);
    },
    
    createStore: function(hostId,filter) {

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
        var jsonFilter;
        var hostFilter = {
            type: 'atom',
            method: ['='],
            field: ['HOST_ID'],
            value: [hostId]
        };
        if(filter) {
            jsonFilter = filter;
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

        return this.store;
    },

    realign: function() {
        try {
            this.setWidth(this.parent.getInnerWidth()-50);
            var adjHeight = this.parent.getInnerHeight();
            var reqHeight = (this.getStore().getCount()+1)*30;
            if(reqHeight < 200)
                reqHeight = 200;
            var maxHeight = adjHeight*0.7;
            if(this.el && this.el.dom)
                this.setHeight(reqHeight > maxHeight ? maxHeight : reqHeight);
            this.doLayout();
        } catch(e) {
            // ignore errors, those can occur when the grid is refreshed
        }
    },

    initComponent : function() {
        this.parent.on("columnresize", function(cmp) {
            this.realign();
        },this);
        this.parent.on("resize", function(cmp) {
            this.realign();
        },this);
        this.store.on("load",function(store,records) {
            this.realign();
        },this);
        this.on("afterrender",function() {
            this.realign();
        },this);
        
        this.cm = new Ext.grid.ColumnModel({
            columns : [{
                dataIndex: 'SERVICE_ID',
                renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                    metaData.css = 'x-tree-elbow';

                    return " ";
                },
                width: 20
            },{
                dataIndex: 'SERVICE_CURRENT_STATE',
                renderer: Icinga.Cronks.Tackle.Renderer.StatusColumnRenderer,
                width: 25
            },{
                header: _('Service name'),
                dataIndex : 'SERVICE_NAME',
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

                    return "<span style='"+((state == 1 || state == 99) ? 'color:#ffffff' : 'color:#000000') +"'>"+value+"</span>";
                }
            },{
                renderer: function() {
                    return '<div class="icinga-icon-service" style="width:20px;height:16px"></div>';
                },
                width:35
            },{
                header: _('SLA'),
                dataIndex: 'SLA_STATE_AVAILABLE',
                width:50,
                resizable:false,
                renderer: function(value,meta,record) {
                    if(record.get('SLA_STATE_AVAILABLE') == 0 &&
                         record.get('SLA_STATE_UNAVAILABLE') == 0)
                          return "<div style='width:50px;height:14px' ext:qtip='"+_('No SLA information available')+"'></div>";
                    value = parseFloat(value,10).toFixed(3);

                    return value+"%";
                }

            },{
                header: _('Last check'),
                dataIndex : 'SERVICE_LAST_CHECK',
                width: 150,
                renderer: function(value,meta,record) {
                   var str = AppKit.util.Date.getElapsedString(value);
                   var now = new Date();
                   var lastCheckDate = Date.parseDate(value,'Y-m-d H:i:s')
                        || Date.parseDate(value,'Y-m-d H:i:sP')
                        || Date.parseDate(value+":00",'Y-m-d H:i:sP');
                   var nextCheckDate = Date.parseDate(record.get('SERVICE_NEXT_CHECK'),'Y-m-d H:i:s')
                        || Date.parseDate(value,'Y-m-d H:i:sP')
                        || Date.parseDate(value+":00",'Y-m-d H:i:sP');
                   var elapsed = parseInt(now.getElapsed(lastCheckDate)/1000,10);

                   if(!now.between(lastCheckDate,nextCheckDate.add(Date.SECOND,30)))
                       return "<div style='color:red;padding-left:19px;background-position: left center;' class='icinga-icon-exclamation-red'"+
                              " ext:qtip='Should have been checked "+AppKit.util.Date.getElapsedString(value)+"'>"+value+"</div>";
                   if(elapsed > (60*60*24))
                       return "<div ext:qtip='"+str+"'>"+value+"</div>";
                   return "<div ext:qtip='"+value+"'>"+str+"</div>";
                }
            }, {
                header: _('Flags'),
                dataIndex: 'SERVICE_ID',
                width: 100,
                renderer: Icinga.Cronks.Tackle.Renderer.FlagIconColumnRenderer('service'),
                listeners: {
                    click: Icinga.Cronks.Tackle.Renderer.FlagIconColumnClickHandler,
                    scope: this
                }

            }, {
                header: _('Output'),
                dataIndex: 'SERVICE_OUTPUT',
                sortable: false,
                width: 400,
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
                },{
                    dataIndex: 'SERVICE_ACTION_URL',
                    width: 75,
                    renderer: Icinga.Cronks.Tackle.Renderer.AdditionalURLColumnRenderer("SERVICE"),
                    listeners: {
                        click: Icinga.Cronks.Tackle.Renderer.AdditionalURLColumnClickHandler("SERVICE"),
                        scope:this
                    }

                }),
                scope:this

            }]
        });
        Icinga.Cronks.Tackle.ServicesSubGrid.superclass.initComponent.call(this);
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

Ext.reg('cronks-tackle-information-servicegrid', Icinga.Cronks.Tackle.ServicesSubGrid);