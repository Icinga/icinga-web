/**
 * This class creates ExtJS grids from json data
 */

Ext.ns('Cronk.grid');

Cronk.grid.MetaGridCreator = function(meta) {
	
	// PUBLIC INTERFACE
	var pub = this;
	
	// CALING THE PARENT
	Cronk.grid.MetaGridCreator.superclass.constructor.call(this);
	
	/*
	 * PRIVATE INTERFACE
	 */
	
	var
	grid_events = {},
		
	addGridMethods = function(config, colIndex, colItem) {

		Ext.each(config,function(item, fIndex, allItem) {
			addConfigGridMethod(item, fIndex, allItem,colItem,colIndex);
		}, this);
	
	},

	addConfigGridMethod  = function(item, fIndex, allItem, colItem,colIndex) {
		/**
		 * Fixes timing issues - when the callback function is not yet initialized the function
		 * will be called later on
		 */
		var cb = createGridCallback(item, colItem);
		if(cb) {
			if (Ext.isEmpty(item.type)) {
				item.type = 'renderer';
			}

			if (item.type == 'renderer') {
				pub.column_array[colIndex].renderer = cb;
			}
			else if (item.type == 'grouprenderer') {
				pub.column_array[i].groupRenderer = cb;
			}
			else {
				addGridEvent(item, cb);
			}
		} else {
			addConfigGridMethod.defer(100,this,[item, fIndex, allItem, colItem,colIndex])
		}

	},

	createGridCallback = function(struct, columnName) {
		
		// AppKit.log(struct);
		try {
			if (Ext.isEmpty(struct.type)) {
				struct.type = 'renderer';
			}

			if (!Ext.isEmpty(struct['function'])) {

				var ns = null;
				var f = null;

				if (!Ext.isEmpty(struct.namespace)) {
					ns = struct.namespace;
					f = Ext.decode(struct.namespace + '.' + struct['function']);
				}
				else {
					f = Ext.decode(struct['function']);

				}

				if (!Ext.isEmpty(columnName)) {
					var c = Ext.apply({field: columnName}, struct['arguments'] || {});
					var tmp = f.call(this, c);
					f = tmp;
				}
				else {

					var c = {
						oGrid: pub.getGrid(),
						oStore: pub.getStore(),
						oMeta : pub.getMeta(),
						oCreator: pub
					};

					Ext.apply(c, struct['arguments'] || {});
					var tmp = f.createDelegate(ns || f, [c], true);

					f = tmp;
				}

				if (Ext.isFunction(f)) {
					return {
						fn: f,
						scope: (ns || f)
					};
				}

				throw("createGridCallback: no function comes back!");
			}
		} catch(e) {
			switch(e) {
				//check and rethrow
				case "createGridCallback: no function comes back!":
					throw("createGridCallback: no function comes back!");
					break;
				default:
					return false;
			}
		}
	},
		
	addGridEvent = function(struct, cb) {
		
		if (Ext.isEmpty(struct.type)) {
			throw("Type is needed");
		}
		
		if (!grid_events[struct.type]) {
			grid_events[struct.type] = [];
		}
		
		if (Ext.isEmpty(cb)) {
			cb = createGridCallback(struct);
		}
		
		if (Ext.isEmpty(cb.fn)) {
			throw("Missing fn in callback");
		}
		
		grid_events[struct.type].push(cb);
		
		return cb; 
	},
		
	applyEventsToGrid = function(grid) {
		
		if (!Ext.isEmpty(pub.meta.template.option.gridEvents)) {
			
			// AppKit.log('GridEvents', pub.meta.template.option.gridEvents);
			
			Ext.iterate(pub.meta.template.option.gridEvents, function(k, v) {
				addGridEvent(k);				
			}, this);
		}
	
		Ext.iterate(grid_events, function(e, arry) {
			Ext.each(arry, function(item, index, allArry) {
				if (!Ext.isEmpty(item.fn) && Ext.isFunction(item.fn)) {
                    
					grid.on(e, item.fn, item.scope || window);
				}
			});
		});
		
		// Weired manual bubbling to gather needed information from the store
		var bubbleEvent = (function() { try { pub.grid.fireEvent('activate'); } catch (e) {} }).createDelegate(this);
		pub.grid.on('render', function() {
			pub.grid.getStore().on('datachange', function() {
				pub.grid.getStore().on('datachange', bubbleEvent);
				bubbleEvent();
			}, pub, { single: true });
			bubbleEvent();
		}, pub, { single: true });
		
	},
	
	parseConfig = function() {
		Ext.each(pub.meta.keys, function(item, index, arry) {
			
			var field = pub.meta.fields[item];
			
			pub.mapping_array[index] = { name : item };
			
			pub.column_array[index] = {
				header:			(field.display['icon'] ? '<div class="icon-16 '+field.display['icon']+'"></div>' : "")+
                                (field.display['label'] || ""),
				dataIndex:		item,
				sortable:		(field.order.enabled ? true : false),
				hidden:			(field.display.visible ? false : true)
			};
			
			// Column width
			if (field.display.width) {
				pub.column_array[index].width = field.display.width;
			}
			
			// Apply special config
			if (field.display['Ext.grid.Column']) {
				Ext.apply(pub.column_array[index], field.display['Ext.grid.Column']);
			}
			
			// Filling sort info
			if (field.order['default'] == true) {
				pub.sort_array.push({
					direction: (field.order.direction ? field.order.direction.toUpperCase() : 'ASC'),
					field: item
				});
			}
			
			// Adding javascript methods
			if (field.display['jsFunc']) {
				addGridMethods(field.display['jsFunc'], index, item);
			}
			
			// Build a filter array
			if (field.filter['enabled'] == true && field.filter['type'] == 'extjs' && field.filter['subtype']) {
				var f = field.filter;
				// Setting the name
				f.name = (f.name ? f.name : item);
				f.id = item;
				f['label'] = (f['label'] ? f['label'] : field.display['label']);
				pub.filter_array[ item ] = f;
			}
			
		}, this);
		
		// If global filter definitions exist, add them to the array
		if ("filter" in pub.meta.template.option) {
			Ext.iterate(pub.meta.template.option.filter, function(k, v) {
				if (v['enabled'] == true && v['type'] == 'extjs') {
					var f = v;
					f.name = (f.name ? f.name : k);
					f.id = k;
					f['label'] = (f['label'] ? f['label'] : "NO LABEL");
					pub.filter_array[ k ] = f
				}
			}, this);
		}
		
		Ext.apply(pub.pager_array, {
			enabled : pub.meta.template.pager.enabled || false ,
			size :  parseInt(AppKit.getPrefVal('org.icinga.grid.pagerMaxItems'),10) || 25,
			start : pub.meta.template.pager.start || 0
		});
		
	},
	
	applyMetaGrid = function() {
		
		// Our grid		
		var grid_config = {
			store:				pub.getMetaStore(),	
			trackMouseOver:		false,
			disableSelection:	false,
			loadMask:			true,
			collapsible:		false,
			animCollapse:		true,
			border:				false,
			emptyText:			"No data was found ...",
			layout:				'fit',

			// Custom properties for our custom
			// object
			meta:				pub.meta,
			filter:				pub.filter_array,
			parentCmp:			this
		};

		// Stateful handling
		if (pub.stateuid) {
			Ext.apply(grid_config, {
				stateful: Ext.isDefined(pub.initialstate) ? false : true,
				stateId: pub.stateuid
			});
		}
		
		// Add the selection model:
		if (pub.meta.template.option.selection_model) {
			switch (pub.meta.template.option.selection_model) {
				case 'checkbox':
					var sm = new Ext.grid.CheckboxSelectionModel();
					grid_config['selModel'] = sm;
					var tmp_col = [];
					tmp_col.push(sm);
					Ext.each(pub.column_array, function(item, i, ary) { tmp_col.push(item) });
					pub.column_array = tmp_col;
				break;
			}
		}
		
		// So we can modify this before
		grid_config['columns'] = pub.column_array;
		
		var view_config = {
            forceFit: true,
            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})'
    	};
    	
    	// Apply special config from xml to vier
    	if (pub.meta.template.option['Ext.grid.GridView']) {
    		Ext.apply(view_config, pub.meta.template.option['Ext.grid.GridView']);
    	}
    	
    	if (pub.meta.template.grouping.enabled == true) {
			
			// Add xml options to our grouping view
			if (pub.meta.template.grouping['Ext.grid.GroupingView']) {
				Ext.apply(view_config, pub.meta.template.grouping['Ext.grid.GroupingView']);
			}
			
			grid_config.view = new Ext.grid.GroupingView(view_config);
		}
		else {
			grid_config.view = new Ext.grid.GridView(view_config);
		}
		
		// Adding a pager bar if wanted
		if (pub.pager_array.enabled == true) {
			grid_config.bbar = new Ext.PagingToolbar({
				pageSize:		parseInt(AppKit.getPrefVal('org.icinga.grid.pagerMaxItems'),10),
				store:			pub.getMetaStore(),
				displayInfo:	true,
				displayMsg:		_('Displaying topics {0} - {1} of {2}'),
				emptyMsg:		_('No topics to display')
			});
		}
		
		// Loading only if not explicit wanted
		if (!("storeDisableAutoload" in pub.params) && !pub.params["storeDisableAutoload"]) {
			pub.getMetaStore().load();
		}

		// Apply special config from xml to grid
		if (pub.meta.template.option['Ext.grid.GridPanel']) {
			Ext.apply(grid_config, pub.meta.template.option['Ext.grid.GridPanel']);
		}
		
		pub.grid =  new Cronk.grid.GridPanel(grid_config);

		// Start autoloading
		if (!Ext.isEmpty(pub.params.autoRefresh)) {
			var i = parseInt(AppKit.getPrefVal('org.icinga.grid.refreshTime'),10)*1000;
			var gridRefreshTask = {
				run: function() {
					this.getStore().reload();
				},
				interval: i,
				scope: pub.grid
			};

			AppKit.getTr().start(gridRefreshTask);
		}
		
		applyEventsToGrid(pub.grid);
		
		if (Ext.isDefined(pub.initialstate)) {
			pub.grid.applyState(pub.initialstate);
		}
		
		return pub.grid;
	};

	
	/*
	 * PUBLIC
	 */
	
	Ext.apply(pub, {
		// Url of the store
		store_url : undefined,
		// ExtJS store object 
		meta_store : undefined,
		// ExtJS reader object
		meta_reader : undefined,
		// Template json
		meta : {},
		// Extracted column mappings
		mapping_array : [],
		// Extracted columns (ColumnModel)
		column_array : [],
		// Sorting information
		sort_array : [],
		// Info about pager bbar
		pager_array : {},
		// Configured filters
		filter_array : {},
		// Custom grid events applied after creating the component
		grid_events : {},
		// Parameters from the view
		params : {},
		// Selection model
		selection_model : undefined,
		// Stateuid
		stateuid : undefined,
		// Initial state from cronk configuration
		initialstate : undefined,
		// Store is loaded
		storeloaded : false,
		// The grid object
		grid : null,
		
		createGrid : function() {
			parseConfig();
			return applyMetaGrid();			
		},
		
		getGrid : function() {
			return this.grid;
		},
		
		setMeta : function(meta) {
			Ext.apply(this.meta, meta || {});
		},
		
		getMeta : function() {
			return this.meta;
		},
		
		setStateUid : function(stateuid) {
			this.stateuid = stateuid;
		},
		
		setInitialState : function(state) {
			this.initialstate = state;
		},
		
		setStoreUrl : function(url) {
			this.store_url = url;
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
		
		getStore : function() {
			return this.meta_store;
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
						page_start: parseInt(this.pager_array.start,10),
						page_limit: parseInt(this.pager_array.size,10)
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

		setParameters : function(p) {
			Ext.apply(this.params, p);
		},
		
		storeIsLoaded : function() {
			return this.storeloaded
		},
		
		initStore : function() {
			this.getMetaStore().load();
		}
	});
	
	/*
	 * -------
	 */
	
	if (!Ext.isEmpty(meta)) {
		pub.setMeta(meta);
	}
	
	// Return the interface
	return pub;
};

Ext.extend(Cronk.grid.MetaGridCreator, Object, {
	
});

// ---
