
Ext.ns('Cronk.grid');

Cronk.grid.GridPanel = Ext.extend(Ext.grid.GridPanel, {
	meta : {},
	filter: {},
	
	initComponent : function() {
		this.addEvents('autorefreshchange','connectionmodify');
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
        this.on("connectionmodify",function(value) {
            if(this.connectionComboBox)
                this.connectionComboBox.selectByValue(value);
        },this)
	
	},
    selectedConnection: 'icinga',

    getConnectionComboBox: function() {
        var connArr = this.initialConfig.meta.connections;
        for(var i=0;i<connArr.length;i++)
            connArr[i] = [connArr[i]];

        this.connectionComboBox = new Ext.form.ComboBox({
            store: new Ext.data.ArrayStore({
                autoDestroy: true,
                fields: ['connection'],
                data : connArr
            }),
            displayField: 'connection',
            typeAhead: true,
            mode: 'local',
            forceSelection: true,
            defaultValue: this.selectedConnection,
            triggerAction: 'all',
            emptyText: this.selectedConnection,
            selectOnFocus: true,
            width: 135,
            listeners: {
                afterrender: function(me) {
                   
                },
                select: function(me,record) {
                    this.setConnection(record.get("connection"));

                    this.getStore().setBaseParam("connection",this.selectedConnection);
                    this.refreshGrid();
                },
                scope:this
            },

            getListParent: function() {
                return this.el.up('.x-menu');
            },
            iconCls: 'no-icon' //use iconCls if placing within menu to shift to right side of menu
        });
        return this.connectionComboBox;
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
				handler: function(oBtn, e) {this.store.load();},
				scope: this
			}, {
				text: _('Settings'),
				iconCls: 'icinga-icon-application-edit',
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
								buttons: [{
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
	
	stateEvents: [
	   'autorefreshchange','activate', 'columnmove ', 'columnresize', 
	   'groupchange', 'sortchange', 'afterrender','connectionmodify'
	],
	
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
		o = {
			groupField : null,
			columns : []
		};
		
		if (Ext.isDefined(this.store.groupField)) {
			o.groupField = this.store.getGroupState()
			o.groupDir = this.store.groupDir;
			o.groupOnSort = this.store.groupOnSort;
		}
		
		Ext.iterate(this.colModel.lookup, function(colId, col) {
			if (Ext.isEmpty(col.dataIndex) === false) {
				var colData = {}
				Ext.copyTo(colData, col, [
					'hidden',
					'width',
					'dataIndex',
					'id',
					'sortable'
				]);
				o.columns.push(colData);
			}
		}, this);
		
		return o;
	},
	
	applyPersistentColumnModel : function(data) {
		var cm = this.colModel;
        
        if (Ext.isArray(data.columns)) {
        	Ext.each(data.columns, function(item, index) {
        		if (Ext.isDefined(item.dataIndex)) {
        			var ci = cm.findColumnIndex(item.dataIndex);
        			if (ci>0) {
        				var org = cm.getColumnById(ci);
        				if (Ext.isDefined(org)) {
        					
        					if (Ext.isDefined(data.groupField) && data.groupField === org.dataIndex) {
        						cm.setHidden(org.id, false);
        					} else {
                                cm.setHidden(org.id, item.hidden);
        					}
        					
                            cm.setColumnWidth(org.id, item.width)
        				}
        			}
        		}
        	}, this);
        }
        
        if (Ext.isDefined(data.groupField) && Ext.isDefined(this.store.groupBy)) {
        	this.store.on('beforeload', function() {
	            (function() {
                   
                    var dir = Ext.isEmpty(data.groupDir) ? 'ASC' : data.groupDir;
		            
		            if (Ext.isDefined(data.groupOnSort)) {
		                this.store.groupOnSort = data.groupOnSort 
		            }
		            
		            this.store.groupBy(data.groupField, true, dir);
		            this.store.reload();
	            }).defer(50, this);
	            return false;
        	}, this, {single : true});
        };

      

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
            nativeState: Ext.grid.GridPanel.prototype.getState.apply(this),
			filter_params: this.filter_params || {},
			filter_types: this.filter_types || {},
			store_origin_params: ("originParams" in store) ? store.originParams : {},
            sortToggle: store.sortToggle,
            sortInfo: store.sortInfo,
			colModel: this.getPersistentColumnModel(),
			autoRefresh: aR,
            connection: this.store.baseParams["connection"]
		};
		return o;
	},
	
	applyState: function(state) {
		if (!Ext.isObject(state)) {
			return false;
		}

		var reload = false;
		var store = this.getStore();
		if (Ext.isObject(state.colModel)) {
			this.applyPersistentColumnModel(state.colModel);
		}
		
		if (state.filter_types) {
			this.filter_types = state.filter_types;
		}
		if (state.sortToggle) {
			store.sortToggle = state.sortToggle;
		}
		if (state.sortInfo && Ext.isDefined(state.sortInfo.field)) {
			var direction = Ext.isDefined(state.sortInfo.direction) ? state.sortInfo.direction : 'ASC';
			store.sort(state.sortInfo.field, state.sortInfo.direction);
		}	
		
		if (state.groupOnSort) {
			store.groupOnSort = state.groupOnSort;
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
        
        if (state.connection) {
            this.setConnection(state.connection);

        }
		if(Ext.isObject(state.nativeState))
    		return Ext.grid.GridPanel.prototype.applyState.call(this,{columns: state.nativeState.columns});
        return true;
	},
	
    setConnection: function(connection) {
        this.selectedConnection = connection;
        if(typeof this.connectionComboBox !== "undefined")
            this.connectionComboBox.selectByValue(connection);

        this.getStore().setBaseParam("connection",this.selectedConnection);
        this.fireEvent("connectionmodified");
    },

	applyParamsToStore : function(params) {
		for (var i in params) {
            if(i == "connection") {
                this.setConnection(params[i]);
            }
			this.store.setBaseParam(i, params[i]);
		}
	}
	

});

Ext.reg('cronkgrid', Cronk.grid.GridPanel);
