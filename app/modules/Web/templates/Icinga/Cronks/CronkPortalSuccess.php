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

var container = new Ext.Container({
	layout: 'border',
	height: 500,
	
	items: [{
		region: 'center',
		title: 'MyView',
        margins: '0 0 0 5',
        layout: 'column',
        items: new Ext.TabPanel({
        	border: false,
        	activeTab: 0,
        	
        	
        	
        	items: [{
				title: 'Welcome',
				autoLoad: {
					url: '<?php echo $ro->gen('icinga.cronks.loader', array('cronk' => 'portalHello')); ?>'
        		},
				closable: true
        	}, portal, {
				title: 'Grid',
				autoLoad: {
					url: '<?php echo $ro->gen('icinga.cronks.loader', array('cronk' => 'viewProc')); ?>',
					params: { 'p[template]': 'icinga-test-template' },
					scripts: true
        		},
				closable: true
        	}]
        })
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
        
        items: [{
            title: 'Navigation',
            border: false,
            iconCls: 'nav' // see the HEAD section for style used
        }, {
            title: 'Settings',
            html: 'Some settings in here.',
            border: false,
            iconCls: 'settings'
        }]

	}]
});

container.render("<?php echo $htmlid; ?>");
</script>