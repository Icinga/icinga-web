Ext.ns('Icinga.Cronks.Tackle');

Icinga.Cronks.Tackle.Cronk = Ext.extend(Ext.Panel, {
	
	constructor : function(config) {
		
		config = Ext.apply(config || {}, {
			layout : 'border',
			border : false,
			defaults : {
				border : false
			}
		})
		
		Icinga.Cronks.Tackle.Cronk.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Cronks.Tackle.Cronk.superclass.initComponent.call(this);
		
		this.add({
			xtype : 'cronks-tackle-objecttree',
			region : 'center'
		}, {
			xtype : 'panel',
			region : 'south',
			title : 'SOUTH',
			height : 200,
			collapsible : true
		});
	}
});

Ext.reg('cronks-tackle-cronk', Icinga.Cronks.Tackle.Cronk);