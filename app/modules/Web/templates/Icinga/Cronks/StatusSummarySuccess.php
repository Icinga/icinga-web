<?php
	$htmlid = $rd->getParameter('htmlid');
?>

<script type="text/javascript">

function CronkDisplayStateSummary() {
	
	// Our store to retrieve the cronks
	var store = new Ext.data.JsonStore({
	    url: '<?php echo $ro->gen('icinga.cronks.statusSummary.json'); ?>',
	    root: 'status_data.data',
	    fields: [
	        'state_id', 'state_name', 'type', 'count'
	    ]
	});
	
	// Load the data
	store.load();

	//alert(store.getTotalCount());

	// Template to display the cronks
	var tpl = new Ext.XTemplate(
	    '<tpl for=".">',
	    	'<div class="test-l" id="{state_id}">',
        	'<span class="x-editable">{state_id}</span>|',
        	'<span class="x-editable">{state_name}</span>|',
        	'<span class="x-editable">{type}</span>|',
        	'<span class="x-editable">{count}</span>',
        	'</div>',
	    '</tpl>',
	    '<div class="x-clear"></div>'
	);
	
	// The dataview container
	var view = new Ext.DataView({
		title: 'test',
        store: store,
        tpl: tpl,
        // autoHeight:true,
        // multiSelect: true,
        // overClass:'x-view-over',
        itemSelector:'div.test-l',
        emptyText: 'No data'
        
       	// cls: 'cronk-data-view'
        
    });
	
	var cmp = Ext.getCmp("<?php echo $htmlid; ?>");
	cmp.add(view);
	
	Ext.getCmp('view-container').doLayout();
	
}

CronkDisplayStateSummary();

</script>
