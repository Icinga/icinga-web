Ext.ns('Icinga.Reporting.widget');

Icinga.Reporting.widget.WeekDayPicker = Ext.extend(Ext.ux.form.MultiSelect, {
	
	constructor : function(config) {
		
		this.store = [
			[1, _('Mon')],
			[2, _('Tue')],
			[3, _('Wed')],
			[4, _('Thu')],
			[5, _('Fri')],
			[6, _('Sat')],
			[7, _('Sun')]
		];
		
		Icinga.Reporting.widget.WeekDayPicker.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Reporting.widget.WeekDayPicker.superclass.initComponent.call(this);
	}
	
});