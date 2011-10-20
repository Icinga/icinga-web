Ext.ns('Icinga.Cronks.Tackle');

Icinga.Cronks.Tackle.ObjectGrid = Ext.extend(Ext.grid.GridPanel, {
	title : 'Object tree',
	
	constructor : function(config) {
		
		config = Ext.apply(config || {}, {
			layout : 'fit'
		});
		
		Icinga.Cronks.Tackle.ObjectGrid.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		
		this.store = new Ext.data.ArrayStore({
			autoDestroy : true,
			idIndex : 0,
			fields : ['object_id', 'object_name'],
			data : [
			 [1, 'test-host-1'],
			 [2, 'test-host-2'],
			 [3, 'test-host-3'],
			 [4, 'test-host-4']
			]
		});
		
		this.cm = new Ext.grid.ColumnModel({
			defaults : {
				width : 200,
				
			},
			columns : [{
				id : 'object_id',
				header : _('OID'),
				dataIndex : 'object_id'
			}, {
				header : _('Name'),
				dataIndex : 'object_name'
			}]
		});
		
		Icinga.Cronks.Tackle.ObjectGrid.superclass.initComponent.call(this);
	}
	
});

Ext.reg('cronks-tackle-objectgrid', Icinga.Cronks.Tackle.ObjectGrid);