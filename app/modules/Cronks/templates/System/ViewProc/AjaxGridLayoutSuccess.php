<?php 
	$htmlid = $rd->getParameter('htmlid');
?>
<script type="text/javascript">
<!-- // <![CDATA[

if (Ext.get("<?php echo $htmlid; ?>")) {
	
	AppKit.Ext.Widgets.IcingaAjaxGridPanel = Ext.extend(Ext.grid.GridPanel, {
		meta : {},
		filter: {},
		
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
		
		setMeta : function(m) {
			this.meta = m;
		},
		
		setFilter : function(f) {
			this.filter = f;
		}
		
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
						
						if (!f.name) {
							f.name = index;
						}
						
						if (!f.id) {
							f.id = index;
						}
						
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
	
	// The filter window object
	var IcingaGridFilterWindow = function() {
		var oWin;
		var oFilter;
		var oCoPanel;
		var oTargetPanel;
		var oGrid;
		var oRestrictions = {};
		
		function oWindow() {
			if (!oWin) {
				oWin = new Ext.Window({
					title: '<?php echo $tm->_("Modify filter"); ?>',
					// width: 200,
					// height: 200,
					closeAction: 'hide',
					width: 500,
					layout: 'fit',
					
					defaults: {
						border: false
					},
					
					listeners: {
						add: function(co, oNew, index) {
							co.doLayout();
						}
					},
					
					bbar: {
						items: [{
							text: '<?php echo $tm->_("Apply"); ?>',
							iconCls: 'silk-accept',
							handler: function(b, e) {
								IcingaGridFilterWindow.applyFilters();
							}
						},{
							text: '<?php echo $tm->_("Discard"); ?>',
							iconCls: 'silk-cross'
						}]
					}
				});
			}
			
			return oWin;
		}
		
		function prepareFilter() {
			var w = oWindow();
			
			if (!oCoPanel) {
				
				oCoPanel = new Ext.form.FormPanel({
					id: 'filter-' + oGrid.getId(),
					
					defaults: {
						border: false
					}
				});
				
				var fields = [];
				var i=0;
				for (var k in oFilter) {
					fields.push([i++, k, oFilter[k]['label']]);
				}
				
				var oCombo = new Ext.form.ComboBox({
					
					store: new Ext.data.ArrayStore({
						id: 0,
						fields: ['fId', 'fType', 'fLabel'],
						data: fields
					}),
					
					name: '__restriction_selector',
					
					mode: 'local',
					typeAhead: true,
					triggerAction: 'all',
					forceSelection: true,
					
					
					fieldLabel: '<?php echo $tm->_("Add restriction"); ?>',
					
					valueField: 'fType',
					displayField: 'fLabel',
					
					listeners: {
						select: function(oCombo, record, index) {
							oCombo.setValue('');
							addResctriction(record.data['fType']);
						}
					}
				});
			
				oCoPanel.add({ layout: 'form', style: 'padding: 5px;', items: oCombo });
				
				// Glue together
				w.add(oCoPanel);
			}	
			
			return true;		
			
		}
		
		function addResctriction(type) {
			if (oFilter[type]) {
				
				oCoPanel.add( AppKit.Ext.FilterHandler.createComponent(oFilter[type]) );
				oCoPanel.doLayout();
			}
				
		}
		
		function getFormValues() {
			var data = oCoPanel.getForm().getValues();
			var o = {};
			
			for (var k in data) {
				if (k.indexOf('__') !== 0) {
					o['f[' + k + ']'] = data[k];
				}
			}
			
			return o;
		}
			
		return {
			
			// Our clickhandler to start the window
			startHandler : function(b, e) {
				var win = oWindow();
				win.setPosition(b.el.getLeft(), b.el.getTop());
				win.show(b.el);
			},
			
			setFilterCfg : function(f) {
				oFilter = f;
				prepareFilter();
			},
			
			setGrid : function(g) {
				oGrid = g;
			},
			
			destroyHandler : function() {
				oWindow().hide();
				oWindow().destroy();
			},
			
			applyFilters : function() {
				var data = getFormValues();
				
				oWindow().hide();
				
				oGrid.getStore().baseParams = data;
				
				oGrid.getStore().reload();
			}
			
		}
		
	}();
	
	function loadAjaxGrid(meta) {	

		IcingaMetaGridCreator.setStoreUrl("<?php echo $ro->gen('icinga.cronks.viewProc.json', array('template' => $rd->getParameter('template'))); ?>");
		var grid = IcingaMetaGridCreator.createGridFrom(meta);
		
		
		IcingaGridFilterWindow.setGrid(grid);
		IcingaGridFilterWindow.setFilterCfg( IcingaMetaGridCreator.getFilterCfg() );
		
		// Distribute destroy events
		grid.on('destroy', function() {
			IcingaGridFilterWindow.destroyHandler();
		});
		
		// Add the window to a toolbar button
		grid.on('render', function(g) {
			
			if (meta.template.option.mode == 'minimal') {
				this.topToolbar.hide();
			}
			else {
			
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
								handler: function(b, e) {
									this.getStore().baseParams = {};
									this.getStore().reload();
								},
								scope: this
							}]
						}
					}
				]);
			
			}
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
