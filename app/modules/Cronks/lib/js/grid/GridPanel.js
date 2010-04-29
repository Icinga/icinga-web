
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
		return new Ext.Toolbar({
			items: [{
				text: _('Refresh'),
				iconCls: 'silk-arrow-refresh',
				tooltip: _('Refresh the data in the grid'),
				handler: function(oBtn, e) { this.store.reload(); },
				scope: this
			}, {
				text: _('Settings'),
				iconCls: 'silk-cog',
				toolTip: _('Grid settings'),
				menu: {
					items: [{
						// xtype: 'button',
						text: _('Auto refresh'),
						iconCls: 'silk-database-refresh',
						enableToggle: true,
						handler: function(oBtn, e) {
							
							if (oBtn.pressed == true) {
								this.trefresh = AppKit.getTr().start({
									run: function() {
										this.getStore().reload();
									},
									interval: 120000,
									scope: this
								});
							}
							else {
								AppKit.getTr().stop(this.trefresh);
								delete this.trefresh;
							}
							
						},
						scope: this
					}]
				}
			}]
		});
	},
	
	setMeta : function(m) {
		this.meta = m;
	},
	
	setFilter : function(f) {
		this.filter = f;
	}
	
});

Ext.reg('cronkgrid', Cronk.grid.GridPanel);
