
Ext.ns('Cronk.grid');

Cronk.grid.GridPanel = Ext.extend(Ext.grid.GridPanel, {
	meta : {},
	filter: {},
	
	initComponent : function() {
		this.addEvents('autorefreshchange');	
		this.autoRefreshEnabled = null;
		this.tbar = this.buildTopToolbar();
		
		_G = this;
		
		if (this.store) {
			// Add nodata background
			this.store.on('datachanged', function(store) {
				if (store.getCount() == 0) {
					if(_G.getGridEl())
						_G.getGridEl().child('div').addClass('x-icinga-nodata');
				}
				else {
					if(_G.getGridEl())
						_G.getGridEl().child('div').removeClass('x-icinga-nodata');
				}
			});
		}			
		Cronk.grid.GridPanel.superclass.initComponent.call(this);
		
		this.on("show",function() {
			if(this.autoRefreshEnabled) {
				this.startRefreshTimer();
			}
		},this);
	
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
				handler: function(oBtn, e) { this.store.load(); },
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
								this.startRefreshTimer();	
							} else {
								this.stopRefreshTimer();	
							}	
						},
						listeners: {
							render: function(btn) {
								if(this.autoRefreshEnabled !== null)
									btn.setChecked(this.autoRefreshEnabled,true);
								this.on("autorefreshchange",function(v) {			
									btn.setChecked(v,true);
								});
							},
							scope:this

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
									+ "/modules/web/customPortal/"
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
					if(autoRefreshDefault && this.autoRefreshEnabled === null) {	
						this.startRefreshTimer();
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
	},
	stateEvents: ['autorefreshchange','activate', 'columnmove ', 'columnresize', 'groupchange', 'sortchange'],
	
	startRefreshTimer: function() {
		var autoRefresh = AppKit.getPrefVal('org.icinga.grid.refreshTime') || 300;
		this.stopRefreshTimer();
		
		this.trefresh = AppKit.getTr().start({
			run: function() {
				this.refreshGrid();
			},
			interval: (autoRefresh*1000),
			scope: this
		});
		this.autoRefreshEnabled = true;
		this.fireEvent('autorefreshchange',true);
	},

	stopRefreshTimer: function(noVisualUpdate) {
		if(this.trefresh) {
			AppKit.getTr().stop(this.trefresh);
			delete this.trefresh;
		}
		this.autoRefreshEnabled = false;
		if(!noVisualUpdate) {
			this.fireEvent('autorefreshchange',false);
		}
	
	},
	getPersistentColumnModel : function() {
		
		o = {};
		Ext.iterate(this.colModel.config, function(col, colId) {
			o[colId] = {};
			Ext.copyTo(o[colId], col, [
				'hidden',
				'width',
				'dataIndex',
				'id',
				'sortable'
			]);
		}, this);
		
		return o;
	},
	
	applyPersistentColumnModel : function(data) {
		var cm = this.colModel;
		
		Ext.iterate(data, function(colId, col) {
			
			if (Ext.isDefined(col.dataIndex)){
				var org = cm.getColumnById(colId);
				
				// Column was not moved arropund
				if (Ext.isDefined(org) && org.dataIndex == col.dataIndex) {
					cm.setHidden(colId, col.hidden);
					cm.setColumnWidth(colId, col.width);
				}
			}
			
		}, this);
	},
	refreshTask: new Ext.util.DelayedTask(function() {
		//NOTE: hidden tabs won't be refreshed
	
		if(!this.store || this.ownerCt.hidden)
			return true;
		if(Ext.isFunction((this.getTopToolbar() || {}).doRefresh)) {
			this.getTopToolbar().doRefresh();
		} else if(Ext.isFunction((this.getBottomToolbar() || {}).doRefresh)) {
			this.getBottomToolbar().doRefresh();
		} else if(this.getStore()) {	
			this.getStore().reload();
		}
	}),

	refreshGrid: function() {
		this.refreshTask.delay(200,null,this);
	},
	getState: function() {
		var store = this.getStore();
		var aR = null;
		if(this.autoRefreshEnabled === true)
			aR = 1;
		if(this.autoRefreshEnabled === false)
			aR = -1;
		
		var o = {
			filter_params: this.filter_params || {},
			filter_types: this.filter_types || {},
			store_origin_params: ("originParams" in store) ? store.originParams : {},
			colModel: this.getPersistentColumnModel(),
			autoRefresh: aR
		};
		
		return o;
	},
	
	applyState: function(state) {
		var reload = false;
		var store = this.getStore();
		
		if (Ext.isObject(state.colModel)) {
			this.applyPersistentColumnModel(state.colModel);
		}
		
		if (state.filter_types) {
			this.filter_types = state.filter_types;
		}
		
		if (state.store_origin_params) {
			store.originParams = state.store_origin_params;
			this.applyParamsToStore(store.originParams, store);
			reload = true;
		}
		
		if (state.filter_params) {
			this.filter_params = state.filter_params;
			this.applyParamsToStore(this.filter_params, store);
			reload = true;
		}
		
		if (state.autoRefresh == 1) {
			this.startRefreshTimer();
		} else if (state.autoRefresh == -1) {
			this.stopRefreshTimer();
		}

		if (reload == true) {
			this.refreshGrid();
		}
					
		return true;
	},
	
	applyParamsToStore : function(params) {
		for (var i in params) {
			this.store.setBaseParam(i, params[i]);
		}
	}
	


});

Ext.reg('cronkgrid', Cronk.grid.GridPanel);
