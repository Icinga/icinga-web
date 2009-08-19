

<script type="text/javascript">
YAHOO.util.Event.onContentReady('out', function() {
	var url = '<?php echo $ro->gen('icinga.cronks.loader', array('cronk' => 'viewProc', 'p' => array('template' => 'icinga-test-template')));  ?>';

	var mainTabs = new Ext.TabPanel({
        id:'main-tabs',        
        activeTab:0,
        region:'center',
        margins:'0 5 5 0',
        enableTabScroll: true,
        defaults: {autoScroll:true},
        
        items: [{
            title: 'Tab 1',
            autoLoad:{"url": url,"scripts":true}
        },{
            title: 'Tab 2',
            autoLoad:{"url": url,"scripts":true}
        }]
            
	});

	mainTabs.render('out');
		
});


</script>
<div id="out"></div>
<div id="cronk-1"></div>