Ext.ns('AppKit.Ext.grid');

// This are the javascript methods available within
// the namespace
AppKit.Ext.grid.IcingaColumnRenderer = {

	bogusGroupRenderer : function(cfg) {
		return function(value, garbage, record, rowIndex, colIndex, store) {
			return "GROUP: " + v;
		}
	},
	
	truncateText : function(cfg) {
		return function(value, metaData, record, rowIndex, colIndex, store) {
			var out = Ext.util.Format.ellipsis(value, (cfg.length || 50));
			if (out.indexOf('...', (out.length-3)) != -1) {
				metaData.attr = 'ext:qtip="' + value + '"';
			}
			
			return out;
		}
	},
	
	columnStyle : function(cfg) {
		return function(value, metaData, record, rowIndex, colIndex, store) {
			metaData.attr = 'style="' + cfg.style + '"';
			return value;
		}
	},
	
	columnElement : function(cfg) {
		return function(value, metaData, record, rowIndex, colIndex, store) {
			Ext.apply(metaData, cfg);
			
			if (cfg.value) {
				return cfg.value;
			}
			
			if (cfg.noValue != true) {
				return value;
			}
		}
	},
	
	columnImage : function(cfg) {
		return function(value, metaData, record, rowIndex, colIndex, store) {
			if (cfg.style) {
				metaData.attr += ' style="' + cfg.style + '"';
			}
			
			return String.format('<img src="/appkit/image/{0}" />', cfg.image);
		}
	},
	
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
