/**
 * Single scope object to handle filter changes
 */

Ext.ns('Cronk.util');

Cronk.util.GridFilterWindow = function() {
		var oWin;					// The EXT window
		var oFilter;				// 
		var oCoPanel;				// Formpanel the fields are arranged
		var oCombo;					// Restrictions selector
		var oGrid;					// Grid object created before
		
		var oRestrictions = {};		// The restrictins choosen
		var oOrgBaseParams = {};	// Base params object
		var oComboData = [];		// Data for the combo store
		var oTemplateMeta = {};
		
		var oFilterHandler = new Cronk.FilterHandler();
	
		oFilterHandler.on('compremove', function(fh, panel, meta) {
			var f = getRestrictionsList();
	
			if (!meta.id) {
				return true;
			}
			
			Ext.each(f, function(item, index, ary) {
				if (item[1] == meta.id) {
					
					var r = new Ext.data.Record({
						'fId' : item[0],
						'fType' : item[1],
						'fLabel' : item[2]
					});
					
					if (oGrid.filter_types) {
						delete oGrid.filter_types[ item[1] ];
					}
					
					oCombo.getStore().add([ r ]);
				}
			});
			
			oWindow().doLayout(false, true);
			
			return true;
		});
		
		function oWindow() {
			if (!oWin) {
				oWin = new Ext.Window({
					title: _("Modify filter"),
					closeAction: 'hide',
					width: 500,
					autoHeight: true,
					// layout: 'fit',
					
					defaults: {
						border: false
					},
					
					listeners: {
						render: function(oc) {
							
							if (oGrid.filter_types) {
								var i = 0;
								
								Ext.iterate(oGrid.filter_types, function(key, item) {
									var r = new Ext.data.Record(item);
									
									selectRestrictionHandler(oCombo, r, i);
									i++;
								})
							} 
							
							if (oGrid.filter_params && oCoPanel) {
								Ext.iterate(oGrid.filter_params, function (key, val) {
	//								console.log(key + ": " + val);
									key = key.replace(/^f\[|\]$/g, "");
									var c = oCoPanel.findBy(function(ti) {
										
										if (ti.hiddenName == key || ti.name == key) {
											return true;
										}
										
										return false; 
									});
									
									if (c[0]) {
										c[0].setValue(val);
									}
								});
							}
							
							// Handler to recalculate the window height if
							// adding or removing components
							var armh = function(c, item, index) {
								this.syncSize();
	            				this.syncShadow();
							}
							
							oc.on('add', armh, oc, {delay: 40});
							oc.on('remove', armh, oc, {delay: 40});
						},
						
						afterrender: function() {
							this.doLayout(false, true);
						},
						
						hide: function(oc) {
							oGrid.filter_params = getFormValues(false);
							oGrid.fireEvent('activate');
						}
					},
	
					keys: {
						key: 13,
						fn: function() {
							this.applyFilters();
						},
						scope: pub
					},
	
					bbar: {
						items: [{
							text: _("Apply"),
							iconCls: 'icinga-icon-accept',
							handler: function(b, e) {
								pub.applyFilters();
							}
						},{
							text: _("Discard"),
							iconCls: 'icinga-icon-cross',
							handler: function(b, y) {
								oWin.hide();
							}
						}, '-',{
							text: _("Reset"),
							iconCls: 'icinga-icon-delete',
							handler: function(b, y) {
								pub.resetFilterForm();
							}
						}]
					}
				});
			}
			
			return oWin;
		}
		
		function getRestrictionsList() {
			var fields = [];
			var i=0;
			for (var k in oFilter) {
				fields.push([i++, k, oFilter[k]['label']]);
			}
			return fields;
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
				
				oComboData = getRestrictionsList();
				
				oCombo = new Ext.form.ComboBox({
					
					store: new Ext.data.ArrayStore({
						idIndex: 0,
						fields: ['fId', 'fType', 'fLabel'],
						data: oComboData
					}),
					
					'name': '__restriction_selector',
					
					mode: 'local',
					typeAhead: true,
					triggerAction: 'all',
					forceSelection: false,
					
					
					fieldLabel: _("Add restriction"),
					
					valueField: 'fType',
					displayField: 'fLabel',
					
					listeners: {
						select: selectRestrictionHandler
					}
				});
			
				oCoPanel.add({ layout: 'form', style: 'padding: 5px;', items: oCombo });
				
				// Glue together
				w.add(oCoPanel);
			}	
			
			return true;		
			
		}
		
		function selectRestrictionHandler(oCombo, record, index) {
			var type = record.data['fType'];
			
			// Reset the combo
			oCombo.setValue('');
			
			// Add a new field construct
			addResctriction(type);
			
			// Remove the selected item from the store
			oCombo.getStore().removeAt(index);
			
			var tmp = oGrid.filter_types || {};
			tmp[ record.data['fType'] ] = record.data;
			oGrid.filter_types = tmp;
		}
		
		function addResctriction(type) {
			
			if (oFilter[type]) {
				
				// Create a filter panel component and add them
				// to the form
				oCoPanel.add( oFilterHandler.createComponent( oFilter[type]) );
				
				// Notify about changes
				oCoPanel.doLayout(false, true);
			}
				
		}
		
		function getFormValues(raw) {
			var data = {}
			try {
				data = oCoPanel.getForm().getValues();
			} catch(e) {
				data = {}
			}
			var o = {};
			
			for (var k in data) {
				if (k.indexOf('__') !== 0) {
					o['f[' + k + ']'] = data[k];
				}
			}
			
			return o;
		}
			
		var pub = {
			
			removeRestrictionHandler : function(b, e) {
				
			},
			
			/**
			 * The handler to init the window and show the filter restrictinos
			 */
			startHandler : function(b, e) {
				var win = oWindow();
				win.setPosition(b.el.getLeft(), b.el.getTop());
				win.show(b.el);
			},
			
			/**
			 * Sets the filter cfg parsed from IcingaMetaGridCreator
			 */
			setFilterCfg : function(f) {
				oFilter = f;
				prepareFilter();
			},
			
			/**
			 * Sets the grid object, we need this to apply 
			 * the filter to the store
			 */
			setGrid : function(g) {
				oGrid = g;
				
				if ("originParams" in oGrid.getStore()) {
					oOrgBaseParams = oGrid.getStore().originParams;
				}
				
				oGrid.on('activate', function() {
					if (oCoPanel) {
						oGrid.filter_params = getFormValues(false);
					}
					return true;
				}, this);
				
				oGrid.getStore().on('datachanged', function(store) {
					this.markActiveFilters();
				}, this);
				
				if (oGrid.filter_params) {
					oGrid.on("render",this.applyFilters.createDelegate(this,oGrid.filter_params));
				}
			},
			
			setMeta : function(meta) {
				oTemplateMeta = meta;
				oFilterHandler.setMeta(oTemplateMeta);
			},
			
			/**
			 * If the parent object destroys, destroy our objects too
			 */
			destroyHandler : function() {
				oWindow().hide();
	
				// Objects				
				oWindow().destroy();
				oCoPanel && oCoPanel.destroy();
				
				// Data
				oRestrictions = {};
				oOrgBaseParams = {}
				oFilter = {}; 
			},

			markActiveFilters : function() {
				var btn = Ext.getCmp(oGrid.id+"_filterBtn");
				if(!btn) {
					this.markActiveFilters.defer(200,this,[data]);
					return true;
				}
				
				var i = 0;
				for (var ele in oGrid.filter_params) {
					i++;
				}
			
				if(i)
					btn.addClass("activeFilter");
				else
					btn.removeClass("activeFilter");
			},

			/**
			 * If a restriction was made, this method applies the restrictins
			 * to the store
			 */
			applyFilters : function(owd) {
				var data = owd || getFormValues();
				oGrid.getStore().baseParams = {};
				Ext.apply(oGrid.getStore().baseParams, oOrgBaseParams);
				Ext.apply(oGrid.getStore().baseParams, data);
			
				
				oGrid.getStore().load();
				oGrid.fireEvent('activate');
				
				oWindow().hide();
			},
			
			/**
			 * Reset the base params to its default and reload
			 * the store
			 */
			removeFilters : function() {
				oGrid.getStore().baseParams = oOrgBaseParams;
				oGrid.filter_params = null;
				oGrid.filter_types = null;
				oGrid.getStore().load();
				
				var btn = Ext.getCmp(oGrid.id+"_filterBtn");
				
				if(btn) {
					btn.removeClass("activeFilter");
				}
				
				oFilterHandler.removeAllComponents();
				oGrid.fireEvent('activate');
			},
			
			resetFilterForm : function() {
				oFilterHandler.removeAllComponents();
				oGrid.fireEvent('activate');
			}
			
		};
	
		return pub;
		
};	
