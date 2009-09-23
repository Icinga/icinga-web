Ext.ns('AppKit.Ext.util');

AppKit.Ext.util.InterGridUtil = function(){
	
	var pub = {
		
		gridFilterLink : function(config, baseParams) {
			var tabs = Ext.getCmp('cronk-tabs');
			var id = config.parentid || null;
			var panel = Ext.getCmp(id);
			
			if (!panel) {
				
				panel = AppKit.Ext.CronkMgr.create(config);					
				tabs.add(panel);
				
				panel.on('add', function(p, c, i) {
					if (i==0 && (c.getXType() == 'grid' || c.getXType() == 'appkit-grid')) {
						Ext.iterate(baseParams, function(k,v) {
							c.getStore().setBaseParam(k, v);
						});
						c.getStore().reload();
					}
				});
			}
			else {
				grids = panel.findByType('appkit-grid');
				if (grids[0]) {
					Ext.iterate(baseParams, function(k,v) {
					grids[0].getStore().setBaseParam(k, v);
					});
					grids[0].getStore().reload();
				}
			}
			
			panel.setTitle(config.title);
			tabs.setActiveTab(panel);
			
			tabs.doLayout();
			Ext.getCmp('view-container').doLayout();
		}
	};	
	
	return pub;
	
}();