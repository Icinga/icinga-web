Ext.ns('Icinga.Reporting.abstract');

Icinga.Reporting.abstract.ApplicationWindow = Ext.extend(Ext.Panel, {
	
	constructor : function(config) {
		Icinga.Reporting.abstract.ApplicationWindow.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		if (Ext.isEmpty(this.plugins)) {
			this.plugins = [];
		}
		
		this.plugins.push(new Ext.ux.plugins.ContainerMask ({
			msg : Ext.isEmpty(this.mask_text) ? _('Please be patient') : this.mask_text,
			masked : Ext.isEmpty(this.mask_show) ? false :  this.mask_show
		}));
		
		Icinga.Reporting.abstract.ApplicationWindow.superclass.initComponent.call(this);
	},
	
	setToolbarEnabled : function(bool, pos) {
		if (Ext.isEmpty(bool)) {
			bool = true;
		}
		var i = 0;
		this.getTopToolbar().items.eachKey(function(key, item) {
			if (!Ext.isEmpty(pos)) {
				if (pos == ++i) {
					item.setDisabled(!bool);
				}
			}
			else {
				item.setDisabled(!bool);
			}
		});
	}
	
});