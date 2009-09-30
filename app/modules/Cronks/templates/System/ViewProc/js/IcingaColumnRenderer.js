Ext.ns('AppKit.Ext.grid');

// This are the javascript methods available within
// the namespace
AppKit.Ext.grid.IcingaColumnRenderer = {
	subGrid : function(cfg) {
		return function(grid, rowIndex, colIndex, e) {
			var fieldName = grid.getColumnModel().getDataIndex(colIndex);
			if (fieldName == cfg.field) {
				
				var record = grid.getStore().getAt(rowIndex);
				var val = record.data[ cfg.sourceField ];
				var id = (cfg.idPrefix || 'empty') + 'subGridComponent';
				
				var cronk = {
					parentid: id,
					title: (cfg.titlePrefix || '') + " " + record.data[ cfg.labelField ],
					crname: 'gridProc',
					closable: true,
					params: { template: cfg.targetTemplate }
				};
				
				var filter = {};
				filter["f[" + cfg.targetField + "-value]"] = val;
				filter["f[" + cfg.targetField + "-operator]"] = 50;
				
				AppKit.Ext.util.InterGridUtil.gridFilterLink(cronk, filter);
			}
		}
	}
};
