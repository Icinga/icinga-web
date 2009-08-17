
<div id="data-grid"></div>
<script type="text/javascript">
<!--

Ext.onReady(function(){

	
	function loadAjaxGrid(meta) {	
		// Prepare structures for the gridconfig
		var mapping_array = new Array(meta.keys.length);
		var column_array = new Array(meta.keys.length);
		
		for (var i=0; i<meta.keys.length; i++) {
			var index = meta.keys[i];

			if (meta.fields[index] && meta.fields[index].display.visible == true) { 
			
				mapping_array[i] = {name: index};
	
				column_array[i] = {
					header:			meta.fields[index].display.label,
					width:			(meta.fields[index].display.width ? meta.fields[index].display.width : 120),
					dataIndex:		index,
					sortable:		(meta.fields[index].order.enabled ? true : false)
				};

			}
		}
		
		// The field mapping
		var mapping = Ext.data.Record.create(mapping_array);
		                                                 	
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
	
			columns: column_array,		
	
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
	}

	// Try to load template info for column info
	var request = YAHOO.util.Connect.asyncRequest('GET', '<?php echo $ro->gen('icinga.templateView.json.metaInfo', array('template' => $rd->getParameter('template'))); ?>', {
		success: function(o) {
			var meta = {};
			try {
				meta = YAHOO.lang.JSON.parse(o.responseText);
			}
			catch (x) { 
				alert("Parsing template meta information failed!");
				return;
			}

			// Load the grid
			loadAjaxGrid(meta);
		},

		// Disable meta data caching
		cache: false
	});
    
});

-->
</script>
