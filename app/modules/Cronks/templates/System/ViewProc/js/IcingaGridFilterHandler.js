
// ---
// KEEP THIS LINE
// ---

/**
 * Single scope object to handle filter changes
 */
var IcingaGridFilterWindow = function() {
	var oWin;					// The EXT window
	var oFilter;				// 
	var oCoPanel;				// Formpanel the fields are arranged
	var oCombo;					// Restrictions selector
	var oGrid;					// Grid object created before
	
	var oRestrictions = {};		// The restrictins choosen
	var oOrgBaseParams = {};	// Base params object
	var oComboData = [];		// Data for the combo store
	var oTemplateMeta = {};
	
	var oFilterHandler = new AppKit.Ext.FilterHandler();

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
				
				oCombo.getStore().add([ r ]);
			}
		});
		
		oWindow().doLayout();
		
		return true;
	});
	
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
						iconCls: 'silk-cross',
						handler: function(b, y) {
							oWin.hide();
						}
					}, '-',{
						text: '<?php echo $tm->_("Reset"); ?>',
						iconCls: 'silk-delete',
						handler: function(b, y) {
							IcingaGridFilterWindow.resetFilterForm();
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
				forceSelection: true,
				
				
				fieldLabel: '<?php echo $tm->_("Add restriction"); ?>',
				
				valueField: 'fType',
				displayField: 'fLabel',
				
				listeners: {
					select: function(oCombo, record, index) {
						var type = record.data['fType'];
						
						// Reset the combo
						oCombo.setValue('');
						
						// Add a new field construct
						addResctriction(type);
						
						// Remove the selected item from the store
						oCombo.getStore().removeAt(index);
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
			
			// Create a filter panel component and add them
			// to the form
			oCoPanel.add( oFilterHandler.createComponent( oFilter[type]) );
			
			// Notify about changes
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
			oOrgBaseParams = oGrid.getStore().baseParams;
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
		
		/**
		 * If a restriction was made, this method applies the restrictins
		 * to the store
		 */
		applyFilters : function() {
			var data = getFormValues();

			oGrid.getStore().baseParams = {};
			Ext.apply(oGrid.getStore().baseParams, oOrgBaseParams);
			Ext.apply(oGrid.getStore().baseParams, data);
			
			oGrid.getStore().reload();
			
			oWindow().hide();
		},
		
		/**
		 * Reset the base params to its default and reload
		 * the store
		 */
		removeFilters : function() {
			oGrid.getStore().baseParams = oOrgBaseParams;
			oGrid.getStore().reload();
		},
		
		resetFilterForm : function() {
			oFilterHandler.removeAllComponents();
		}
		
	};

	return pub;
	
}();