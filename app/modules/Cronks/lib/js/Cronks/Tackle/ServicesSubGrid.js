Ext.ns('Icinga.Cronks.Tackle');


Icinga.Cronks.Tackle.ServicesSubGrid = Ext.extend(Ext.grid.GridPanel, {
    autoDestroy: true,
    ctCls: 'x-tree-lines',
    stripeRows: true,
    style:'margin-left:25px',    
    cls: 'icinga-service-subgrid',
    events: ['serviceSelected'],
    bubbleEvents: ['serviceSelected'],
    listeners: {
        rowClick: function(grid, idx,event) {
            grid.fireEvent('serviceSelected',grid.getStore().getAt(idx));
        }
    },

    constructor : function(config) {
        
        config.store = this.createStore(config.hostId);

        config.bbar = new Ext.PagingToolbar({
            store: config.store,
            displayInfo: true,
            pageSize:25
        });
        
        Icinga.Cronks.Tackle.ServicesSubGrid.superclass.constructor.call(this, config);
    },
    createStore: function(hostId) {

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
                'SERVICE_SCHEDULED_DOWNTIME_DEPTH',
                'SERVICE_PROBLEM_HAS_BEEN_ACKNOWLEDGED'
            ]
           
        });
        this.store.setFilter({
            type: 'AND',
            field: [{
                type: 'atom',
                method: ['='],
                field: ['HOST_ID'],
                value: [hostId]
            }]
        });
        return this.store;
    },
    realign: function() {
        try {
            this.setWidth(this.parent.getInnerWidth()*0.8);
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
                renderer: function() {
                    return '<div class="icinga-icon-service" style="width:20px;height:16px"></div>';
                },
                width:35
            },{
                header: _('Service name'),
                dataIndex : 'SERVICE_NAME'
            },{
                header: _('SLA'),
                dataIndex: 'SLA_STATE_AVAILABLE',
                width:50,
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
                dataIndex : 'SERVICE_LAST_CHECK',
                width: 150,
                renderer: function(value,meta,record) {
                   var str = AppKit.util.Date.getElapsedString(value);
                   var now = new Date();
                   var lastCheckDate = new Date(value);
                   var nextCheckDate = new Date(record.get('SERVICE_NEXT_CHECK'));
                   var elapsed = parseInt(now.getElapsed(lastCheckDate)/1000,10);

                   if(!now.between(lastCheckDate,nextCheckDate))
                       return "<div style='color:red;padding-left:19px;background-position: left center;' class='icinga-icon-exclamation-red'"+
                              " qtip='Should have been checked "+AppKit.util.Date.getElapsedString(value)+"'>"+value+"</div>";
                   if(elapsed > (60*60*24))
                       return "<div qtip='"+str+"'>"+value+"</div>";
                   return "<div qtip='"+value+"'>"+str+"</div>";
                }
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