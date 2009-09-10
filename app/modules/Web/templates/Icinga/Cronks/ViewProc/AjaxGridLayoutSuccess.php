<?php 
	$htmlid = $rd->getParameter('htmlid');
?>
<script type="text/javascript">
<!-- // <![CDATA[

if (Ext.get("<?php echo $htmlid; ?>")) {
	
	AppKit.Ext.Widgets.IcingaAjaxGridPanel = Ext.extend(Ext.grid.GridPanel, {

		initComponent : function() {
			this.tbar = this.buildTopToolbar();
			
			AppKit.Ext.Widgets.IcingaAjaxGridPanel.superclass.initComponent.call(this);
		},
	
		// Top toolbar of the grid
		buildTopToolbar : function() {
			return [{
				text: '<?php echo $tm->_("Refresh"); ?>',
				iconCls: 'silk-arrow-refresh',
				tooltip: '<?php echo $tm->_("Refresh the data in the grid"); ?>',
				handler: function(oBtn, e) { this.store.reload(); },
				scope: this
			}];
		},
		

	
	});
	
	var IcingaMetaGridCreator = function() {
		return {

		store_url : undefined, 
		meta_store : undefined,
		meta_reader : undefined,
			
		setStoreUrl : function(url) {
			this.store_url = url;
		},
		
		createGridFrom : function(meta) {
				this.meta = meta;
				
				this.mapping_array = new Array(meta.keys.length);
				this.column_array = new Array(meta.keys.length);
				this.sort_array = new Array(meta.keys.length);
				this.pager_array = new Array(3);
				this.filter_array = new Array(meta.keys.length);
				this.sortinfo = new Array(2);

				var ii = 0;

				for (var i=0; i<meta.keys.length; i++) {
					var index = meta.keys[i];
					var field = meta.fields[index];
		 			
					this.mapping_array[i] = {name: index};
					
					this.column_array[i] = {
						header:			field.display.label,
						width:			(field.display.width ? field.display.width : 120),
						dataIndex:		index,
						sortable:		(field.order.enabled ? true : false),
						hidden:			(field.display.visible ? false : true)
					};
		
					if (field.order['default'] == true) {
						this.sort_array[ii] = {
								direction: (field.order.direction ? field.order.direction.toUpperCase() : 'ASC'),
								field: index
							};
		
						ii++;
					}
				}
				
				return this.applyMetaGrid();			
		},
		
		applyMetaGrid : function() {
			// Our grid
			var grid_config = {
				store:				this.getMetaStore(),	
				trackMouseOver:		false,
				disableSelection:	false,
				loadMask:			true,
				collapsible:		true,
				animCollapse:		true,
				border:				false,
				columns:			this.column_array
			};
			
			var view_config = {
	            forceFit: true,
	            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})'
        	};
        	
        	if (this.meta.template.grouping.enabled == true) {
				view_config.hideGroupedColumn = false;
			
				grid_config.view = new Ext.grid.GroupingView(view_config);
			}
			else {
				grid_config.view = new Ext.grid.GridView(view_config);
			}
			
			// Adding a pager bar if wanted
			if (this.pager_array.enabled == true) {
				grid_config.bbar = new Ext.PagingToolbar({
					pageSize:		this.pager_array.size,
					store:			this.getMetaStore(),
					displayInfo:	true,
					displayMsg:		'Displaying topics {0} - {1} of {2}',
					emptyMsg:		'No topics to display'
	
					// ,
					// plugins:		new Ext.ux.SlidingPager()
				});
	
				this.getMetaStore().load({params:{page_start: pager_array.start, page_limit: pager_array.size}});
			}
			else {
				this.getMetaStore().load();
			}
			
			return new AppKit.Ext.Widgets.IcingaAjaxGridPanel(grid_config);
		},
		
		getMetaMapping : function() {
			return Ext.data.Record.create(this.mapping_array);
		},
		
		getMetaReader : function() {
			if (!this.meta_reader) {
				// Readerconfig
				var reader_config = {
						root:				'resultRows',
						totalProperty:		'resultCount',
						successProperty:	'resultSuccess'
				};
		
				if (this.meta.template.datasource.id) {
					reader_config.idProperty = this.meta.template.datasource.id;
				}
		
				this.meta_reader = new Ext.data.JsonReader(reader_config, this.getMetaMapping());
			}
			
			return this.meta_reader;
		},
		
		getMetaStore : function() {
			if (!this.meta_store) {
				var store_config = { 
					autoLoad: false,
					 
					proxy: new Ext.data.HttpProxy({
						url: this.store_url
					}),
					
					reader:			this.getMetaReader(),
		
					remoteSort:		true,
		
					paramNames: {
						start:	'page_start',
						limit:	'page_limit',
						dir:	'sort_dir',
						sort:	'sort_field'
					}
				};
	
				this.meta_store = new Object();
				
				if (this.meta.template.grouping.enabled == true) {
		
					store_config.sortInfo = sort_array[0];
					store_config.groupField = this.meta.template.grouping.field;
					store.groupOnSort = true;
					
					this.meta_store = new Ext.data.GroupingStore(store_config);
				}
				else {
					this.meta_store = new Ext.data.Store(store_config); 
				}
			}
			
			return this.meta_store;
		}
		}
	}();
	
	// The filter window object
	var IcingaGridFilterWindow = function() {
		var oWin;
		
		function oWindow() {
			if (!oWin) {
				oWin = new Ext.Window({
					title: '<?php echo $tm->_("Modify filter"); ?>',
					width: 200,
					height: 200,
					closeAction: 'hide',
					layout: 'form'
				});
			}
			
			return oWin;
		} 
			
		return {
			
			// Our clickhandler to start the window
			startHandler : function(b, e) {
				var win = oWindow();
				win.setPosition(b.el.getLeft(), b.el.getTop());
				win.show(b.el);
			}
			
		}
		
	}();
	
	function loadAjaxGrid(meta) {	

		IcingaMetaGridCreator.setStoreUrl("<?php echo $ro->gen('icinga.cronks.viewProc.json', array('template' => $rd->getParameter('template'))); ?>");
		var grid = IcingaMetaGridCreator.createGridFrom(meta);
		
		// Add the window to a toolbar button
		grid.on('render', function(g) {
			this.topToolbar.add([
				'-', {
					text: '<?php echo $tm->_("Filter"); ?>',
					iconCls: 'silk-pencil',
					menu: { 
						items: [{ 
							text: '<?php echo $tm->_("Modify"); ?>', 
							iconCls: 'silk-application-form',
							handler: IcingaGridFilterWindow.startHandler,
							scope: this
						},{ 
							text: '<?php echo $tm->_("Re-apply"); ?>', 
							iconCls: 'silk-building-go',
							handler: AppKit.Ext.bogusHandler,
							scope: this
						},{ 
							text: '<?php echo $tm->_("Remove"); ?>', 
							iconCls: 'silk-cancel',
							handler: AppKit.Ext.bogusHandler,
							scope: this
						}]
					}
				}
			]);
		});
		
		//Insert the grid in the parent
		var cmp = Ext.getCmp("<?php echo $htmlid; ?>");
		cmp.insert(0, grid);
		
		// Refresh the container layout
		Ext.getCmp('view-container').doLayout();
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
    
};


// ]]>-->
</script>
