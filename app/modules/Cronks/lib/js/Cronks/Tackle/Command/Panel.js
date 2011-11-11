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
		
		this.dataview = new Icinga.Cronks.Tackle.Command.View({
			store : this.store,
			flex : 1
		});
		
		this.dataview.on('click', this.onCommandClick, this);
		
		this.form = new Icinga.Cronks.Tackle.Command.Form({
			flex : 1
		});
		
		this.add(this.dataview, this.form);
		
		this.doLayout();
	},
	
	onCommandClick : function(dataView, index, node, e) {
		var record = this.store.getAt(index);
		this.form.rebuildFormForCommand(record.data.definition);
	}
});