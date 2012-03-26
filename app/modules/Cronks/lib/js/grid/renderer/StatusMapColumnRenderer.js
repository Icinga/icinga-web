Ext.ns('Cronk.grid');

Cronk.grid.StatusmapColumnRenderer = {
	
	CRONK_STATUSMAP_ID : 'cronkStatusmapInlineHostTarget',
	CRONK_STATUSMAP_NAME : 'icingaStatusMap',
	CRONK_STATUSMAP_ICON : 'icinga-cronk-icon-footprint',
	
	hostToStatusmap : function(cfg) {
		
		var tabPanel = Ext.getCmp('cronk-tabs');
		return function(grid, rowIndex, colIndex, e) {
			
			var fieldName = grid.getColumnModel().getDataIndex(colIndex);
            if (fieldName == cfg.field) {
            	var record = grid.getStore().getAt(rowIndex);
            	var value = record.data[cfg.field];
            	var hostname = record.data[cfg.hostname_field || 'host_name'] || '*NOT FOUND*';
				var statusMap = Ext.getCmp(Cronk.grid.StatusmapColumnRenderer.CRONK_STATUSMAP_ID);
				
				if (Ext.isEmpty(statusMap)) {
					var newCronk = {
						id : Cronk.grid.StatusmapColumnRenderer.CRONK_STATUSMAP_ID,
						crname : Cronk.grid.StatusmapColumnRenderer.CRONK_STATUSMAP_NAME,
						closable : true,
						iconCls : Cronk.grid.StatusmapColumnRenderer.CRONK_STATUSMAP_ICON

					}
					
					statusMap = Cronk.factory(newCronk);
					tabPanel.add(statusMap);
				}
				
				statusMap.setTitle(String.format('Map {0} centered', hostname));

				statusMap.on('activate', function(cronk) {
					var map = Cronk.Registry.get(Cronk.grid.StatusmapColumnRenderer.CRONK_STATUSMAP_ID).local.statusmap;
                    map.centerNodeByObjectId(value);
				}, null, { delay : 500, single : true }); // Wait to let the map expose
				
	            tabPanel.setActiveTab(statusMap);
            
            }
		}
	}
	
};