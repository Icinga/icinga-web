

Cronk.util.CronkListingPanel = function(c) {
	
	var CLP = this;
	
	Cronk.util.CronkListingPanel.superclass.constructor.call(this, c);
	
	this.stores = {};
	
	this.cronks = {};
	
	this.categories = {};
	
	this.default_act = -1;
	
	this.template = new Ext.XTemplate(
	    '<tpl for=".">',
	    	'<div class="cronk-preview" id="{name}">',
        	'<div class="thumb"><img ext:qtip="{description}" src="{image}"></div>',
        	'<span class="x-editable">{name}</span>',
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
					}, this)
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
					{
						name:'parameter',
						convert:function(v,record) {
							if(!Ext.isObject(v))
								return v;
							for(var i in v) {
								if(Ext.isObject(v[i]))
									v[i] = Ext.encode(v[i]);
							}
							return v;
						}
					},
					{
						name: 'image',
						convert: function(v, record){
							return AppKit.util.Dom.imageUrl(v);
						}
					}
			    ]
			});
		}
		
		var store = CLP.stores[storeid];
		
		store.loadData(data);
		
	}
	
	var createView = function(storeid, title) {
		
		CLP.add({
			title: title,
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
		        store: CLP.getStore(storeid),
		        tpl: CLP.template,
		        overClass:'x-view-over',
		        itemSelector:'div.cronk-preview',
		        emptyText: 'No data',
		       	cls: 'cronk-data-view',
		        border: false,
		        
		        // Create the drag zone
		        listeners: {
		        	render: CLP.initCronkDragZone,
		        	dblclick: CLP.dblClickHandler
		        } 
		    }),
			border: false
		});
		
	}
	
	this.loadData(this.combinedProviderUrl);
	
	var act = false;
	
	CLP.on('afterrender', function() {
		if (!CLP.applyActiveItem() && this.default_act >= 0) {
			console.log(this.default_act);
			CLP.setActiveItem(this.default_act);
		}
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
	
	id: 'cronk-listing-panel',
	stateId: 'cronk-listing-panel',
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
		},
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
	
	constructor: function(c) {
		Cronk.util.CronkListingPanel.superclass.constructor.call(this, c);
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
			var panel = tabPanel.add({
				xtype: 'cronk',
				title: record.data['name'],
				crname: record.data.cronkid,
				closable: true,
				params: record.data.parameter
			});
			
			tabPanel.setActiveTab(panel);
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
	
	reloadAll : function() {
		
		
		this.removeAll();
		
		Ext.iterate(this.stores, function(storeid, store) {
			store.destroy();
			delete(this.stores[storeid]);
		}, this);
		
		this.loadData(this.combinedProviderUrl);
		
		AppKit.util.Layout.doLayout(null, 200);
	}
});
 