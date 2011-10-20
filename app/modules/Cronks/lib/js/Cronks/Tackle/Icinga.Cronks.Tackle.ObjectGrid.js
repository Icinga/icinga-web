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
	constructor : function(config) {
		
		config = Ext.apply(config || {}, {
			layout : 'fit'
		});
		
		Icinga.Cronks.Tackle.ObjectGrid.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		
		this.store = new Icinga.Api.RESTStore({
            target: 'host',
            columns: [
                'HOST_ID',
                'HOST_NAME',
                'HOST_CURRENT_STATE',
                'HOST_OBJECT_ID',
                'HOST_LAST_CHECK',
                'HOST_SCHEDULED_DOWNTIME_DEPTH',
                'HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED'
            ]
        
        })
		this.store.load();
		this.cm = new Ext.grid.ColumnModel({

			columns : [{
				header : _('State'),
				dataIndex : 'HOST_CURRENT_STATE',
                columnWidth: 25,
                width; 25,
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
                header: _('Last check'),
                dataIndex : 'HOST_LAST_CHECK'
            }]
		});
		
		Icinga.Cronks.Tackle.ObjectGrid.superclass.initComponent.call(this);
	}
	
});

Ext.reg('cronks-tackle-objectgrid', Icinga.Cronks.Tackle.ObjectGrid);