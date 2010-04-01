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
		},
		
		clickGridLink : function(id, template, f, t) {
			var el = Ext.get(id)
			if (id && el) {
				el.addClass('icinga-link');
				el.on('click', (function() {
					var cronk = {
						parentid: 'click-grid-link-' + id, 
						title: (t || 'SUBGRID'),
						crname: "gridProc",
						closable: true,
						params: {template: template}
					};
					
					Ext.iterate(f, function(k, v) {
						delete(f[k]);
						f['f[' + k + '-value]'] = v;
						f['f[' + k + '-operator]'] = 50;
					});
					
					AppKit.Ext.util.InterGridUtil.gridFilterLink(cronk, f);
					
				}).createDelegate(this));
			}
		}
	};	
	
	return pub;
	
}();