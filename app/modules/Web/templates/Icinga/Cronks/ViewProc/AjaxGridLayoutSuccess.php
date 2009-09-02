<?php 
	$htmlid = $rd->getParameter('htmlid');
?>
<script type="text/javascript">
<!-- // <![CDATA[

// if (Ext.get("<?php echo $htmlid; ?>")) {
	
	function loadAjaxGrid(meta) {	
		
		// Prepare structures for the gridconfig
		var mapping_array	= new Array(meta.keys.length);
		var column_array	= new Array(meta.keys.length);
		var sort_array		= new Array(meta.keys.length);
		var pager_array		= new Array(3);
		var filter_array	= new Array(meta.keys.length);
		var sort_info		= new Array(2);

		var ii				= 0;
		
		
		for (var i=0; i<meta.keys.length; i++) {
			var index = meta.keys[i];
			var field = meta.fields[index];
 			
			mapping_array[i] = {name: index};
			
			column_array[i] = {
				header:			field.display.label,
				width:			(field.display.width ? field.display.width : 120),
				dataIndex:		index,
				sortable:		(field.order.enabled ? true : false),
				hidden:			(field.display.visible ? false : true)
			};

			if (field.order['default'] == true) {
				sort_array[ii] = {
						direction: (field.order.direction ? field.order.direction.toUpperCase() : 'ASC'),
						field: index
					};

				ii++;
			}
		}

		// Pager config
		pager_array = {
			enabled:	(meta.template.pager.enabled ? true : false),
			size:		(meta.template.pager.size ? meta.template.pager.size : 25),
			start:		(meta.template.pager.start ? meta.template.pager.start : 0)
		};
		
		// The field mapping
		var mapping = Ext.data.Record.create(mapping_array);

		// Readerconfig
		var reader_config = {
				root:				'resultRows',
				totalProperty:		'resultCount',
				successProperty:	'resultSuccess'
		};

		if (meta.template.datasource.id) {
			reader_config.idProperty = meta.template.datasource.id;
		}

		var reader = new Ext.data.JsonReader(reader_config, mapping);

		// Store configuration
		var store_config = { 
			autoLoad: false,
			 
			proxy: new Ext.data.HttpProxy({
				url: '<?php echo $ro->gen('icinga.cronks.viewProc.json', array('template' => $rd->getParameter('template'))); ?>'
			}),
			
			reader: reader,

			remoteSort:		true,

			paramNames: {
				start:	'page_start',
				limit:	'page_limit',
				dir:	'sort_dir',
				sort:	'sort_field'
			}
		};

		var store = new Object();
		
		if (meta.template.grouping.enabled == true) {

			store_config.sortInfo = sort_array[0];
			store_config.groupField = meta.template.grouping.field;
			store.groupOnSort = true;
			
			store = new Ext.data.GroupingStore(store_config);
		}
		else {
			store = new Ext.data.Store(store_config); 
		}
		
		var h = Ext.getCmp('<?php echo $rd->getParameter('htmlid'); ?>').getHeight()-53;
		
		// Our grid
		var grid_config = {
			store:				store,
			
			trackMouseOver:		false,
	        disableSelection:	false,
	        loadMask:			true,
			
			collapsible:		true,
	        animCollapse:		true,
			frame:				true,

			// If width is null defaults to auto
			// width:				600,
			autoWidth:			true,
			height:				h > 300 ? h : 300,
	
			columns:			column_array
		};

		var view_config = {
            forceFit: true,
            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})'
        };

		if (meta.template.grouping.enabled == true) {
			view_config.hideGroupedColumn = false;
			
			grid_config.view = new Ext.grid.GroupingView(view_config);
		}
		else {
			grid_config.view = new Ext.grid.GridView(view_config);
		}

		// Adding a pager bar if wanted
		if (pager_array.enabled == true) {
			grid_config.bbar = new Ext.PagingToolbar({
				pageSize:		pager_array.size,
				store:			store,
				displayInfo:	true,
				displayMsg:		'Displaying topics {0} - {1} of {2}',
				emptyMsg:		'No topics to display'

				// ,
				// plugins:		new Ext.ux.SlidingPager()
			});

			store.load({params:{page_start: pager_array.start, page_limit: pager_array.size}});
		}
		else {
			store.load();
		}

		var cmp = Ext.getCmp("<?php echo $htmlid; ?>");
		cmp.removeAll(true);
		cmp.add(new Ext.grid.GridPanel(grid_config));
		
		Ext.getCmp('cronk-container').doLayout();
	}

	// First loading the meta info to configure the grid
	Ext.Ajax.request({
		   url: '<?php echo $ro->gen('icinga.cronks.viewProc.json.metaInfo', array('template' => $rd->getParameter('template'))); ?>',
		   success: function(response, opts) {
		      var meta = Ext.decode(response.responseText);
		      loadAjaxGrid(meta); // Build the grid
		   },
		   failure: function(response, opts) {
			   Ext.Msg.alert('Error', 'Could not load template meta information');
		   }
	});
    
// };


// ]]>-->
</script>
