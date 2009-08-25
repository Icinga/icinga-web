<?php 
	$htmlid = $rd->getParameter('htmlid');
?>
<div id="<?php echo $htmlid; ?>">

</div>
<script type="text/javascript">
var tools = [{

    id:'gear',

    handler: function(){
        Ext.Msg.alert('Message', 'The Settings tool was clicked.');
    }

},{

    id:'close',
    handler: function(e, target, panel){
        panel.ownerCt.remove(panel, true);
    }

}];

var exampleHtml = 'LAOLA, das ist ein Test';

var portal = new Ext.ux.Portal({
    height: 500,
    layout: 'column',
    autoScroll: true,
    title: 'portal',

    items:[{

        columnWidth: .09,
        style: 'padding: 10px;',
        items:[{
            title: 'Another Panel 1',
            tools: tools,
            html: exampleHtml
        },
        {
            title: 'Grid1',
            tools: tools,
            autoLoad: {
				url: '<?php echo $ro->gen('icinga.cronks.loader', array('cronk' => 'viewProc')); ?>',
				params: { 'p[template]': 'icinga-test-template' },
				scripts: true
			}
        }]

    }, {
    	columnWidth: .09,
    	style: 'padding: 10px;',
    	items:[{
            title: 'Another Panel 2',
            tools: tools,
            html: exampleHtml
        }]
    	
    }]


});

var tabPanel = new Ext.TabPanel({
	autoHeight: true,
	autoScroll: true,
	autoWidth: true,
	border: false
});

tabPanel.add({
	autoLoad: { url: '<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => 'portalHello')); ?>' },
	title: 'Welcome'
});

tabPanel.add({
	autoLoad: {
		url: '<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => 'viewProc')); ?>',
		params: { 'p[template]': 'icinga-test-template' },
		scripts: true
	},
	title: 'Grid'
});



var container = new Ext.Panel({
	layout: 'border',
	monitorResize: true,
	border: false,
	height: 500,
	
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
            autoLoad: '<?php echo $ro->gen('icinga.cronks.crloader', array('cronk' => 'crlist')); ?>'
        }]

	}]
});

container.render("<?php echo $htmlid; ?>");

container.doLayout();

</script>