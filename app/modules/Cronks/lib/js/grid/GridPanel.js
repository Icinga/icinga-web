
Ext.ns('Cronk.grid');

Cronk.grid.GridPanel = Ext.extend(Ext.grid.GridPanel, {
	meta : {},
	filter: {},
	
	initComponent : function() {
		this.tbar = this.buildTopToolbar();
		
		Cronk.grid.GridPanel.superclass.initComponent.call(this);
	},

	// Top toolbar of the grid
	buildTopToolbar : function() {
		return [{
			text: 'Refresh',
			iconCls: 'silk-arrow-refresh',
			tooltip: 'Refresh the data in the grid',
			handler: function(oBtn, e) { this.store.reload(); },
			scope: this
		}, {
			text: 'Settings',
			iconCls: 'silk-cog',
			toolTip: 'Grid settings',
			menu: {
				items: [{
					xtype: 'button',
					text: 'Auto refresh',
					iconCls: 'silk-database-refresh',
					enableToggle: true,
					handler: function(oBtn, e) {
						
						if (oBtn.pressed == true) {
							this.trefresh = AppKit.Ext.getTr().start({
								run: function() {
									this.getStore().reload();
								},
								interval: 120000,
								scope: this
							});
						}
						else {
							AppKit.Ext.getTr().stop(this.trefresh);
							delete this.trefresh;
						}
						
					},
					scope: this
				}]
			}
		}];
	},
	
	setMeta : function(m) {
		this.meta = m;
	},
	
	setFilter : function(f) {
		this.filter = f;
	}
	
});

Ext.reg('cronkgrid', Cronk.grid.GridPanel);
