<?php 
	$parentid = $rd->getParameter('parentid');
?>
<div id="<?php echo $parentid; ?>">

</div>
<script type="text/javascript">

Ext.onReady(function(){

var TabContextMenu =  function() {
	var ctxItem = null;
	var tp = null;
	
	return {
		handle : function (panel, tab, e) {
			if (!this.contextmenu) {
				this.contextmenu = new Ext.menu.Menu({
					items: [{
						text: '<?php echo $tm->_("Close"); ?>',
						id: panel.id + '-close',
						iconCls: 'silk-cross',
						handler: function() { panel.remove(ctxItem); }
					}, {
						text: '<?php echo $tm->_("Close others"); ?>',
						id: panel.id + '-close-others',
						handler: function() {
							tp.items.each(function(item){
								if(item.closable && item != ctxItem){
									tp.remove(item);
								}
							});
						}
					}, {
						text: '<?php echo $tm->_("Rename"); ?>',
						id: panel.id + '-rename',
						iconCls: 'silk-table-edit',
						handler: TabContextMenu.renameTab,
						scope: this
					}, {
						text: '<?php echo $tm->_("Refresh"); ?>',
						tooltip: '<?php echo $tm->_("Reload the cronk (not the content)"); ?>', 
						iconCls: 'silk-arrow-refresh',
						handler: function() { ctxItem.getUpdater().refresh(); }
					}]
				});
			}
			
			ctxItem = tab;
			
			if (!tp) tp = panel;
			
			this.contextmenu.items.get(panel.id + '-close').setDisabled(!tab.closable);
			this.contextmenu.items.get(panel.id + '-close-others').setDisabled(!tab.closable);
			this.contextmenu.items.get(panel.id + '-rename').setDisabled(!tab.closable);
			
			this.contextmenu.showAt(e.getPoint());
		},
		
		renameTab : function() {
			var msg = Ext.Msg.prompt('<?php echo $tm->_("Enter title"); ?>', '<?php echo $tm->_("Change title for this tab"); ?>', function(btn, text) {
				if (btn == 'ok' && text) {
					ctxItem.setTitle(text);
				}		
			}, this, false, ctxItem.title);
			
			// Move the msgbox to our context menu
			msg.getDialog().alignTo(this.contextmenu.el, 'tr-tr');
		}
	};
	
}();

var CronkTabHandler = function() {
	
	return {
		
		tabPanelDropTarget : function(t) {
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
		}
		
	}
	
}();

var tabPanel = new Ext.TabPanel({
	id : 'cronk-tabs',
	border : false,
	enableTabScroll :true,
	resizeTabs : false,
	
	// This component is stateful!
	stateful: true,
	stateId: 'cronk-tab-panel',
	
	stateEvents: ['add', 'remove', 'tabchange'],
	
	getState: function() {
		
		var cout = { };
		
		this.items.each(function(item, index, l) {
			if (item.iscronk && AppKit.Ext.CronkMgr.cronkExist(item.cronkkey)) {
				var c = AppKit.Ext.CronkMgr.getCronk(item.cronkkey);
				delete c['cronk'];
				cout[c.cmpid] = c;
			}
		});
		
		return {
			cronks: cout,
			items: this.items.getCount(),
			active: this.getActiveTab().id
		}
	},
	
	applyState: function(state) {
		if (state.cronks) {
			Ext.iterate(state.cronks, function(index, item, o) {
				var config = {};
				Ext.apply(config, item.config, item.crconf);
				
				var cronk = AppKit.Ext.CronkMgr.create(config);
				
				this.add(cronk);
				
			}, this);
			
			this.doLayout();
			
			this.setActiveTab(state.active);
		}
	},
	
	// Here comes the drop zone
	listeners: {
		render: CronkTabHandler.tabPanelDropTarget,
		contextmenu: TabContextMenu.handle
	}
});


var cronk_list_id = AppKit.Ext.genRandomId('cronk');
var cronk_search_id = AppKit.Ext.genRandomId('cronk');
var cronk_status_summary_id = AppKit.Ext.genRandomId('cronk');
var cronk_status_summary_chart_id = AppKit.Ext.genRandomId('cronk');

var container = new Ext.Panel({
	layout: 'border',
	border: false,
	monitorResize: true,
	height: 776,
	
	id: 'view-container', // OUT CENTER COMPONENT!!!!!
	
	items: [{	// -- NORTH
		region: 'north',
		id: 'north-frame',
		layout: 'column',
		border: false,
		
		defaults: {
			layout: 'fit',
			border: false
		},
				
		items: [{
			columnWidth: .33,
			id: cronk_search_id
		}, {
			columnWidth: .46,
			id: cronk_status_summary_chart_id
		}, {
			columnWidth: .20,
			id: cronk_status_summary_id
		}]
	}, { // -- SOUTH
		region: 'south',
		title: '<?php echo $tm->_("Log"); ?>',
		collapsible: true,
		id: 'south-frame',
		layout: 'fit',
		height: 150,
		
		stateful: true,
		stateId: 'south-frame',
		
		defaults: {
			border: false
		}
	}, { // -- CENTER
		region: 'center',
		// title: 'MyView',
        margins: '0 0 10 5',
        cls: 'cronk-center-content',
        id: 'center-frame',
        layout: 'fit',
        items: tabPanel
	}, { // -- WEST
		region: 'west',
		id: 'west-frame',
		title: ' ',
        split: true,
        minSize: 200,
        maxSize: 400,
        width: 200,
        collapsible: true,
        margins: '0 0 10 0',
        
        stateful: true,
        stateId: 'west-frame',
        
        layout: {
        	type: 'accordion',
            animate: true
        },

        defaults: {
			border: false,
			autoScroll: true
        },
        
        items: [{
            title: '<?php echo $tm->_("Settings"); ?>',
            html: 'Some settings in here.'
        }]

	}]
});

// Resize the container on windowResize
Ext.EventManager.onWindowResize(function(w,h) {
	this.setHeight(h-65);
	this.doLayout();
}, container);

// Set initial size
container.setHeight(Ext.lib.Dom.getViewHeight()-65);

// Render the container
container.render("<?php echo $parentid; ?>");


// Adding the first cronk (say hello here)
if (tabPanel && tabPanel.items.getCount() <= 0) {
	var cHello = AppKit.Ext.CronkMgr.create({
		title: '<?php echo $tm->_("Welcome"); ?>',
		crname: 'portalHello',
		parentid: undefined,
		layout: 'fit',
		loaderUrl: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => null)); ?>",
		closable: false
	});
	
	tabPanel.add(cHello);
	tabPanel.doLayout();	
	tabPanel.setActiveTab(cHello);
}

// Adding the cronk list
if ((west = Ext.getCmp('west-frame'))) {
	
	var cList = AppKit.Ext.CronkMgr.create({
		parentid: cronk_list_id ,
		title: '<?php echo $tm->_("Cronks"); ?>',
		crname: 'crlist',
		loaderUrl: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => null)); ?>",
		layout: 'fit'
	});
	
	west.add(cList);
	west.doLayout();
	
	west.getLayout().setActiveItem(cList);
}


// Search component
if ((search = Ext.getCmp(cronk_search_id))) {
	
	var cSearch = AppKit.Ext.CronkMgr.create({
		parentid: cronk_search_id,
		crname: 'icingaSearch',
		layout: 'fit',
		height: 100
	});
	
	search.add(cSearch);
	cSearch.doLayout();
}

// Status-summary component
if ((status_summary = Ext.getCmp(cronk_status_summary_id))) {

	var cStatusSummary = AppKit.Ext.CronkMgr.create({
		parentid: cronk_status_summary_id,
		crname: 'icingaStatusSummary',
		height: 100,
		params: {otype: "text"}
	});

	status_summary.add(cStatusSummary);
	cStatusSummary.doLayout();
}

if ((status_summary_chart = Ext.getCmp(cronk_status_summary_chart_id))) {

	var cStatusSummary = AppKit.Ext.CronkMgr.create({
		parentid: cronk_status_summary_chart_id ,
		crname: 'icingaStatusSummary',
		height: 100,
		params: {otype: "chart"}
	});

	status_summary_chart.add(cStatusSummary);
	cStatusSummary.doLayout();
}

// LOG bottom component
if ((south = Ext.getCmp('south-frame'))) {
	
	var cLog = AppKit.Ext.CronkMgr.create({
		parentid: AppKit.Ext.genRandomId('cronksouth'),
		crname: 'gridLogView'
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
	south.doLayout();
}

// Inform about layout changes
container.doLayout();




});
</script>
