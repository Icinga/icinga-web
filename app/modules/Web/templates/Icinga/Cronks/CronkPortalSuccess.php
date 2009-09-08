<?php 
	$htmlid = $rd->getParameter('htmlid');
?>
<div id="<?php echo $htmlid; ?>">

</div>
<script type="text/javascript">

Ext.onReady(function(){

var TabContextMenu =  function(){
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

var tabPanel = new Ext.TabPanel({
	id: 'cronk-tabs',
	border: false,
	
	// Here comes the drop zone
	listeners: {
		render: initTabPanelDropZone,
		contextmenu: TabContextMenu.handle
	}
});

function initTabPanelDropZone(t) {
	new Ext.dd.DropTarget(t.header, {
					ddGroup: 'cronk',
					
					notifyDrop: function(dd, e, data){
						
						var id = AppKit.genRandomId('cronk-');
						
						var params = {
							'p[htmlid]': id
						};
					
						if (data.dragData.parameter) {
							for (var k in data.dragData.parameter) {
								params['p[' + k + ']'] = data.dragData.parameter[k];
							}
						}
						
						// our panel
						var panel = new Ext.Panel({
							title: data.dragData.name,
							closable: true,
							id: id,
							layout: 'fit'
						});
						
						// add them to the tabs
						tabPanel.add(panel);
						
						// Render and set active
						tabPanel.setActiveTab(tabPanel.items.length-1);
						
						// Reconfigure the updater to have the ability to refresh.
						panel.getUpdater().setDefaultUrl({
							url: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => null)); ?>" + data.dragData.id,
							scripts: true,
							params: params
						})
						
						// initial refresh
						panel.getUpdater().refresh();
					}
	});

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
			border: false
        },
        
        items: [{
            title: 'Navigation'
        }, {
            title: 'Settings',
            html: 'Some settings in here.'
        }, {
            title: 'Cronks',
            id: cronk_list_id
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
tabPanel.add({
	autoLoad: { url: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => 'portalHello')); ?>" },
	title: 'Welcome',
	// height: Ext.getCmp('center-frame').getHeight()
});

// Adding the cronk list
var coList = Ext.getCmp(cronk_list_id)
coList.getUpdater().setDefaultUrl({
	url: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => 'crlist')); ?>",
	scripts: true,
    params: { 'p[htmlid]': cronk_list_id }
});
coList.getUpdater().refresh();

// Search component
var coSearch = Ext.getCmp(cronk_search_id)
coSearch.getUpdater().setDefaultUrl({
	url: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => 'icingaSearch')); ?>",
	scripts: true,
    params: { 'p[htmlid]': cronk_search_id }
});
coSearch.getUpdater().refresh();

// LOG bottom component
var coLog = Ext.getCmp('south-frame');
coLog.getUpdater().setDefaultUrl({
	url: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => 'gridLogView')); ?>",
	scripts: true,
    params: { 'p[htmlid]': 'south-frame' }
});

// After the LOG component is added, start autorefresh
coLog.on('add', function(el, component, index) {
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

// Load the LOG component
coLog.getUpdater().refresh();


// Set default active items
Ext.getCmp('west-frame').getLayout().setActiveItem(2);
tabPanel.setActiveTab(0);

// Inform about layout changes
container.doLayout();




});
</script>
