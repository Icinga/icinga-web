Ext.ns('AppKit.Ext.util');

AppKit.Ext.util.InterGridUtil = function(){
	
	var applyParametersToGrid = function(baseParams, c) {
		if ((c.getXType() == 'grid' || c.getXType() == 'appkit-grid')) {
			
			var store = c.getStore();
			if (!"originParams" in store || typeof(store.originParams) == "undefined") {
				store.originParams = {};
			}
			
			Ext.iterate(baseParams, function(k,v) {
				store.originParams[k] = v;
				store.setBaseParam(k, v);
			});
			
			c.getStore().reload();
		}
	};
	
	var pub = {
		
		gridFilterLink : function(config, baseParams) {
			var tabs = Ext.getCmp('cronk-tabs');
			var id = config.parentid || null;
			var panel = Ext.getCmp(id);
			
			// disable grid autoload
			config.params['storeDisableAutoload'] = 1;
			
			if (!panel) {
				
				panel = AppKit.Ext.CronkMgr.create(config);					
				tabs.add(panel);
				
				panel.on('add', function(p, c, i) {
					applyParametersToGrid(baseParams, c)
				});
			}
			else {
				grids = panel.findByType('appkit-grid');
				if (grids[0]) {
					applyParametersToGrid(baseParams, grids[0]);
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