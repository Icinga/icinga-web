Ext.ns('AppKit.Ext.grid');

// This are the javascript methods available within
// the namespace
AppKit.Ext.grid.IcingaColumnRenderer = {

	bogusRenderer : function(value, metaData, record, rowIndex, colIndex, store) {
		var cfg = this.getConfig('bogusRenderer');
		return (cfg.prefixText || "NO TEXT: ") + value;
	},
	
	truncateText : function(value, metaData, record, rowIndex, colIndex, store) {
		var cfg = this.getConfig('truncateText');
		var out = Ext.util.Format.ellipsis(value, (cfg.length || 50));
		if (out.indexOf('...', (out.length-3)) != -1) {
			metaData.attr = 'ext:qtip="' + value + '"';
		}
		
		return out;
	},
	
	servicesForHost : function(grid, rowIndex, colIndex, e) {
		var cfg = this.getConfig('servicesForHost');
		var fieldName = grid.getColumnModel().getDataIndex(colIndex);
		
		if (fieldName == cfg.field) {
			alert('hostname clicked');
		}
	}
	
};

// Copy the configure methods into our namespace
Ext.apply(AppKit.Ext.grid.IcingaColumnRenderer, AppKit.Ext.grid.ConfigurableColumnRenderer);