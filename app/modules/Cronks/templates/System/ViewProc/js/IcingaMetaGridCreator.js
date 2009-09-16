
// ---
// KEEP THIS LINE
// ---
 
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
					this.filter_array = new Object();
					this.sortinfo = new Array(2);
	
					var ii = 0;
	
					for (var i=0; i<meta.keys.length; i++) {
						var index = meta.keys[i];
						var field = meta.fields[index];
			 			
						this.mapping_array[i] = {name: index};
						
						// default column array
						this.column_array[i] = {
							header:			field.display['label'],
							dataIndex:		index,
							sortable:		(field.order.enabled ? true : false),
							hidden:			(field.display.visible ? false : true)
						};
	
						if (field.display.width) {
							this.column_array[i].width = field.display.width;
						}
						
						// Apply special config
						if (field.display['Ext.grid.Column']) {
							Ext.apply(this.column_array[i], field.display['Ext.grid.Column']);
						}
			
						// Filling sort info
						if (field.order['default'] == true) {
							this.sort_array[ii] = {
									direction: (field.order.direction ? field.order.direction.toUpperCase() : 'ASC'),
									field: index
								};
			
							ii++;
						}
						
						// Build a filter array
						if (field.filter['enabled'] == true && field.filter['type'] == 'extjs' && field.filter['subtype']) {
							var f = field.filter;
							
							// Switch types
							// f.type = f.subtype;
							// delete(f.subtype);
							
							// Setting the name
							if (!f.name) {
								f.name = index;
							}
							
							// Copy the id
							// if (!f.id) {
								f.id = index;
							// }
							
							// Get the label from the display conf
							if (!f['label']) {
								f['label'] = field.display['label']
							}
							
							this.filter_array[ index ] = f
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
					columns:			this.column_array,
					
					// Custom properties for our custom
					// object
					meta:				this.meta,
					filter:				this.filter_array
				};
				
				var view_config = {
		            forceFit: true,
		            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})'
	        	};
	        	
	        	// Apply special config from xml to vier
	        	if (this.meta.template.option['Ext.grid.GridView']) {
	        		Ext.apply(view_config, this.meta.template.option['Ext.grid.GridView'])
	        	}
	        	
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
				
				// Apply special config from xml to grid
				if (this.meta.template.option['Ext.grid.GridPanel']) {
					Ext.apply(grid_config, this.meta.template.option['Ext.grid.GridPanel']);
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
			
						// Get the first default sort field
						store_config.sortInfo = this.sort_array[0];
						
						store_config.groupField = this.meta.template.grouping.field;
						store_config.groupOnSort = true;
						
						this.meta_store = new Ext.data.GroupingStore(store_config);
					}
					else {
						this.meta_store = new Ext.data.Store(store_config); 
					}
				}
				
				return this.meta_store;
			},
			
			getFilterCfg : function() {
				return this.filter_array || {};
			}
		
		} // END RETURN
}();