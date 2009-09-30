
Ext.ns('AppKit.Ext.grid');

AppKit.Ext.grid.GridPanel = Ext.extend(Ext.grid.GridPanel, {
	meta : {},
	filter: {},
	
	initComponent : function() {
		this.tbar = this.buildTopToolbar();
		
		AppKit.Ext.grid.GridPanel.superclass.initComponent.call(this);
	},

	// Top toolbar of the grid
	buildTopToolbar : function() {
		return [{
			text: 'Refresh',
			iconCls: 'silk-arrow-refresh',
			tooltip: 'Refresh the data in the grid',
			handler: function(oBtn, e) { this.store.reload(); },
			scope: this
		}];
	},
	
	setMeta : function(m) {
		this.meta = m;
	},
	
	setFilter : function(f) {
		this.filter = f;
	}
	
});

Ext.reg('appkit-grid', AppKit.Ext.grid.GridPanel);