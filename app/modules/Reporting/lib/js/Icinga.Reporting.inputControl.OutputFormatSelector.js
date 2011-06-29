Ext.ns('Icinga.Reporting.inputControl');

Icinga.Reporting.inputControl.OutputFormatSelector = Ext.extend(Ext.form.ComboBox, {
	
	constructor : function(config) {
		config = Ext.apply(config || {}, {
			typeAhead : 'true',
			mode : 'local',
			triggerAction : 'all',
			valueField : 'id',
			displayField : 'label',
			store : new Ext.data.ArrayStore({
				autoDestroy : true,
				id : 0,
				fields: ['id', 'label'],
				data : [
					['pdf', _('PDF')],
					['csv', _('Comma seperated spreadsheet')]
				]
			})
		});
		
		Icinga.Reporting.inputControl.OutputFormatSelector.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Reporting.inputControl.OutputFormatSelector.superclass.initComponent.call(this);
	}
	
});