<?php 
	$htmlid = $rd->getParameter('htmlid');
?>
<div id="<?php echo $htmlid; ?>">

</div>
<script type="text/javascript">

Ext.onReady(function(){

var TabContextMenu =  function() {
	var ctxItem = null;
	
	return {
		handle : function (panel, tab, e) {
			if (!this.contextmenu) {
				this.contextmenu = new Ext.menu.Menu({
					items: [{
						text: 'Close tab',
						id: panel.id + '-close',
						handler: function() {
							panel.remove(ctxItem);
						}
					}, {
						text: 'Refresh',
						
						handler: function() {
							ctxItem.getUpdater().refresh();
						}
					}]
				});
			}
			
			ctxItem = tab;
			
			this.contextmenu.items.get(panel.id + '-close').setDisabled(!tab.closable);
			
			this.contextmenu.showAt(e.getPoint());
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
								var panel = AppKit.Ext.createCronk({
									htmlid: AppKit.genRandomId('cronk-'),
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
	id: 'cronk-tabs',
	border: false,
	
	// This component is stateful!
	/* stateId: 'cronk-tab-panel',
	stateful: true,
	
	stateEvents: ['tabchange'],
	getState: function() {
		return {
			tab: function() {
				return this.getActiveTab().id;
			}
		}
	},
	
	applyState: function(state) {
		Ext.Msg.alert('ok', 'state');
	}, */
	
	// Here comes the drop zone
	listeners: {
		render: CronkTabHandler.tabPanelDropTarget,
		contextmenu: TabContextMenu.handle
	}
});

function initTabPanelDropZone(t) {


}

var cronk_list_id = AppKit.genRandomId('cronk-');
var cronk_search_id = AppKit.genRandomId('cronk-');

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
			columnWidth: .33,
			html: 'test2'	
		}, {
			columnWidth: .33,
			html: 'test3'	
		}]
	}, { // -- SOUTH
		region: 'south',
		title: '<?php echo $tm->_("Log"); ?>',
		collapsible: true,
		id: 'south-frame',
		layout: 'fit',
		height: 150,
		style: {
			'margin-top': '5px'
		}
	}, { // -- CENTER
		region: 'center',
		title: 'MyView',
        margins: '0 0 0 5',
        cls: 'cronk-center-content',
        id: 'center-frame',
        layout: 'fit',
        items: tabPanel
	}, { // -- WEST
		region: 'west',
		id: 'west-frame',
		title: 'Misc',
        split: true,
        minSize: 200,
        maxSize: 400,
        width: 200,
        collapsible: true,
        margins: '0 0 0 5',
        
        layout: {
        	type: 'accordion',
            animate: true
        },

        defaults: {
			border: false,
			autoScroll: true
        },
        
        items: [{
            title: '<?php echo $tm->_("Navigation"); ?>'
        }, {
            title: '<?php echo $tm->_("Settings"); ?>',
            html: 'Some settings in here.'
        }]

	}]
});

// Resize the container on windowResize
Ext.EventManager.onWindowResize(function(w,h) {
	this.setHeight(h-80);
	this.doLayout();
}, container);

// Set initial size
container.setHeight(Ext.lib.Dom.getViewHeight()-80);

// Render the container
container.render("<?php echo $htmlid; ?>");

// Adding the first cronk (say hello here)
if (tabPanel) {
	var cHello = AppKit.Ext.createCronk({
		title: '<?php echo $tm->_("Welcome"); ?>',
		crname: 'portalHello',
		loaderUrl: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => null)); ?>",
		layout: 'fit'
	});
	
	tabPanel.add(cHello);
	
	tabPanel.doLayout();
	
	tabPanel.setActiveTab(cHello);
}

// Adding the cronk list
if ((west = Ext.getCmp('west-frame'))) {
	
	var cList = AppKit.Ext.createCronk({
		htmlid: cronk_list_id ,
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
	
	var cSearch = AppKit.Ext.createCronk({
		htmlid: cronk_search_id ,
		crname: 'icingaSearch',
		loaderUrl: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => null)); ?>",
		layout: 'fit',
		height: 100
	});
	
	search.add(cSearch);
	cSearch.doLayout();
}

// LOG bottom component
if ((south = Ext.getCmp('south-frame'))) {
	
	var cLog = AppKit.Ext.createCronk({
		htmlid: AppKit.genRandomId('cronk-'),
		crname: 'gridLogView',
		loaderUrl: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => null)); ?>",
		layout: 'fit'
	});

	// After the LOG component is added, start autorefresh
	cLog.on('add', function(el, component, index) {
		if (index == 0) {
			var refreshHander = function() {
				component.getStore().reload();
			}
			
			Ext.TaskMgr.start({
				run: refreshHander,
				interval: 60 * 1000 // 60s
			});
		}
	});
	
	south.add(cLog);
	south.doLayout();
}

// Inform about layout changes
container.doLayout();




});
</script>
