Ext.ns('Cronks.util');

Cronks.util.CategoryEditor = Ext.extend(Ext.Window, {
	title: _('Category editor'),
	
	closeAction: 'hide',
	height: 400,
	resizable: false,
	layout: 'fit',
	
	constructor : function(cfg) {
		Cronks.util.CategoryEditor.superclass.constructor.call(this, cfg);
	},
	
	initComponent : function() {
		
		Cronks.util.CategoryEditor.superclass.initComponent.call(this);
		
		this.grid = this.buildGrid();
		
		this.add(this.grid);
		
		this.on('beforeshow', function(w) {
			w.grid.getStore().reload();
		}, this);
	},
	
	buildGrid : function() {
		
		var systemRenderer = function(value, o, record, rowIndex, colIndex, store) {
			if (value == true) {
				o.css = 'icinga-icon-shield';
			}
			else {
				o.css = 'icinga-icon-user';
			}
			return '';
		};
		
		var writer = new Ext.data.JsonWriter({
			encode: true,
			encodeDelete: true,
			writeAllFields: true
		});
		
		var editor = new Ext.form.TextField();
		
		var booleanEditor = new Ext.form.ComboBox({
			typeAhead: true,
			triggerAction: 'all',
			lazyRender:true,
			mode: 'local',
			store: new Ext.data.ArrayStore({
			    id: 0,
			    fields: [
			        'value',
			        'label'
			    ],
			    data: [[false, 'false'], [true, 'true']]
			}),
			valueField: 'value',
			displayField: 'label'
		});
		
		var grid = new (Ext.extend(Ext.grid.EditorGridPanel, {
			width: 540,
			height: 400,
			
			selModel: new Ext.grid.RowSelectionModel({
				singleSelect: true
			}),
			
			store: new Ext.data.JsonStore({
				url: AppKit.c.path + '/cronks/provider/categories',
				writer: writer,
				autoLoad: true,
				autoSave: true,
				paramsAsHash: true,
				baseParams: {
					all: 1,
					invisible: 1
				},
				listeners: {
					write: function(store, action, result, transaction, record) {
						store.reload();
					}
				}
			}),
			
			colModel: new Ext.grid.ColumnModel({
				defaults: {
					sortable: false
				},
				
				columns: [{
					header: _('CatId'),
					dataIndex: 'catid',
					width: 100,
					fixed: true
				}, {
					header: _('Title'),
					dataIndex: 'title',
					editor: editor
				}, {
					header: "",
					dataIndex: 'system',
					width: 16,
					renderer: systemRenderer,
					fixed: true
				}, {
					header: _('Visible'),
					dataIndex: 'visible',
					editor: booleanEditor,
					width: 80,
					fixed: true
					
				}, {
					header: _('Position'),
					dataIndex: 'position',
					editor: editor,
					width: 80,
					fixed: true
				}, {
					header: _('Cronks'),
					dataIndex: 'count_cronks',
					width: 60,
					fixed: true
				}]
			}),
			
			viewConfig: {
				forceFit: true
			},
			
			bbar: [{
				text: _('Reload'),
				iconCls: 'icinga-icon-database-refresh',
				handler: function(b, e) {
					this.grid.getStore().reload();
				},
				scope: this
			}, '-', {
				text: _('Add'),
				iconCls: 'icinga-icon-add',
				handler: function(b, e) {
					
					Ext.Msg.prompt('New category', 'Please enter CatId:', function(btn, text){
						if (btn == 'ok'){
							var record = new this.grid.store.recordType({
								id: this.grid.store.getCount()+1,
								catid: String(text).toLowerCase(),
								title: String(text),
								system: false,
								position: 0,
								visible: true
							});
							
							this.grid.store.addSorted(record);
						}
					}, this);

				},
				scope: this
			}, {
				text: _('Delete'),
				iconCls: 'icinga-icon-delete',
				handler: function(b, e) {
					var record = this.grid.getSelectionModel().getSelected();
					
			        if (!record) {
			            return false;
			        }
			        
			        if (record.data.system == true) {
			        	AppKit.notifyMessage(_('Error'), _('You can not delete system categories'));
			        	return false;
			        }
			        
			        this.grid.store.remove(record);
				},
				scope: this
			}, '-', {
				text: _('OK'),
				iconCls: 'icinga-icon-accept',
				handler: function(b, e) {
					this.grid.store.save();
					this.hide();
				},
				scope: this
			}]
		}));
		
		grid.on('beforeedit', function(e) {
			if (e.record.data.system == true) {
				AppKit.notifyMessage(_('Error'), _('System categories are not editable!'));
				return false;
			}
		}, this);
		
		return grid;
	}
});

Cronk.util.CronkListingPanel = function(c) {
	
	var CLP = this;
	
	Cronk.util.CronkListingPanel.superclass.constructor.call(this, c);
	
	this.stores = {};
	
	this.cronks = {};
	
	this.categories = {};
	
	this.default_act = -1;
	
	this.template = new Ext.XTemplate(
	    '<tpl for=".">',
	    	'<div class="{statusclass}" id="{name}">',
	    	'<div class="cronk-status-icon">',
        	'<div class="thumb"><img ext:qtip="{description}" src="{image}"></div>',
        	'<span class="x-editable">{name}</span>',
        	'</div>',
        	'</div>',
	    '</tpl>',
	    '<div class="x-clear"></div>'
	);
	
	this.loadData = function(url) {
		
		var mask = null;
		
		if (this.getEl()) {
			mask = new Ext.LoadMask(this.getEl(), {msg: _('Loading Cronks ...')});
			mask.show();
		}
		
		Ext.Ajax.request({
			url: url,
			callback: function(o,s,r) {
				if (mask)
					mask.hide();
					delete(mask);
			},
			success: function(r, o) {
				var data = Ext.decode(r.responseText);
				if (Ext.isDefined(data.categories) && Ext.isDefined(data.cronks)) {
					
					CLP.categories = data.categories;
					
					CLP.cronks = data.cronks;
					
					var i = 0;
					
					Ext.each(data.categories, function(item, index, arry) {
						if (Ext.isDefined(data.cronks[item.catid])) {
							if (this.getStore(item.catid)) {
								fillStore(item.catid, data.cronks[item.catid]);
							}
							else {
								fillStore(item.catid, data.cronks[item.catid]);
								createView(item.catid, item.title);
								
								if (Ext.isDefined(item.active) && item.active == true) this.default_act=i;
								
							}
							
							i++;
							
						}
					}, this);
					
					AppKit.util.Layout.doLayout(null, 200);
				}
			},
			failure: function (r, o) {
				var str = String.format(
					_('Could not load the cronk listing, following error occured: {0} ({1})'),
					r.status,
					r.statusText
				);
				
				parentcmp.add({
					layout: 'fit',
					html: str
				});
				parentcmp.doLayout();
				
				AppKit.notifyMessage('Ajax Error', str, { waitTime: 20 });
			},
			scope: CLP
		});
		
	};
	
	var fillStore = function(storeid, data) {
		
		if (Ext.isEmpty(CLP.stores[storeid])) {
			CLP.stores[storeid] = new Ext.data.JsonStore({
				autoDestroy: true,
				autoLoad: false,
			    root: 'rows',
			    idProperty: 'cronkid',
			    fields: [
			        'name', 'cronkid', 'description', 
			        'module', 'action', 'system', 'owner',
			        'categories', 'groupsonly', 'state',
			        {
						name: 'image_id',
						convert: function(v,record) {
							return record.image;
						}
					}, {
						name:'ae:parameter',
						convert:function(v,record) {
							if(!Ext.isObject(v))
								return v;
							for(var i in v) {
								if(Ext.isObject(v[i]))
									v[i] = Ext.encode(v[i]);
							}
							return v;
						}
					}, {
						name: 'image',
						convert: function(v, record){
							return AppKit.util.Dom.imageUrl(v);
						}
					}, {
						name: 'statusclass',
						convert: function(v, record) {
							var cls = 'cronk-preview';
							
							if (record.owner == true) {
								cls += ' cronk-item-owner';
							}
							
							if (record.system == true) {
								cls += ' cronk-item-system';
							}
							
							if (!record.system && !record.owner) {
								cls += ' cronk-item-shared';
							}
							
							return cls;
						}
					}
			    ]
			});
		}
		
		var store = CLP.stores[storeid];
		
		store.loadData(data);
	}
	
	var createView = function(storeid, title) {
		
		var store = CLP.getStore(storeid);
		
		CLP.add({
			title: String.format('{0} ({1})', title, store.getCount()),
			autoScroll:true,
			
			/*
			 * Bubbeling does not work because it collapse the 
			 * parent panel all the time
			 */
			listeners: {
				collapse: function(panel) {
					CLP.saveState();
				}
			},
			
			items: new Ext.DataView({
		        store: store,
		        tpl: CLP.template,
		        overClass:'x-view-over',
		        itemSelector:'div.cronk-preview',
		        emptyText: 'No data',
		       	cls: 'cronk-data-view',
		        border: false,
		        
		        // Create the drag zone
		        listeners: {
		        	render: CLP.initCronkDragZone.createDelegate(CLP),
		        	click: CLP.dblClickHandler.createDelegate(CLP),
		        	contextmenu: CLP.handleContextmenu.createDelegate(CLP)
		        } 
		    }),
			border: false
		});
		
	}
	
	this.loadData(this.combinedProviderUrl);
	
	var act = false;
	
	CLP.on('afterrender', function() {
		if (!CLP.applyActiveItem() && this.default_act >= 0) {
			CLP.setActiveItem(this.default_act);
		}
	});
	
	var cb = Cronk.util.CronkBuilder.getInstance();
	
	cb.addListener('writeSuccess', function() {
		CLP.reloadAll();
	});
}

Ext.extend(Cronk.util.CronkListingPanel, Ext.Panel, {
	layout: 'accordion',
	layoutConfig: {
		animate: true,
		renderHidden: false,
		hideCollapseTool: true,
		fill: true
	},
	
	autoScroll: true,
	border: false,
	
	defaults: { border: false },
	
	stateful: true,
	
	stateEvents: ['collapse'],
	stateful: true,
	bubbleEvents: [],
	
	tbar: [{
		iconCls: 'icinga-icon-arrow-refresh',
		text: _('Reload'),
		handler: function(b, e) {
			var p = Ext.getCmp('cronk-listing-panel');
			p.reloadAll();
		}
	}, {
		text: _('Settings'),
		iconCls: 'icinga-icon-cog',
		menu: [{
			text: _("Tab slider"),
			checked: false,
			checkHandler: function(checkItem, checked) {
				
				var refresh = AppKit.getPrefVal('org.icinga.tabslider.changeTime') || 60;
				
				var tp = Ext.getCmp('cronk-tabs');
				
				if (checked == true) {
					if (Ext.isDefined(this.sliderTask)) {
						AppKit.getTr().stop(this.sliderTask);
					}
					
					this.sliding_tab = tp.getActiveTabIndex();
					
					this.sliderTask = {
						run: function() {
							this.sliding_tab++;		
							if (this.sliding_tab >= tp.items.getCount()) {
								this.sliding_tab = 0;
							}
							
							tp.setActiveTab(this.sliding_tab);
						},
						interval: (refresh * 1000),
						scope: this
					}
					
					AppKit.getTr().start(this.sliderTask);
				}
				else {
					AppKit.getTr().stop(this.sliderTask);
				}
				
			},
			scope: this
		}]	
	}],
	
	applyState: function(state) {
		if (!Ext.isEmpty(state.active_tab) && state.active_tab >= 0) {
			this.active_tab = state.active_tab;
		}
	},
	
	getState: function() {
		var active = this.getLayout().activeItem, i;
		this.items.each(function(item, index, l) {
			if (item == active) {
				i = index;
			}
		});
		
		if (typeof(i) !== "undefined" && i>=0) {
			return { active_tab: i }
		}
	},
	
	setCategoryAdmin: function(grant) {
		
		if (grant == true) {
			this.isCategoryAdmin = true;
		}
		else {
			this.isCategoryAdmin = false;
		}
		
		if (this.isCategoryAdmin == true) {
			this.getTopToolbar().insert(1, {
				text: _('Categories'),
				iconCls: 'icinga-icon-category',
				handler: function(b, e) {
					this.showCategoryEditor(b.getEl());
				},
				scope: this
			})
		}
	},
	
	showCategoryEditor: function(where) {
		if (this.isCategoryAdmin !== true) {
			return false;
		}
		
		if (!Ext.isDefined(this.categoryEditor)) {
			this.categoryEditor = new Cronks.util.CategoryEditor({
				id: this.id + '-category-editor'
			});
		}
		
		if (this.categoryEditor.isVisible()) {
			this.categoryEditor.hide(where);
		}
		else {
			this.categoryEditor.show(where);
		}
		
	},
	
	getStore : function(storeid) {
		if (Ext.isDefined(this.stores[storeid])) {
			return this.stores[storeid];
		}
	},
	
	dblClickHandler: function(oView, index, node, e) {
		var record = oView.getStore().getAt(index);
		
		var tabPanel = Ext.getCmp('cronk-tabs');
	
		if (tabPanel) {
			var cronk = {
				xtype: 'cronk',
				iconCls: Cronk.getIconClass(record.data['image_id']),
				title: record.data['name'],
				crname: record.data.cronkid,
				closable: true,	
				params: Ext.apply({}, record.data['ae:parameter'], { 
					module: record.data.module, 
					action: record.data.action 
				})
			};
			Cronk.util.InterGridUtil.gridFilterLink(cronk, {});
		}
	},
	
	initCronkDragZone : function (v) {
		v.dragZone = new Ext.dd.DragZone(v.getEl(), {
			ddGroup: 'cronk',
			
			getDragData: function(e) {
			var sourceEl = e.getTarget(v.itemSelector, 10);
	
	            if (sourceEl) {
	                d = sourceEl.cloneNode(true);
	                d.id = Ext.id();
	                return v.dragData = {
	                    sourceEl: sourceEl,
	                    repairXY: Ext.fly(sourceEl).getXY(),
	                    ddel: d,
	                    dragData: v.getRecord(sourceEl).data
	                }
	
	            }
			
			},
			
			getRepairXY: function() {
				return this.dragData.repairXY;
			}
		
		});
	},
	
	setActiveItem : function(id) {
		this.getLayout().setActiveItem(id);
	},
	
	applyActiveItem : function() {
		var c = this
		if (!Ext.isEmpty(c.active_tab)) {
			c.getLayout().setActiveItem(c.active_tab);
			return true;
		}
		return false;
	},
	
	getContextmenu : function() {
		
		var idPrefix = this.id + '-context-menu';
		
		if (!Ext.isDefined(this.contextmenu)) {
			var ctxMenu = new Ext.menu.Menu({
				
				setItemData : function(view, index, node) {
					this.ctxView = view;
					this.ctxIndex = index;
					this.ctxNode = node;
				},
				
				getItemRecord : function() {
					return this.ctxView.getStore().getAt(this.ctxIndex);
				},
				
				getItemData : function() {
					var r = this.getItemRecord();
					if (Ext.isDefined(r.data)) {
						return r.data;
					}
				},
				
				getListing : function() {
					return this.listing
				},
				
				id: idPrefix,
				
				items: [{
					id: idPrefix + '-button-edit',
					text: _('Edit'),
					iconCls: 'icinga-icon-pencil',
					handler: function(b, e) {
						var cb = Cronk.util.CronkBuilder.getInstance();
						
						if (Ext.isObject(cb)) {
							cb.show(b.getEl());
							cb.setCronkData(ctxMenu.getItemData());
						}
						else {
							AppKit.notifyMessage(_('Error'), _('CronkBuilder has gone away!'));
						}
					}
				}, {
					id: idPrefix + '-button-delete',
					text: _('Delete'),
					iconCls: 'icinga-icon-bin',
					handler: function(b, e) {
						var item = ctxMenu.getItemData();
						Ext.Msg.confirm(_('elete cronk'), String.format(_('Are you sure to delete {0}'), item['name']), function(btn) {
							if (btn == 'yes') {
								Ext.Ajax.request({
									url: AppKit.c.path + '/cronks/provider/cronks',
									params: {
										xaction: 'delete',
										cid: item.cronkid,
										name: item.name,
										categories: item.categories,
										description: item.description,
										image: item.image,
										module: item.module,
										action: item.action
									},
									success: function(response, options) {
										AppKit.notifyMessage(_('Cronk deleted'), String.format(_('We have deleted your cronk "{0}"'), item['name']));
										
										ctxMenu.getListing().reloadAll();
									},
									failure: function(response, options) {
										var o = Ext.decode(response.responseText);
										if (Ext.isObject(o) && Ext.isDefined(o.errors)) {
											AppKit.notifyMessage(_('Error'), String.format(_('Some error: {0}'), o.errors[0]));
										}
									}
								});
							}
						});
					}
				}],
				
				listeners: {
					show: function(ctxm) {
						if (this.getItemData().system == true || this.getItemData().owner == false) {
							this.items.get(idPrefix + '-button-edit').setDisabled(true);
							this.items.get(idPrefix + '-button-delete').setDisabled(true);
						}
						else {
							this.items.get(idPrefix + '-button-edit').setDisabled(false);
							this.items.get(idPrefix + '-button-delete').setDisabled(false);
						}
					}
				}
			});
			
			this.contextmenu = ctxMenu;
		}
		
		return this.contextmenu;
	},
	
	handleContextmenu : function(view, index, node, e) {
		e.stopEvent();
		
		var ctxMenu = this.getContextmenu();
		
		ctxMenu.setItemData(view, index, node);
		
		ctxMenu.listing = this;
		
		ctxMenu.showAt(e.getXY());
	},
	
	reloadAll : function() {
		this.removeAll();
		
		Ext.iterate(this.stores, function(storeid, store) {
			store.destroy();
			delete(this.stores[storeid]);
		}, this);
		
		this.loadData(this.combinedProviderUrl);
	}
});
 
