Ext.ns('Icinga.Reporting.widget');

Icinga.Reporting.widget.MonthPicker = Ext.extend(Ext.ux.form.MultiSelect, {
	
	constructor : function(config) {
		
		this.store = [
			[1, _('Jan')],
			[2, _('Feb')],
			[3, _('Mar')],
			[4, _('Apr')],
			[5, _('May')],
			[6, _('Jun')],
			[7, _('Jul')],
			[8, _('Aug')],
			[9, _('Sep')],
			[10, _('Oct')],
			[11, _('Nov')],
			[12, _('Dec')]
		];
		
		Icinga.Reporting.widget.MonthPicker.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Reporting.widget.MonthPicker.superclass.initComponent.call(this);
	}
	
});