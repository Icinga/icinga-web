Ext.ns('Icinga.Cronks.Tackle.Command');

Icinga.Cronks.Tackle.Command.Panel = Ext.extend(Ext.Panel, {
	
	title : _('Commands'),
	iconCls : 'icinga-icon-bricks',
	layout : 'hbox',
	layoutConfig : {
		align : 'stretch',
		pack : 'start'
	},
	
	constructor : function(config) {
		if (Ext.isEmpty(config.type)) {
                throw ("config.type is needed: host, service, hostgroup, servicegroup, process");
        }
		
		this.store = new Ext.data.JsonStore({
			url : AppKit.c.path+'/modules/appkit/dispatch',
			baseParams : {
				module : 'Api',
				action : 'ApiCommandInfo',
				outputType : 'json',
				params : {
					extjs : 1
				}
			}
		});
		
		Icinga.Cronks.Tackle.Command.Panel.superclass.constructor.call(this, config);
	},
	
	setType : function(type) {
		this.store.setBaseParam('params', Ext.encode({
			extjs : 1,
			type : type
		}));
		
		this.store.load();
	},
	
	initComponent : function() {
		Icinga.Cronks.Tackle.Command.Panel.superclass.initComponent.call(this);
		
		this.setType(this.type);
		
		this.commandDataView = new Icinga.Cronks.Tackle.Command.View({
			store : this.store,
			flex : 1
		});
		
		var dummyPanel2 = new Ext.Panel({
            title : 'DUMMY',
            html : 'I am a stupid dummy panel',
            flex : 1
        });
		
		this.add(this.commandDataView, dummyPanel2);
		
		this.doLayout();
	}
});