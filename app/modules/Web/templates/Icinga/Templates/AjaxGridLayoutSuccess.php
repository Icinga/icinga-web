
<div id="data-grid"></div>
<script type="text/javascript">
<!--

Ext.onReady(function(){

	// The field mapping
	var mapping = Ext.data.Record.create([
		{name: 'host_name'},
		{name: 'service_name'}
	]);
	                                                 	
	var store = new Ext.data.GroupingStore({ 
		autoLoad:true,
		 
		proxy: new Ext.data.HttpProxy({
			url: '<?php echo $ro->gen('icinga.templateView.json', array('template' => $rd->getParameter('template'))); ?>'
		}),
		
		reader: new Ext.data.JsonReader({
			root: 'resultRows',
			totalProperty: 'resultCount',
			remoteSort: false,
		},mapping),

		sortInfo: { field: 'host_name', direction: 'ASC' },
		groupField: 'host_name'
	});
	
	
	
	// Our grid
	var grid = new Ext.grid.GridPanel({
		store:				store,
		
		trackMouseOver:		false,
        disableSelection:	false,
        loadMask:			true,
		renderTo:			'data-grid',
		
		collapsible:		true,
        animCollapse:		true,
		frame:				true,
		
		width:				750,
		height:				440,

		columns: [
			{ header: 'Host', width: 120, dataIndex: 'host_name', sortable: true },
			{ header: 'Service', width: 120, dataIndex: 'service_name', sortable: true } 	
		],		

        // paging bar on the bottom
        bbar: new Ext.PagingToolbar({
            pageSize: 25,
            store: store,
            displayInfo: true,
            displayMsg: 'Displaying topics {0} - {1} of {2}',
            emptyMsg: "No topics to display"
        }),

        view: new Ext.grid.GroupingView({
            forceFit: true,
            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})'
        })
        
	});

	// Init the store
	store.load({params:{start:0, limit:25}});
    
});

-->
</script>
