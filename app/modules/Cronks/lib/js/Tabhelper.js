Ext.ns('Cronk.util');

Cronk.util.CronkTabHelper = function() {
	var pub = {};
	var tp = null;
	var ctxItem = null;
	var contextmenu = null;
	var keyMap = null;
}
	
Cronk.util.CronkTabHelper = Ext.extend(Object, { 
		
	last_tab: null,
	
	sliding_tab: null,
	
	init: function(c) {
		tp = c;
		
		// create  our drop target for the cronks
		tp.on('afterrender', this.createDropTarget, this, { single: true });
		
		// Deny removing all tabs
		tp.on('beforeremove', this.itemRemoveActiveHandler, this);
		
		// Enable title change bubble to safe the state 
		// of the component
		tp.on('add', this.itemModifier, this);
		
		// Saving the last tab for a history
		tp.on('beforetabchange', this.itemActivate, this);
		
		// Adding the contextmenu
		tp.on('contextmenu', this.contextMenu, this);
		
		// Check if we've no tabs and say hello if the frame is empty
		tp.on('afterrender', this.sayHello, this, { single: true, delay: 5 });
		
		// Prevent refresh
		this.applyGlobalKeyMap();
	},
	
	createWelcomeCronk : function() {
		return Cronk.factory({
			title: _("Welcome"),
			crname: ( AppKit.getPrefVal('org.icinga.cronk.default') || 'portalHello' ),
			closable: true
		});
	},
	
	sayHello : function() {
		if (tp.items.getCount() < 1) {
			tp.setActiveTab(tp.add(this.createWelcomeCronk()));
		}
	},
	
	applyGlobalKeyMap : function() {
		keyMap = new Ext.KeyMap(Ext.getDoc(), {
			key: Ext.EventObject.F5,
			fn: function(keycode, e) {
				
				if (Ext.isIE) {
					e.browserEvent.keyCode = Ext.EventObject.ESC;
				}
				
				var tab = tp.getActiveTab();
				if (tab) {
					e.stopEvent();
					tab.getUpdater().refresh();
				}
			},
			scope: this
		});
	},
	
	createDropTarget : function() {
		var t = tp;
		new Ext.dd.DropTarget(t.header, {
						ddGroup: 'cronk',
						
						notifyDrop: function(dd, e, data){
							// add them to the tabs
							var a = tp.add({
								iconCls: Cronk.getIconClass(data.dragData['image_id']),
								title: data.dragData['name'],
								crname: data.dragData.cronkid,
								closable: true,
								params: data.dragData.parameter,
								xtype: 'cronk'
							});
							
							// Set active
							tp.setActiveTab(a);
						}
		});
	},
	
	itemRemoveActiveHandler : function (tabPanel, ri) {
		
		if (tabPanel.items.getCount() <= 1) {
			AppKit.notifyMessage(_('Sorry'), _('Could not remove the last tab!'));
			return false;
		}
		else {
			
			if (this.last_last_tab && ri.id !== last) {
				tabPanel.setActiveTab( last );
			}
			else {
				tabPanel.setActiveTab( (tabPanel.items.getCount() - 1) );
			}
		}
		
		return true;
	},
	
	itemModifier : function (co, item, index) {
		item.enableBubble('titlechange');
		return true;
	},
	
	itemActivate : function (p, ntab, ctab) {
		if (ctab && "id" in ctab) {
			this.last_tab = ctab.id;
		}
	},
	
	contextMenu : function (myp, tab, e) {
		if (!this.contextmenu) {
			this.contextmenu = new Ext.menu.Menu({
				items: [{
					text: _("Close"),
					id: tp.id + '-close',
					iconCls: 'icinga-icon-cross',
					handler: function() { tp.remove(ctxItem); }
				}, {
					text: _("Close others"),
					id: tp.id + '-close-others',
					handler: function() {
						tp.items.each(function(item){
							if(item.closable && item != ctxItem){
								tp.remove(item);
							}
						});
					}
				}, {
					text: _("Rename"),
					id: tp.id + '-rename',
					iconCls: 'icinga-icon-table-edit',
					handler: this.renameTab,
					scope: this
				}, {
					text: _("Refresh"),
					tooltip: _("Reload the cronk (not the content)"), 
					iconCls: 'icinga-icon-arrow-refresh',
					handler: function() {
						ctxItem.getUpdater().refresh();
					}
				}, {
					text: _("Settings"),
					iconCls: 'icinga-icon-cog',
					menu: [{
						text: _("Save Cronk"),
						tooltip: _("Save this view as new cronk"),
						iconCls: 'icinga-icon-star-plus',
						handler: function() {
							var cb = Cronk.util.CronkBuilder.getInstance();
							cb.show(this.getEl());
							cb.setCurrentCronkId(ctxItem.getId());
						}
					}, {
						text: _("Tab slider"),
						checked: false,
						checkHandler: function(checkItem, checked) {
							
							var refresh = AppKit.getPrefVal('org.icinga.grid.refreshTime') || 300;
							
							if (checked == true) {
								
								if (Ext.isDefined(this.sliderTask)) {
									AppKit.getTr().stop(this.sliderTask);
								}
								
								this.sliding_tab = this.getTabIndex(ctxItem);
								
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
				}]
			});
		}
		
		ctxItem = tab;
		
		this.contextmenu.items.get(myp.id + '-close').setDisabled(!tab.closable);
		this.contextmenu.items.get(myp.id + '-close-others').setDisabled(!tab.closable);
		this.contextmenu.items.get(myp.id + '-rename').setDisabled(!tab.closable);
		this.contextmenu.showAt(e.getPoint());
	},
	
	getTabIndex: function(tab) {
		var i = -1;
		tp.items.each(function(item, index, a) {
			i++;
			if (item == tab) {
				return false;
			}
		});
		return i;
	},
	
	enameTab : function() {
		var msg = Ext.Msg.prompt(_("Enter title"), _("Change title for this tab"), function(btn, text) {
			
			if (btn == 'ok' && text) {
				ctxItem.setTitle(text);
			}
					
		}, this, false, ctxItem.title);
		
		// Move the msgbox to our context menu
		msg.getDialog().alignTo(this.contextmenu.el, 'tr-tr');
	},
	
	createURLCronk: function(data) {

		var urlCronk =  Cronk.factory({
			title: data.title,
			crname: data.crname,
			closable: true
		});
		urlCronk.on("add",function(p, c, i) {
			if(!c.store)
				return null; 

			Ext.apply(c.store.baseParams,data.cr_base);
			c.store.groupDir = data.groupDir;
			c.store.groupField = data.groupField;
			
			c.store.sort(data.groupField,data.groupDir);
			AppKit.log(c);
			if(c.parentCmp.sort_array)  {
				c.parentCmp.sort_array[0]['direction'] = data.groupDir;
				c.parentCmp.sort_array[0]['field'] = data.groupField;
			}
			c.store.load();

		});
		return urlCronk;
	}
	
});
