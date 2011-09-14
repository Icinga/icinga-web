Ext.ns('Icinga.Reporting.inputControl');

Icinga.Reporting.inputControl.ApiSelectionField = Ext.extend(Ext.form.ComboBox, {
	constructor : function(config) {
		
		Ext.apply(config, {
			typeAhead : true,
			triggerAction : 'all',
			mode : 'remote'
		});
		
		if (config.tpl) {
			config.tpl = new Ext.XTemplate(
				'<tpl for=".">',
				'<div class="x-combo-list-item">',
				config.tpl,
				'</div>',
				'</tpl>'
			);
		}
		
		config.hiddenName = config.name;
		
		var store = this.createStoreFromConfig({
			target : config.target,
			valueField : config.valueField,
			displayField : config.displayField
		}, config);
		
		config.store = store;
		
		AppKit.log(config);
		
		Icinga.Reporting.inputControl.ApiSelectionField.superclass.constructor.call(this, config);
	},
	
	createStoreFromConfig : function(config, origin) {
		
		var displayField = config.displayField;
		var valueField = config.valueField;
		
		var url = AppKit.util.Config.getBaseUrl() + String.format('/modules/web/api/{0}/json', config.target.toLowerCase());
		
		var baseParams = {
			order_col : displayField
		}
		
		var fields = [];
		
		if (!Ext.isEmpty(origin.columns) && Ext.isArray(origin.columns)) {
			Ext.apply(baseParams, {
				columns : origin.columns.join("|")
			});
			fields = origin.columns;
		} else {
			fields = [displayField, valueField];
		}
		
		AppKit.log(fields);
		
		var store = new Ext.data.JsonStore({
			url : url,
			autoDestroy : true,
			root : 'result',
			idProperty : displayField,
			fields : fields,
			baseParams : baseParams,
			
			listeners : {
				beforeload : function(store, options) {
					if (!Ext.isEmpty(store.baseParams.query)) {
						var targetJson = [];
						Ext.iterate(fields, function(item, key) {
							targetJson.push({
								type : 'atom',
								field : [item],
								method : ['like'],
								value : [String.format('*{0}*', store.baseParams.query)]
							});
						});
						
						store.baseParams.filters_json = Ext.util.JSON.encode({
							type : 'OR',
							field : targetJson
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
