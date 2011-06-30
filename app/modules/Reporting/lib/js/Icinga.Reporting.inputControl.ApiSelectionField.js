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
		
		var store = new Ext.data.JsonStore({
			url : AppKit.util.Config.getBaseUrl() + String.format('/web/api/{0}/json', config.target.toLowerCase()),
			autoDestroy : true,
			root : 'result',
			idProperty : displayField,
			fields : [displayField, valueField]
		});
		
		return store;
	},
	
	initComponent : function() {
		Icinga.Reporting.inputControl.ApiSelectionField.superclass.initComponent.call(this);
	}
});