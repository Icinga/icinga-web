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
		
		this.objectGrid = new Icinga.Cronks.Tackle.ObjectGrid({
            region : 'center'
		});
		
		this.infoTabs = new Icinga.Cronks.Tackle.InfoTabPanel({});
		
		this.tabDefaults = new Icinga.Cronks.Tackle.Information.Default();
		this.tabCommands = new Icinga.Cronks.Tackle.Information.Commands();
		this.tabComments = new Icinga.Cronks.Tackle.Information.Comments();
		this.tabRelations = new Icinga.Cronks.Tackle.Information.Relations();
		this.tabServices = new Icinga.Cronks.Tackle.Information.Services();
		
		this.infoTabs.add([
		  this.tabDefaults,
		  this.tabServices,
		  this.tabCommands,
		  this.tabComments,
		  this.tabRelations
		])
		
		this.add([
		  this.objectGrid, {
		  	xtype : 'panel',
		  	iconCls : 'icinga-icon-universal',
		  	region : 'south',
		  	title : _('Object'),
		  	height : 300,
		  	minSize : 300,
		  	maxSize : 600,
		  	collapsible : true,
		  	split : true,
		  	items : this.infoTabs
		  }
		]);
	}
});

Ext.reg('cronks-tackle-cronk', Icinga.Cronks.Tackle.Cronk);