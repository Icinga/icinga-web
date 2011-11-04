/*global Ext: false, Icinga: false, _: false */
Ext.ns('Icinga.Cronks.Tackle');

Icinga.Cronks.Tackle.ObjectGrid = Ext.extend(Ext.grid.GridPanel, {
	title : 'Object tree',
    viewConfig: {
   //     forceFit: true,
        getRowClass: function(record,index) {

            if(parseInt(record.get('HOST_SCHEDULED_DOWNTIME_DEPTH'),10) > 0)
                return 'icinga-row-downtime ';
        }
    },
    hostStore: null,
    serviceInfoStore: null,
	
    constructor : function(config) {
		
		config = Ext.apply(config || {}, {
			layout : 'fit'
		});

        this.createDataHandler(config);
		Icinga.Cronks.Tackle.ObjectGrid.superclass.constructor.call(this, config);
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
            limit: 25,
            offset: 0,
            countColumn: true,
            withSLA: true,
            columns: [
                'INSTANCE_NAME',
                'HOST_ID',
                'HOST_NAME',
                'HOST_CURRENT_STATE',
                'HOST_OBJECT_ID',
                'HOST_LAST_CHECK',
                'HOST_NEXT_CHECK',
                'INSTANCE_NAME',
                'HOST_SCHEDULED_DOWNTIME_DEPTH',
                'HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED'
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
                    
                },
                scope: this
            }
        });
        cfgRef.bbar = new Ext.PagingToolbar({
            store: this.store,
            displayInfo: true
        });
    },
   
	initComponent : function() {
		
		this.store.load();
		this.cm = new Ext.grid.ColumnModel({

			columns : [{
				header : _('State'),
				dataIndex : 'HOST_CURRENT_STATE',
                columnWidth: 25,
                width: 25,
                renderer: Icinga.Cronks.Tackle.Renderer.StatusColumnRenderer,
                scope:this
            },{
                header: _('Host'),
                dataIndex : 'HOST_NAME'
            },{
                header: _('Health'),
                dataIndex: 'HOST_ID',
                renderer: Icinga.Cronks.Tackle.Renderer.ServiceHealthRenderer,
                scope:this
            },{
                header: _('Availability'),
                dataIndex: 'SLA_STATE_AVAILABLE',
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
                width: 150,
                renderer: function(value,meta,record) {
                   var str = AppKit.util.Date.getElapsedString(value);
                   var now = new Date();
                   var lastCheckDate = new Date(value);
                   var nextCheckDate = new Date(record.get('HOST_NEXT_CHECK'));
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
		
		Icinga.Cronks.Tackle.ObjectGrid.superclass.initComponent.call(this);
	}
	
});

Ext.reg('cronks-tackle-objectgrid', Icinga.Cronks.Tackle.ObjectGrid);