Ext.ns('Icinga.Cronks.Tackle');

Icinga.Cronks.Tackle.ObjectGrid = Ext.extend(Ext.grid.GridPanel, {
	title : 'Object tree',
    downTimeQTip : _('Host is currently in a downtime'),
    acknowledgedQTip : _('Host problem has been acknowledged'),
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
                'SERVICE_STATE_COUNT'
            ]
            
        });
		this.store = new Icinga.Api.RESTStore({
            target: 'host',
            columns: [
                'HOST_ID',
                'HOST_NAME',
                'HOST_CURRENT_STATE',
                'HOST_OBJECT_ID',
                'HOST_LAST_CHECK',
                'HOST_NEXT_CHECK',
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
            store: this.store
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
                renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                    value = parseInt(value,10);
                    switch(value) {
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
                    if(parseInt(record.get('HOST_SCHEDULED_DOWNTIME_DEPTH'),10) > 0) {
                        return "<div class='icinga-icon-info-downtime' style='width:25px;height:16px;margin:auto' qtip='"+this.downTimeQTip+"'></div>";
                    } 
                    if(value > 0 && parseInt(record.get('HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED'),10) === 1)
                        return "<div class='icinga-icon-info-problem-acknowledged' style='width:25px;height:16px;margin:auto' qtip='"+this.acknowledgedQTip+"'></div>";
                    return "";
                },
                scope:this
            },{
                header: _('Host'),
                dataIndex : 'HOST_NAME'
            },{
                header: _('Health'),
                dataIndex: 'HOST_ID',
                renderer: function(value) {
                    var id = Ext.id();
                    var _this = this; 
                    (function() {
                        var cmp = new Ext.BoxComponent({
                            layout: 'fit',
                            tpl: new Ext.XTemplate(
                                '<tpl>',
                                    "<div qtip='{SERVICES_0} (of {COUNT_SERVICES_TOTAL}) services without open problems' style='width:{PERC_SERVICES_0}%;background-color:green;height:15px;float:left;'></div>",
                                    "<div qtip='{SERVICES_1} (of {COUNT_SERVICES_TOTAL}) services with state warning (open problems)' style='width:{PERC_SERVICES_1}%;background-color:yellow;height:15px;float:left;'></div>",
                                    "<div qtip='{SERVICES_2} (of {COUNT_SERVICES_TOTAL}) services with state critical (open problems)' style='width:{PERC_SERVICES_2}%;background-color:red;height:15px;float:left;'></div>",
                                    "<div qtip='{SERVICES_3} (of {COUNT_SERVICES_TOTAL}) services with state unknown (open problems)' style='width:{PERC_SERVICES_3}%;background-color:#ffee00;height:15px;float:left'></div>",
                                '</tpl>'
                            ),
                            renderTo: id
                        });
                        
                        _this.summaryStore.addListener("load", function(v,r) {
                            var obj = {
                                SERVICES_0: 0,
                                SERVICES_1: 0,
                                SERVICES_2: 0,
                                SERVICES_3: 0,
                                SERVICES_99: 0,
                                PERC_SERVICES_0: 0,
                                PERC_SERVICES_1: 0,
                                PERC_SERVICES_2: 0,
                                PERC_SERVICES_3: 0,
                                PERC_SERVICES_99: 0,
                                COUNT_SERVICES_TOTAL: 0
                            }
                            _this.summaryStore.filter("HOST_ID",value);
                            _this.summaryStore.each(function(r) {
                                obj["SERVICES_"+r.get('SERVICE_CURRENT_PROBLEM_STATE')] += parseInt(r.get('SERVICE_STATE_COUNT'),10);
                                obj.COUNT_SERVICES_TOTAL  += parseInt(r.get('SERVICE_STATE_COUNT'),10);
                            });
                            for(var idx in obj) {
                                if(idx == "PERC_SERVICES_TOTAL")
                                    continue;
                                obj["PERC_"+idx] = parseInt(obj[idx]*100/obj.COUNT_SERVICES_TOTAL,10);
                            };
                    
                            cmp.update(obj);
                        },_this,{single:true});
                    }).defer(100);
                    return '<div id="'+id+'"></div>'
                },
                scope:this
            },{
                header: _('Last check'),
                dataIndex : 'HOST_LAST_CHECK',
                width: 250,
                renderer: function(value,meta,record) {
                   var str = AppKit.util.Date.getElapsedString(value);
                   var now = new Date();
                   var lastCheckDate = new Date(value);
                   var nextCheckDate = new Date(record.get('HOST_NEXT_CHECK'));
                   var elapsed = parseInt(now.getElapsed(lastCheckDate)/1000,10);
                   
                   if(now.between(lastCheckDate,nextCheckDate))
                       return "<div style='color:red;padding-left:19px;background-position: left center;' class='icinga-icon-exclamation-red'"+
                              " qtip='Should have been checked "+AppKit.util.Date.getElapsedString(value)+"'>"+value+"</div>";
                   if(elapsed > (60*60*24))
                       return "<div qtip='"+str+"'>"+value+"</div>";
                   return "<div qtip='"+value+"'>"+str+"</div>"
                }
            }]
		});
		
		Icinga.Cronks.Tackle.ObjectGrid.superclass.initComponent.call(this);
	}
	
});

Ext.reg('cronks-tackle-objectgrid', Icinga.Cronks.Tackle.ObjectGrid);