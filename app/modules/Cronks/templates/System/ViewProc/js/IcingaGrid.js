
// ---
// KEEP THIS LINE
// ---


Ext.ns('AppKit.Ext.Widgets');
AppKit.Ext.Widgets.IcingaAjaxGridPanel = Ext.extend(Ext.grid.GridPanel, {
	meta : {},
	filter: {},
	
	initComponent : function() {
		this.tbar = this.buildTopToolbar();
		
		AppKit.Ext.Widgets.IcingaAjaxGridPanel.superclass.initComponent.call(this);
	},

	// Top toolbar of the grid
	buildTopToolbar : function() {
		return [{
			text: '<?php echo $tm->_("Refresh"); ?>',
			iconCls: 'silk-arrow-refresh',
			tooltip: '<?php echo $tm->_("Refresh the data in the grid"); ?>',
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