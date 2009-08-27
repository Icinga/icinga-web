<?php 
	$htmlid = $rd->getParameter('htmlid');
?>
<div id="<?php echo $htmlid; ?>">

</div>
<script type="text/javascript">


var tabPanel = new Ext.TabPanel({
	autoHeight: true,
	autoScroll: true,
	autoWidth: true,
	border: false,
	
	// Here comes the drop zone
	listeners: {
		render: initTabPanelDropZone
	}
});

function initTabPanelDropZone(t) {
	new Ext.dd.DropTarget(t.header, {
					ddGroup: 'cronk',
					
					notifyDrop: function(dd, e, data){
						
						var params = {};
					
						if (data.dragData.parameter) {
							for (var k in data.dragData.parameter) {
								params['p[' + k + ']'] = data.dragData.parameter[k];
							}
						}
						
						tabPanel.add({
							title: data.dragData.name,
							closable: true,
							autoLoad: { 
								url: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => null)); ?>" + data.dragData.id,
								scripts: true,
								params: params
							}
						});
						
						tabPanel.setActiveTab(tabPanel.items.length-1);
						tabPanel.doLayout();
					}
	});

}


tabPanel.add({
	autoLoad: { url: "<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => 'portalHello')); ?>" },
	title: 'Welcome'
});



var container = new Ext.Panel({
	layout: 'border',
	monitorResize: true,
	border: false,
	height: 600,
	
	items: [{
		region: 'center',
		title: 'MyView',
        margins: '0 0 0 5',
        items: tabPanel
	}, {
		region: 'west',
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
            autoLoad: {
            	url: '<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => 'crlist')); ?>',
            	scripts: true
        	}
        }]

	}]
});

container.render("<?php echo $htmlid; ?>");

tabPanel.setActiveTab(0);
tabPanel.doLayout();

container.doLayout();

</script>