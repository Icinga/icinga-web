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
	autoHeight: true,
	autoScroll: true,

	border: false,
	id: 'cronk-tabs',
	
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
						
						// out panel
						var panel = new Ext.Panel({
							height: Ext.getCmp('center-frame').getHeight(),
							title: data.dragData.name,
							closable: true,
							id: id
						});
						
						// add them to the tabs
						tabPanel.add(panel);
						
						// Render and set active
						tabPanel.setActiveTab(tabPanel.items.length-1);
						tabPanel.doLayout();
						
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
	height: 600,
	
	id: 'cronk-container', // OUT CENTER COMPONENT!!!!!
	
	items: [{
		region: 'north',
		id: 'north-frame',
		layout: 'column',
		height: 29,
		
		defaults: {
			border: false
		},
		
		style: {
			'margin-left': '5px'
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
	}, {
		region: 'center',
		title: 'MyView',
        margins: '0 0 0 5',
        cls: 'cronk-center-content',
        items: tabPanel,
        id: 'center-frame',
        layout: 'fit'
	}, {
		region: 'west',
		id: 'west-frame',
		title: 'Misc',
        split: true,
        minSize: 200,
        maxSize: 400,
        width: 200,
        collapsible: true,
        margins: '0 0 0 5',
        cls: 'cronk-left-content',
        
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

container.render("<?php echo $htmlid; ?>");

// Adding the first cronk (say hello here)
tabPanel.add({
	autoLoad: { url: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => 'portalHello')); ?>" },
	title: 'Welcome',
	height: Ext.getCmp('center-frame').getHeight()
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


// Set default active items
Ext.getCmp('west-frame').getLayout().setActiveItem(2);
tabPanel.setActiveTab(0);

// Inform about layout changes
container.doLayout();



});
</script>
