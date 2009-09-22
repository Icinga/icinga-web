Ext.ns('AppKit.Ext.grid');

// This are the javascript methods available within
// the namespace
AppKit.Ext.grid.IcingaColumnRenderer = {

	bogusRenderer : function(cfg) {
		return function(value, metaData, record, rowIndex, colIndex, store) {
			return 'BOGUS';
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
	
	servicesForHost : function(cfg) {
		return function(grid, rowIndex, colIndex, e) {
			var fieldName = grid.getColumnModel().getDataIndex(colIndex);
			if (fieldName == cfg.field) {
				
				var record = grid.getStore().getAt(rowIndex);
				var val = record.data[ cfg.sourceField ];
				
				
				var tabs = Ext.getCmp('cronk-tabs');
				
				var id="servicesForHostPanel";
				
				var panel=Ext.getCmp(id);
				
				if (!panel) {
					panel = AppKit.Ext.createCronk({
						htmlid: id,
						title: 'Services for host',
						crname: 'gridProc',
						closable: true,
						layout: 'fit',
						params: { template: cfg.targetTemplate }
					});					
					tabs.add(panel);
					
					panel.on('add', function(p, c, i) {
						if (i==0 && (c.getXType() == 'grid' || c.getXType() == 'icingagrid')) {
							
							var store = c.getStore();
							
							store.setBaseParam('f[' + cfg.targetField + '-value]', val);
							store.setBaseParam('f[' + cfg.targetField + '-operator]', 50);
							
							store.reload();
							
						}
					});
					
				}
				else {
					grids = panel.findByType('icingagrid');
					if (grids[0]) {
						grids[0].getStore().setBaseParam('f[' + cfg.targetField + '-value]', val);
						grids[0].getStore().setBaseParam('f[' + cfg.targetField + '-operator]', 50);
						
						grids[0].getStore().reload();
					}
				}
				
				panel.setTitle('Services for ' + (record.data[ cfg.labelField ] || 'UNKNOWN'));
				tabs.setActiveTab(panel);
				
				tabs.doLayout();
				Ext.getCmp('view-container').doLayout();
			}
		}
	}
	
};