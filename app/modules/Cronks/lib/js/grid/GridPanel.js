
Ext.ns('Cronk.grid');

Cronk.grid.GridPanel = Ext.extend(Ext.grid.GridPanel, {
	meta : {},
	filter: {},
	
	initComponent : function() {
		this.tbar = this.buildTopToolbar();
		
		_G = this;
		
		if (this.store) {
			// Add nodata background
			this.store.on('datachanged', function(store) {
				if (store.getCount() == 0) {
					_G.getGridEl().child('div').addClass('x-icinga-nodata');
				}
				else {
					_G.getGridEl().child('div').removeClass('x-icinga-nodata');
				}
			});
		}		
		Cronk.grid.GridPanel.superclass.initComponent.call(this);
	},

	/*
	 * Top toolbar of the grid
	 */
	buildTopToolbar : function() {
		
		var autoRefresh = AppKit.getPrefVal('org.icinga.grid.refreshTime') || 300;
		var autoRefreshDefault = AppKit.getPrefVal('org.icinga.autoRefresh') && AppKit.getPrefVal('org.icinga.autoRefresh') != 'false';
		
		return new Ext.Toolbar({
			items: [{
				text: _('Refresh'),
				iconCls: 'icinga-icon-arrow-refresh',
				tooltip: _('Refresh the data in the grid'),
				handler: function(oBtn, e) { this.store.reload(); },
				scope: this
			}, {
				text: _('Settings'),
				iconCls: 'icinga-icon-cog',
				toolTip: _('Grid settings'),
				menu: {
					items: [{
						text: String.format(_('Auto refresh ({0} seconds)'), autoRefresh),
						checked: autoRefreshDefault,
						checkHandler: function(checkItem, checked) {
							if (checked == true) {
								this.trefresh = AppKit.getTr().start({
									run: function() {
										if(this.getStore())
											this.getStore().reload();
									},
									interval: (autoRefresh*1000),
									scope: this
								});
							}
							else {
								AppKit.getTr().stop(this.trefresh);
								delete this.trefresh;
							}	
						},
						scope: this
					},{
						text: _('Get this view as URL'),
						iconCls: 'icinga-icon-anchor',
						handler: function(oBtn,e) {
							var urlParams = this.extractGridParams();
							
							var win = new Ext.Window({
								renderTo:Ext.getBody(),
								modal:true,
								initHidden:false,
								width:500,
								autoHeight:true,
								padding:10,
								closeable:true,
								layout:'form',
								title:_('Link to this view'),
								items: {
									xtype:'textfield',
									fieldLabel: _('Link'),
									width:350,
									value: AppKit.util.Config.getBaseUrl()
									+ "/web/customPortal/"
									+ urlParams
								},
								bbar: [{
									text: _('Close'),
									iconCls: 'icinga-icon-close',
									handler: function(b, e) {
										win.close();
									}
								}]
						
							});
						},
						scope:this
					}]
				}
			}],
			listeners: {
				render: function(cmp) {
					if(autoRefreshDefault) {
						this.trefresh = AppKit.getTr().start({
							run: function() {
								if(this.getStore())
									this.getStore().reload();
							},
							interval: (autoRefresh*1000),
							scope: this
						});
					}
				},
				scope: this			
			}
		});
	},
	
	extractGridParams: function() {
		
		var store = this.store;
		var cronk = this.ownerCt.CronkPlugin.cmpConfig;
		var urlParams = "cr_base=";

		
		var counter = 0;						
		for(var i in store.baseParams) {
			var name = i.replace(/(.*?)\[(.*?)\]/g,"$1\|$2_"+counter);	
			urlParams += name+"="+store.baseParams[i]+";";
			counter++;
		}
		
		if(store.sortInfo) {
			urlParams +=
				"/groupDir="+store.sortInfo['direction']+"/"+
				"groupField="+store.sortInfo['field']+"/";
		} else {
			urlParams +=
				"/groupDir=ASC/"+
				"groupField=instance/";
		}
		
		if (Ext.isDefined(cronk.iconCls)) {
			urlParams +=
				"iconCls=" + cronk.iconCls + "/";
		}
		
		urlParams +=
			"template="+this.initialConfig.meta.params.template+"/"+
			"crname="+cronk.crname+"/"+
			"title="+cronk.title+"/";

		return urlParams;		
	},
	
	setMeta : function(m) {
		this.meta = m;
	},
	
	setFilter : function(f) {
		this.filter = f;
	}
	
});

Ext.reg('cronkgrid', Cronk.grid.GridPanel);
