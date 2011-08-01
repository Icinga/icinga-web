Ext.ns('Icinga.Reporting.inputControl');

Icinga.Reporting.inputControl.ApiSelectionField = Ext.extend(Ext.form.ComboBox, {
	constructor : function(config) {
		
		Ext.apply(config, {
			typeAhead : true,
			triggerAction : 'all',
			mode : 'remote'
		});
		
		config.hiddenName = config.name;
		
		var store = this.createStoreFromConfig({
			target : config.target,
			valueField : config.valueField,
			displayField : config.displayField
		});
		
		config.store = store;
		
		Icinga.Reporting.inputControl.ApiSelectionField.superclass.constructor.call(this, config);
	},
	
	createStoreFromConfig : function(config) {
		
		var displayField = config.displayField;
		var valueField = config.valueField;
		
		var url = AppKit.util.Config.getBaseUrl() + String.format('/web/api/{0}/json', config.target.toLowerCase());
		
		var store = new Ext.data.JsonStore({
			url : url,
			autoDestroy : true,
			root : 'result',
			idProperty : displayField,
			fields : [displayField, valueField],
			baseParams : {
				order_col : displayField
			},
			
			listeners : {
				beforeload : function(store, options) {
					if (!Ext.isEmpty(store.baseParams.query)) {
						store.baseParams.filters_json = Ext.util.JSON.encode({
							type : 'AND',
							field : [{
								type : 'atom',
								field : [displayField],
								method : ['like'],
								value : [String.format('*{0}*', store.baseParams.query)]
							}]
						});
					}
				}
			}
		});
		
		store.load();
		
		return store;
	},
	
	initComponent : function() {
		Icinga.Reporting.inputControl.ApiSelectionField.superclass.initComponent.call(this);
		
		this.on('beforequery', function(queryEvent) {
			if (Ext.isEmpty(queryEvent.query)) {
				delete(this.store.baseParams.filters_json);
				this.store.reload();
			}
		}, this);
	}
});