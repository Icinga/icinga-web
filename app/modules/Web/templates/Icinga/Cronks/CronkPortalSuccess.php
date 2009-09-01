<?php 
	$htmlid = $rd->getParameter('htmlid');
?>
<div id="<?php echo $htmlid; ?>">

</div>
<script type="text/javascript">

Ext.onReady(function(){

var tabContextmenut =  {
	ctxItem : null,
	
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
}

var tabPanel = new Ext.TabPanel({
	autoHeight: true,
	autoScroll: true,

	border: false,
	id: 'cronk-tabs',
	
	// Here comes the drop zone
	listeners: {
		render: initTabPanelDropZone,
		contextmenu: tabContextmenut.handle
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

var container = new Ext.Panel({
	layout: 'border',
	border: false,
	monitorResize: true,
	height: 600,
	
	id: 'cronk-container', // OUT CENTER COMPONENT!!!!!
	
	items: [{
		region: 'center',
		title: 'MyView',
        margins: '0 0 0 5',
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
            id: cronk_list_id,
            autoLoad: {
            	url: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => 'crlist')); ?>",
            	scripts: true,
            	params: { 'p[htmlid]': cronk_list_id }
        	}
        }]

	}]
});

container.render("<?php echo $htmlid; ?>");

tabPanel.add({
	autoLoad: { url: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => 'portalHello')); ?>" },
	title: 'Welcome',
	height: Ext.getCmp('center-frame').getHeight()
});

Ext.getCmp('west-frame').getLayout().setActiveItem(2);

tabPanel.setActiveTab(0);
tabPanel.doLayout();

container.doLayout();

});
</script>