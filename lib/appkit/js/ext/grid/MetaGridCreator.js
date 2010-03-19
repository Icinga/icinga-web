/**
 * This class creates ExtJS grids from json data
 */

Ext.ns('AppKit.Ext.grid');

AppKit.Ext.grid.MetaGridCreator = function(meta) {
	// Url of the store
	this.store_url = undefined;
	// ExtJS store object 
	this.meta_store = undefined;
	// ExtJS reader object
	this.meta_reader = undefined;
	// Template json
	this.meta = {};
	// Extracted column mappings
	this.mapping_array = [];
	// Extracted columns (ColumnModel)
	this.column_array = [];
	// Sorting information
	this.sort_array = [];
	// Info about pager bbar
	this.pager_array = {};
	// Configured filters
	this.filter_array = {};
	// Custom grid events applied after creating the component
	this.grid_events = {};
	// Parameters from the view
	this.params = {};
	// Selection model
	this.selection_model = undefined;
	// Stateuid
	this.stateuid = undefined;
	
	// Store is loaded
	this.storeloaded = false;
	
	this.constructor.call(this, meta);
};

AppKit.Ext.grid.MetaGridCreator.prototype = {
	
	constructor : function(meta) {
		this.setMeta(meta);
	},
	
	setStateUid : function(stateuid) {
		this.stateuid = stateuid;
	},
	
	setMeta : function(meta) {
		Ext.apply(this.meta, meta || {});
	},
	
	setStoreUrl : function(url) {
		this.store_url = url;
	},
	
	createGrid : function() {
		this.parseConfig();
		return this.applyMetaGrid();			
	},
	
	parseConfig : function() {
		Ext.each(this.meta.keys, function(item, index, arry) {
			
			var field = this.meta.fields[item];
			
			this.mapping_array[index] = { name : item };
			
			this.column_array[index] = {
				header:			field.display['label'],
				dataIndex:		item,
				sortable:		(field.order.enabled ? true : false),
				hidden:			(field.display.visible ? false : true)
			};
			
			// Column width
			if (field.display.width) {
				this.column_array[index].width = field.display.width;
			}
			
			// Apply special config
			if (field.display['Ext.grid.Column']) {
				Ext.apply(this.column_array[index], field.display['Ext.grid.Column']);
			}
			
			// Filling sort info
			if (field.order['default'] == true) {
				this.sort_array.push({
					direction: (field.order.direction ? field.order.direction.toUpperCase() : 'ASC'),
					field: item
				});
			}
			
			// Adding javascript methods
			if (field.display['jsFunc']) {
				this.addGridMethods(field.display['jsFunc'], index, item);
			}
			
			// Build a filter array
			if (field.filter['enabled'] == true && field.filter['type'] == 'extjs' && field.filter['subtype']) {
				var f = field.filter;
				// Setting the name
				f.name = (f.name ? f.name : item);
				f.id = item;
				f['label'] = (f['label'] ? f['label'] : field.display['label']);
				this.filter_array[ item ] = f;
			}
			
		}, this);
		
		// If global filter definitions exist, add them to the array
		if ("filter" in this.meta.template.option) {
			Ext.iterate(this.meta.template.option.filter, function(k, v) {
				if (v['enabled'] == true && v['type'] == 'extjs') {
					var f = v;
					f.name = (f.name ? f.name : k);
					f.id = k;
					f['label'] = (f['label'] ? f['label'] : "NO LABEL");
					this.filter_array[ k ] = f
				}
			}, this);
		}
		
		Ext.apply(this.pager_array, {
			enabled : this.meta.template.pager.enabled || false ,
			size : this.meta.template.pager.size || 25,
			start : this.meta.template.pager.start || 0
		});
	},
	
	applyMetaGrid : function() {
		
		// Our grid		
		var grid_config = {
			store:				this.getMetaStore(),	
			trackMouseOver:		false,
			disableSelection:	false,
			loadMask:			true,
			collapsible:		false,
			animCollapse:		true,
			border:				false,
			emptyText:			"No data was found ...",
						
			// Custom properties for our custom
			// object
			meta:				this.meta,
			filter:				this.filter_array
		};
		
		// Stateful handling
		if (this.stateuid) {
			Ext.apply(grid_config, {
				stateful: true,
				stateId: this.stateuid,
				stateEvents: ['activate'],
				
				getState: function() {
					
					var o = {
						filter_params: this.filter_params || {},
						filter_types: this.filter_types || {}
					};
					
					var e = true;
					for (var i in o.filter_params) { e=false; break; }
					for (var i in o.filter_types) { e=false; break; }
					
					if (e == false) {
						return o;
					}
				},
				
				applyState: function(state) {
					if (state.filter_params) {
						this.filter_params = state.filter_params;
					}
					
					if (state.filter_types) {
						this.filter_types = state.filter_types;
					}
					
					return true;
				}
			});
		}
		
		// Add the selection model:
		if (this.meta.template.option.selection_model) {
			switch (this.meta.template.option.selection_model) {
				case 'checkbox':
					var sm = new Ext.grid.CheckboxSelectionModel();
					grid_config['selModel'] = sm;
					var tmp_col = [];
					tmp_col.push(sm);
					Ext.each(this.column_array, function(item, i, ary) { tmp_col.push(item) });
					this.column_array = tmp_col;
				break;
			}
		}
		
		// So we can modify this before
		grid_config['columns'] = this.column_array;
		
		var view_config = {
            forceFit: true,
            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})'
    	};
    	
    	// Apply special config from xml to vier
    	if (this.meta.template.option['Ext.grid.GridView']) {
    		Ext.apply(view_config, this.meta.template.option['Ext.grid.GridView'])
    	}
    	
    	if (this.meta.template.grouping.enabled == true) {
			
			// Add xml options to our grouping view
			if (this.meta.template.grouping['Ext.grid.GroupingView']) {
				Ext.apply(view_config, this.meta.template.grouping['Ext.grid.GroupingView']);
			}
			
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
			});
		}
		
		// Loading only if not explicit wanted
		if (!("storeDisableAutoload" in this.params) && !this.params["storeDisableAutoload"]) {
			this.getMetaStore().load();
		}
		
		// Apply special config from xml to grid
		if (this.meta.template.option['Ext.grid.GridPanel']) {
			Ext.apply(grid_config, this.meta.template.option['Ext.grid.GridPanel']);
		}
		
		var grid =  new AppKit.Ext.grid.GridPanel(grid_config);
		
		this.applyEventsToGrid(grid);
		
		return grid;
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
				autoDestroy: true,
				 
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
			
			if (this.pager_array.enabled == true) {
				store_config.baseParams = {
					page_start: this.pager_array.start,
					page_limit: this.pager_array.size
				}
			}

			this.meta_store = {};
			
			if (this.meta.template.grouping.enabled == true) {
	
				// Get the first default sort field
				store_config.sortInfo = this.sort_array[0];
				
				store_config.groupField = this.meta.template.grouping.field;
				store_config.groupOnSort = true;
				
				if (this.meta.template.grouping['Ext.data.GroupingStore']) {
					Ext.applyIf(store_config, this.meta.template.grouping['Ext.data.GroupingStore']);
				}
				
				this.meta_store = new Ext.data.GroupingStore(store_config);
			}
			else {
				this.meta_store = new Ext.data.Store(store_config); 
			}
		}
		
		this.meta_store.on('beforeload', function() {
			this.storeloaded = true;
			return true;
		}, this, { single: true });
		
		return this.meta_store;
	},
	
	getFilterCfg : function() {
		return this.filter_array || {};
	},
	
	getOptions : function() {
		return this.meta.template.option || {};
	},
	
	addGridMethods : function(config, colIndex, colItem) {
		Ext.each(config, function(item, fIndex, allItem) {
			if (item['function'] && item['namespace']) {
				
				var ns = eval(item['namespace']);
				if (ns && typeof ns[ item['function'] ] == "function") {
					// The config for the function
					var cfg = item['arguments'] || {};
					cfg["field"] = colItem;
					
					// Get our function
					var f = ns[ item['function'] ].call(this, cfg);
					
					// Adding a renderer
					if (!item['type'] || item['type'] == 'renderer') {
						// And add them to out column model array
						this.column_array[colIndex].renderer = {
							fn: f,
							scope: ns
						}
					}
					else if (item['type'] == 'grouprenderer') {
						this.column_array[i].groupRenderer = {
							fn: f,
							scope: ns
						}
					}
					else {
						this.addGridEvent(item['type'], f, ns);
					}
					
				}
			
			}
		}, this);
	},
	
	addGridEvent : function(type, fn, ns) {
		if (!this.grid_events[type]) {
			this.grid_events[type] = [];
		}
		this.grid_events[type].push({
			fn: fn,
			scope: ns
		});
	},
	
	applyEventsToGrid : function(grid) {
		Ext.iterate(this.grid_events, function(e, arry) {
			Ext.each(arry, function(item, index, allArry) {
				if (typeof item.fn == "function") {
					grid.on(e, item.fn, item.scope || window);
				}
			});
		})
	},
	
	setParameters : function(p) {
		Ext.apply(this.params, p);
	},
	
	storeIsLoaded : function() {
		return this.storeloaded
	},
	
	initStore : function() {
		this.getMetaStore().load();
	}
};
