<?php 
	$parentid = $rd->getParameter('parentid');
?>
<div id="<?php echo $parentid; ?>"></div>
<script type="text/javascript">
<!-- // <![CDATA[

Ext.onReady(function() {

var TabDragOrder = function() {
	var tp = null;
	
	TabDragOrder.superclass.constructor.call(this);
}

TabDragOrder = (new (Ext.extend(TabDragOrder, Ext.util.Observable, {
	init : function(p) {
		tp = p;
		
		tp.on('render', this.initDD, this, { single: true });
		
		tp.on('add', function(panel, item, index) { this.addDraggable(item) }, this);
	},
	
	initDD : function() {
		this.dropTarget = (new (Ext.extend(Ext.dd.DropTarget, {
			notifyDrop : function(source, e, data) {
				var te = e.getTarget('li.x-tab-strip-closable');
				var d = Ext.dd.Registry.getTarget(te);
				if (d && "id" in d) {
					var tab = Ext.getCmp(d.id);
					if (tab && source.tab) {
						
						var index = tp.items.findIndex('id', tab.getId());
						if (index > 0) {
							(function() {
								var re = tp.remove(source.tab, false);
								var a = tp.getActiveTab();
								tp.insert(index, re);
								tp.setActiveTab(a);
								
							}).defer(40, this);
						}
						
					}
				}
			}
		}))(tp.el, { ddGroup: 'cronk-tab-panels' }));
	},
	
	addDraggable : function(ele) {
		var de = tp.getTabEl(ele);
		if (de) {
			
			Ext.dd.Registry.register(de, { id: ele.getId() } );
			
			ele.dd = (new (Ext.extend (Ext.dd.DragSource, {
				init: function() {
					Ext.dd.DD.prototype.init.apply(this, arguments);
					this.setYConstraint(0,0);
					this.tab = ele;
				},
				
				onStartDrag : function(x, y) {
					// tp.hideTabStripItem(ele);
				},
				
				endDrag : function(e) {
					// tp.unhideTabStripItem(ele);
				},
				
				
			}))(de, { ddGroup: 'cronk-tab-panels' }))
		}
	}
})));

var CronkTabPlugin = (new (function() {

	var tp = null;
	var ctxItem = null;
	var contextmenu = null;
	var keyMap = null

	Ext.apply(this, {
		
		init: function(panel) {
			tp = panel;
			
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
			return AppKit.Ext.CronkMgr.create({
				title: _("Welcome"),
				crname: 'portalHello',
				loaderUrl: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => null)); ?>",
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
						e.browserEvent.keyCode = Ext.EventObject.ESC
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

								// Create the cronk we want
								var panel = AppKit.Ext.CronkMgr.create({
									parentid: AppKit.Ext.genRandomId('cronk'),
									title: data.dragData['name'],
									crname: data.dragData.id,
									loaderUrl: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => null)); ?>",
									closable: true,
									layout: 'fit',
									params: data.dragData.parameter
								});
								
								// add them to the tabs
								tabPanel.add(panel);
								
								// Set active
								tabPanel.setActiveTab(panel);
							}
			});
		},
		
		itemRemoveActiveHandler : function (tabPanel, ri) {
			var s = AppKit.Ext.Storage.getStore(tabPanel.id);
			
			if (tabPanel.items.getCount() <= 1) {
				AppKit.Ext.notifyMessage(_('Sorry'), _('Could not remove the last tab!'));
				return false;
			}
			else {
				
				var last = s.get('last_tab');
				if (last && ri.id !== last) {
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
			var s = AppKit.Ext.Storage.getStore(p.id);
			if (ctab && "id" in ctab) {
				s.add('last_tab', ctab.id);
			}
		},
		
		contextMenu : function (panel, tab, e) {
			if (!this.contextmenu) {
				this.contextmenu = new Ext.menu.Menu({
					items: [{
						text: _("Close"),
						id: panel.id + '-close',
						iconCls: 'silk-cross',
						handler: function() { panel.remove(ctxItem); }
					}, {
						text: _("Close others"),
						id: panel.id + '-close-others',
						handler: function() {
							tp.items.each(function(item){
								if(item.closable && item != ctxItem){
									tp.remove(item);
								}
							});
						}
					}, {
						text: _("Rename"),
						id: panel.id + '-rename',
						iconCls: 'silk-table-edit',
						handler: this.renameTab,
						scope: this
					}, {
						text: _("Refresh"),
						tooltip: _("Reload the cronk (not the content)"), 
						iconCls: 'silk-arrow-refresh',
						handler: function() { ctxItem.getUpdater().refresh(); }
					}]
				});
			}
			
			ctxItem = tab;
			
			this.contextmenu.items.get(panel.id + '-close').setDisabled(!tab.closable);
			this.contextmenu.items.get(panel.id + '-close-others').setDisabled(!tab.closable);
			this.contextmenu.items.get(panel.id + '-rename').setDisabled(!tab.closable);
			this.contextmenu.showAt(e.getPoint());
		},
		
		renameTab : function() {
			var msg = Ext.Msg.prompt(_("Enter title"), _("Change title for this tab"), function(btn, text) {
				
				if (btn == 'ok' && text) {
					ctxItem.setTitle(text);
				}
						
			}, this, false, ctxItem.title);
			
			// Move the msgbox to our context menu
			msg.getDialog().alignTo(this.contextmenu.el, 'tr-tr');
		}
		
	});

}));

var tabPanel = new Ext.TabPanel({
	id : 'cronk-tabs',
	border : false,
	enableTabScroll :true,
	resizeTabs : false,
	
	// Plugin
	plugins: [CronkTabPlugin, TabDragOrder],
	
	// This component is stateful!
	stateful: true,
	stateId: 'cronk-tab-panel',
	
	stateEvents: ['add', 'remove', 'tabchange', 'titlechange'],
	
	getState: function() {
		
		var cout = { };
		
		this.items.each(function(item, index, l) {
			if (item.iscronk && AppKit.Ext.CronkMgr.cronkExist(item.cronkkey)) {
				var c = AppKit.Ext.CronkMgr.getCronk(item.cronkkey);
				var cronk = AppKit.Ext.CronkMgr.getCronkComponent(item.cronkkey);

				c.config.title = cronk.title;

				cout[c.cmpid] = Ext.apply(c);
			}
		});
		
		return {
			cronks: cout,
			items: this.items.getCount(),
			active: this.getActiveTab().id
		}
	},
	
	applyState: function(state) {
		(function() {
			
			if (state.cronks) {

				// Adding all cronks
				Ext.iterate(state.cronks, function(index, item, o) {
					var config = {};
					
					Ext.apply(config, item.config, item.crconf);
					
					var cronk = AppKit.Ext.CronkMgr.create(config);
	
					this.add(cronk);
					
				}, this);

				// Sets tehe active tab
				this.setActiveTab(state.active);
			}
			
						
		}).defer(5, this);
		
		return true;
	},
});

var cronk_list_id = AppKit.Ext.genRandomId('cronk');
var cronk_search_id = AppKit.Ext.genRandomId('cronk');
var cronk_status_summary_id = AppKit.Ext.genRandomId('cronk');
var cronk_status_summary_chart_id = AppKit.Ext.genRandomId('cronk');
var cronk_log_id = AppKit.Ext.genRandomId('cronk');

var container = new Ext.Panel({
	layout: 'border',
	border: false,
	monitorResize: true,
	id: 'view-container', // OUT CENTER COMPONENT!!!!!
	
	items: [{	// -- NORTH
		region: 'north',
		id: 'north-frame',
		border: false,
			
		height: 80,
        layout:'column',
        
		items: [{
			width: 260,
			items: [{ id: cronk_search_id }]
		}, {
			width: 380,
			bodyStyle: 'margin-left: 10px;',
			items: [{ id: cronk_status_summary_chart_id }]
		}, {
			width: 200,
			bodyStyle: 'margin-left: 10px;',
			items: [{ id: cronk_status_summary_id }]
		}, {
			columnWidth: 1,
			html: ''
		}]
		
	}, { // -- SOUTH
		region: 'south',
		title: '<?php echo $tm->_("Log"); ?>',
		collapsible: true,
		id: 'south-frame',
		height: 150,
		layout: 'fit',
		stateful: true,
		stateId: 'south-frame',
		defaults: {
			border: false
		}
		
	}, { // -- CENTER
		region: 'center',
        margins: '0 0 10 5',
        cls: 'cronk-center-content',
        id: 'center-frame',
        layout: 'fit',
        items: tabPanel
	}, { // -- WEST
		region: 'west',
		id: 'west-frame',
//		title: ' ',
        split: true,
        minSize: 200,
        maxSize: 400,
        width: 200,
        collapsible: true,
        margins: '0 0 10 0',
        
        stateful: true,
        stateId: 'west-frame',

		layout: 'fit',

        defaults: {
			border: false,
			autoScroll: true
        }

	}]
});

container.on('afterrender', function() {
	container.setHeight(Ext.lib.Dom.getViewHeight() - 68);
}, container, { single: true });

// Render the container
container.render("<?php echo $parentid; ?>");

// Resize the container on windowResize
Ext.EventManager.onWindowResize(function(w,h) {
	this.setHeight(h-68);
	this.doLayout(false);
}, container);

// Adding the cronk list
if ((west = Ext.getCmp('west-frame'))) {
	
	var cList = AppKit.Ext.CronkMgr.create({
		parentid: cronk_list_id,
		crname: 'crlist',
		loaderUrl: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => null)); ?>"
//		layout: {
//        	type: 'accordion',
//            animate: true
//        }
	});
	
	west.add(cList);
}

// Search component
if ((search = Ext.getCmp(cronk_search_id))) {	
	var cSearch = AppKit.Ext.CronkMgr.create({
		parentid: cronk_search_id + '-cmp',
		crname: 'icingaSearch'
	});

	search.add(cSearch);	
}

// Status-summary component
if ((status_summary = Ext.getCmp(cronk_status_summary_id))) {

	var cStatusSummary = AppKit.Ext.CronkMgr.create({
		parentid: cronk_status_summary_id + '-cmp',
		crname: 'icingaStatusSummary',
		params: {otype: "text"}
	});

	status_summary.add(cStatusSummary);
}

if ((status_summary_chart = Ext.getCmp(cronk_status_summary_chart_id))) {
	var cStatusSummaryChart = AppKit.Ext.CronkMgr.create({
		parentid: cronk_status_summary_chart_id + '-cmp' ,
		crname: 'icingaStatusSummary',
		params: {otype: "chart"}
	});
	
	status_summary_chart.add(cStatusSummaryChart);
}


// LOG bottom component
if ((south = Ext.getCmp('south-frame'))) {
	
	var cLog = AppKit.Ext.CronkMgr.create({
		parentid: cronk_log_id,
		crname: 'gridLogView',
		layout: 'fit',
		height: 150
	});

	// After the LOG component is added, start autorefresh
	cLog.on('add', function(el, component, index) {
		if (index == 0) {
			
			// Refresh the component
			var refreshHandler = function() {
				component.getStore().reload();
			}
			
			// Creating a task
			var interval = 60 * 1000; // 60s
			var task = Ext.TaskMgr.start.defer(interval, this, [{
				run: refreshHandler,
				interval: interval
			}]);
			
			// Run if needed
			var switchHandler = function(c) {
				if (c.collapsed == true) {
					Ext.TaskMgr.stop(task);
				}
				else {
					Ext.TaskMgr.start(task);
				}
			}
			
			// Register corresponding events
			south.on('collapse', switchHandler);
			south.on('expand', switchHandler);
		}
	});
	
	south.add(cLog);
}

container.doLayout(false, true);

// Okay redraw all after a while (if all events are gone)
(function() {
	this.doLayout(false, true);
}).defer(3000, container);

});

// ]]> -->
</script>
