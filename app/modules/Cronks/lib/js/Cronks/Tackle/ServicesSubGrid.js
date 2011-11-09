Ext.ns('Icinga.Cronks.Tackle');

Icinga.Cronks.Tackle.ServicesSubGrid = Ext.extend(Ext.grid.GridPanel, {

    ctCls: 'x-tree-lines',
    stripeRows: true,
    style:'margin-left:25px',
    constructor : function(config) {
        
        config.store = this.createStore(config.hostId);

        config.bbar = new Ext.PagingToolbar({
            store: config.store,
            displayInfo: true,
            pageSize:25
        })
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
        this.setWidth(this.parent.getInnerWidth()-25);
        var adjHeight = this.parent.getInnerHeight();
        var reqHeight = this.getStore().getCount()*30;
        var maxHeight = adjHeight*0.7;
        this.setHeight(reqHeight > maxHeight ? maxHeight : reqHeight);
        this.doLayout();
    },

    initComponent : function() {
        this.parent.on("columnresize", function(cmp) {
            this.realign();
        },this);
        this.parent.on("resize", function(cmp) {
            this.realign();
        },this);
        this.store.on("load",function(store,records) {
            this.setHeight((records.length*30)%(this.parent.getHeight()*0.8));
        },this);
        this.on("afterrender",function() {
            this.realign();
        },this)
        
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
                listeners: {

                    click: function(col,grid,rowIdx,e) {
                        e.stopEvent();
                        return false;
                    },
                    scope: this
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
    }
});

Ext.reg('cronks-tackle-information-servicegrid', Icinga.Cronks.Tackle.ServicesSubGrid);