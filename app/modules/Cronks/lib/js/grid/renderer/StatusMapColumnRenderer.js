// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}

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