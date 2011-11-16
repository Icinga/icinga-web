/*global Ext: false, Icinga: false, _: false */
Ext.ns('Icinga.Cronks.Tackle');

Icinga.Cronks.Tackle.ObjectGrid = Ext.extend(Ext.grid.GridPanel, {
    autoRefresh: true,
    events: ['hostSelected','serviceSelected'],
    viewConfig: {
        
//       forceFit: true,
       getRowClass: function(record,index,rp) {

            rp.body = '<p>'+record.data.HOST_NAME+'</p>';
                return 'x-grid3-row-expanded';


            if(parseInt(record.get('HOST_SCHEDULED_DOWNTIME_DEPTH'),10) > 0)
                return 'icinga-row-downtime ';
        }
    },
    hostStore: null,
    serviceInfoStore: null,

    constructor : function(config) {
		this.id = Ext.id();
		config = Ext.apply(config || {}, {
			layout : 'fit'
		});

        this.createDataHandler(config);
        config.tbar = new Icinga.Cronks.Tackle.Filter.TackleMainFilterTbar({
            id:this.id,
            store: this.store
        });
        this.updateFilter = config.tbar.updateFilter;
        this.getSVCFilter = config.tbar.getSVCFilter.createDelegate(config.tbar);
        Icinga.Cronks.Tackle.ObjectGrid.superclass.constructor.call(this, config);
	},

    listeners: {
        rowclick: function(grid, idx, event ) {
            var record = grid.getStore().getAt(idx);
            grid.fireEvent('hostSelected',record);
        }
    },

    
    createDataHandler: function(cfgRef) {
		this.summaryStore = new Icinga.Api.RESTStore({
            target: 'service_status_summary',
            columns: [
                'HOST_ID',
                'SERVICE_CURRENT_PROBLEM_STATE',
                'SERVICE_SCHEDULED_DOWNTIME_DEPTH',
                'SERVICE_PROBLEM_HAS_BEEN_ACKNOWLEDGED',
                'SERVICE_STATE_COUNT'
            ]
            
        });
		this.store = new Icinga.Api.RESTStore({
            target: 'host',
            limit: 50,
            offset: 0,
            remoteSort:true,
            countColumn: true,
            withSLA: true,
            columns: [
                'INSTANCE_NAME',
                'HOST_ID',
                'HOST_NAME',
                'HOST_CURRENT_PROBLEM_STATE',
                'HOST_CURRENT_STATE',
                'HOST_OBJECT_ID',
                'HOST_LAST_CHECK',
                'HOST_NEXT_CHECK',
                'HOST_OUTPUT',
                'HOST_LONG_OUTPUT',
                'INSTANCE_NAME',
                'HOST_SCHEDULED_DOWNTIME_DEPTH',
                'HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED',
                'HOST_PASSIVE_CHECKS_ENABLED',
                'HOST_ACTIVE_CHECKS_ENABLED',
                'HOST_IS_FLAPPING',
                'HOST_CHECK_TYPE',
                'HOST_NOTIFICATIONS_ENABLED'
                
            ],
            listeners: {
 
                load: function(s,records) {
                    var idFilter = {
                        type: 'OR',
                        field: []
                    };
                    Ext.iterate(records,function(r) {
                        idFilter.field.push({
                            type: 'atom',
                            method: ['='],
                            field: ['HOST_ID'],
                            value: [r.get('HOST_ID')]
                        });
                    },this);
                    this.summaryStore.setFilter(idFilter);
                    this.summaryStore.load();
                    for(var i in this.visibleServicePanels)
                        this.closeServicePanel(i);
                },
                scope: this
            }
        });
        cfgRef.bbar = new Ext.PagingToolbar({
            store: this.store,
            displayInfo: true,
            pageSize:50
        });
    },
    sm : new Ext.grid.CheckboxSelectionModel(),
    refreshLocked: false,
    lockRefresh: function() {
        if(this.refreshLocked)
            return;
        var load = this.store.load;
        this.store.load = function() {
            this.store.load.defer(500);
        }
        this.refreshLocked = true;
        (function() {
            this.store.load=load;
            this.refreshLocked = false;
        }).defer(300);
    },

    visibleServicePanels: {
        length: 0
    },
    
    closeAllServicePanels: function() {
        for(var i in this.visibleServicePanels) {
            this.closeServicePanel(i);
        }
    },

    closeServicePanel: function(id) {
        if(this.visibleServicePanels[id]) {
            if(!this.visibleServicePanels[id] || !this.visibleServicePanels[id].destroy) {
                delete(this.visibleServicePanels[id]);
                return true;
            }
            this.visibleServicePanels[id].destroy();
            delete(this.visibleServicePanels[id]);
            this.visibleServicePanels.length--;
            return true;
        }
    },
    
    openServicePanel: function(id, el) {
        if(this.visibleServicePanels[id])
            this.visibleServicePanels[id].destroy();
        
        this.visibleServicePanels[id] = new Icinga.Cronks.Tackle.ServicesSubGrid({
            filter: this.getSVCFilter(),
            hostId: id,
            renderTo: el,
            parent:this,
            listeners: {
                beforeadd: function() {
                    this.lockRefresh();
                },
                removed: function() {
                    closeServicePanel(id);
                },
                serviceSelected_sub: function(val) {
                    this.fireEvent('serviceSelected',val);
                },
                scope: this
            }
        });
        this.visibleServicePanels.length++;
    },

	initComponent : function() {
		
        this.on("render", function() {
            this.updateFilter();
        },this);

		this.cm = new Ext.grid.ColumnModel({
			columns : [
                this.sm,
            {
				dataIndex : 'HOST_CURRENT_STATE',
                columnWidth: 25,
                width: 25,
                resizable: false,
                sortable:true,
                renderer: Icinga.Cronks.Tackle.Renderer.StatusColumnRenderer,
                scope:this
            },{
                header: _('Host'),
                dataIndex : 'HOST_NAME',
                sortable: true,
                style: 'border: 1px solid black;',
                renderer: function(value, metaData, record, rowIndex, colIndex, store) {   
                    var state = parseInt(record.get("HOST_CURRENT_STATE"),10);

                    switch(state) {
                        case 0:
                            metaData.css = 'icinga-status-up';
                            break;
                        case 1:
                            metaData.css = 'icinga-status-down';
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
                dataIndex: 'HOST_ID',
                disableHeader: true,
                width: 25,
                resizable: false,
                tooltip: _('Show services for this host'),
                renderer: function() {
                    return '<div class="icinga-icon-service" style="cursor:pointer;height:16px;width:16px"></div>';
                },

                listeners: {
                    click: function(col,grid,rowIdx,e) {
                       
                        var row = this.getView().getRow(rowIdx);
                        var record = this.getStore().getAt(rowIdx);
                        var id = record.get('HOST_ID');
                        if(this.visibleServicePanels[id])
                            this.closeServicePanel(id);
                        else  {
                            this.closeAllServicePanels();
                            this.openServicePanel(id,row);
                        }
                       
                    },
                    scope:this
                }
                
            },{
                header: _('Service health'),
                dataIndex: 'HOST_ID',
                menuDisabled: true,
                width:100,
                resizable: false,
                renderer: Icinga.Cronks.Tackle.Renderer.ServiceHealthRenderer,
                scope:this
            },{
                header: _('SLA'),
                dataIndex: 'SLA_STATE_AVAILABLE',
                width:50,
                sortable: true,
                resizable:false,
                renderer: function(value,meta,record) {
                    if(record.get('SLA_STATE_AVAILABLE') == 0 &&
                         record.get('SLA_STATE_UNAVAILABLE') == 0)
                          return "<div style='width:50px;height:14px' qtip='"+_('No SLA information available')+"'></div>";
                    value = parseFloat(value,10).toFixed(3);
                    
                    return value+"%";
                }
                
            },{
                header: _('Last check'),
                dataIndex : 'HOST_LAST_CHECK',
                sortable: true,
                width: 150,
                renderer: function(value,meta,record) {
                   var str = AppKit.util.Date.getElapsedString(value);
                   var now = new Date();
                   var lastCheckDate = Date.parseDate(value,'Y-m-d H:i:s');
                   var nextCheckDate = Date.parseDate(record.get('HOST_NEXT_CHECK'),'Y-m-d H:i:s');

                   var elapsed = parseInt(now.getElapsed(lastCheckDate)/1000,10);
                   
                   if(!now.between(lastCheckDate,nextCheckDate.add(Date.SECOND,30)))
                       return "<div style='color:red;padding-left:19px;background-position: left center;' class='icinga-icon-exclamation-red'"+
                              " qtip='Should have been checked "+AppKit.util.Date.getElapsedString(value)+"'>"+value+"</div>";
                   if(elapsed > (60*60*24))
                       return "<div qtip='"+str+"'>"+value+"</div>";
                   return "<div qtip='"+value+"'>"+str+"</div>";
                }
            },{
                header: _('Flags'),
                dataIndex: 'HOST_ID',
                sortable: false,
                width: 150,
                renderer: Icinga.Cronks.Tackle.Renderer.FlagIconColumnRenderer('host'),
                listeners: {
                    click: Icinga.Cronks.Tackle.Renderer.FlagIconColumnClickHandler,
                    scope: this
                }

            }, {
                header: _('Output'),
                dataIndex: 'HOST_OUTPUT',
                sortable: false,
                width: 200,
                listeners: {
                    scope:this
                },
                renderer: AppKit.renderer.ColumnComponentRenderer({
                    html: "%VALUE%",
                    border: false,
                    record: "%RECORD%",
                    listeners: {
                        render: function(c) {
                            c.getEl().on("click",function(el) {
                                if(!c.getEl())
                                    return;
                                if(c.toggleState && c.toggleState == "open") {
                                    c.getEl().setHeight(c.origHeight)
                                    c.update(c.origValue);
                                    c.toggleState = "closed";
                                } else {
                                    c.origHeight = c.getEl().getHeight();
                                    c.origValue = c.getEl().dom.innerHTML;
                                    c.toggleState = "open";
                                    c.getEl().setHeight(100);
                                    c.update(
                                        "Long output: <br/>"+
                                        c.record.get("HOST_LONG_OUTPUT")
                                    );
                                }
                            });
                        },
                        scope:this
                    }
                }),
                scope:this
               
            }, {
                dataIndex: 'HOST_ID',
                renderer: function() {return ""},
                autoExpand:true,
                menuDisabled: true,
                width: 100
            }]
		});
		
		Icinga.Cronks.Tackle.ObjectGrid.superclass.initComponent.call(this);
	}
	
});

Ext.reg('cronks-tackle-objectgrid', Icinga.Cronks.Tackle.ObjectGrid);