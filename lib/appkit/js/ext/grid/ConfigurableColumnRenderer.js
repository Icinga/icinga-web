Ext.ns('AppKit.Ext.grid');

AppKit.Ext.grid.ConfigurableColumnRenderer = {
	config : {},
	
	setConfig : function (renderer, cfg) {
		this.config[renderer] = cfg;
	},
	
	getConfig : function (renderer) {
		return this.config[renderer] || {};
	}
};