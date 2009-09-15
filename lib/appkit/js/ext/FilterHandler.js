
AppKit.Ext.FilterHandler = function() {	
	
	var oFilterOp = {
		'appkit.ext.filter.text': 'text',
		'appkit.ext.filter.number': 'number',
	};
	
	var oOpList = {
		text: [
			[60, 'contain'],
			[61, 'doesn\'t contain'],
			[50, 'is'],
			[51, 'isn\'t']
		]	,
		
		number: [
			[50, 'is'],
			[51, 'isn\'t'],
			[70, 'less than'],
			[71, 'greater than']
		]
	};
	
	var oOpDefault = {
		number: 50,
		text: 60
	};
	
	function componentDispatch(meta) {
		
		var panel = new Ext.Panel({
			id: 'fco' + meta.id,
			border: false,
			style: 'padding: 2px;',
			layout: 'column',
			
			defaults: {
				border: false,
				style: 'padding: 2px;'				
			}
		});
		
		// Adding the label
		panel.add([
			{items: getLabelComponent(meta), columnWidth: .19 },
			{items: getOperatorComponent(meta), columnWidth: .3},
			{items: getFilterComponent(meta), columnWidth: .4 },
			{items: getRemoveComponent(meta), columnWidth: .1}
		]);
		
		return panel;
		
	};
	
	function getRemoveComponent(meta) {
		return new Ext.Button({
			xtype: 'button',
			iconCls: 'silk-cross',
			handler: function(b, e) {
				var p = Ext.getCmp('fco' + meta.id);
				if (p) {
					p.findParentByType('form').remove(p);
				}
			}
		});
	};
	
	function getLabelComponent(meta) {
		return new Ext.Panel({
			html: meta['label'],
			border: false
		});
	};
	
	function getOperatorComponent(meta) {
		var  type = null;
		
		// Disable the operator
		if (meta.no_operator && meta.no_operator == true) {
			return new Ext.Panel({ border: false });
		} 
		
		if (meta.operator_type) {
			type = meta.operator_type;
		}
		
		if (!type) {
			type = oFilterOp[meta.subtype];
		}
		
		// Data and corresponding store
		var data = oOpList[type];
		
		var store = new Ext.data.ArrayStore({
			id: 0,
			fields: ['id', 'label'],
			data: data
		});
		
		// this is our combo field
		var oCombo = new Ext.form.ComboBox({
			store: store,
			
			mode: 'local',
			typeAhead: true,
			triggerAction: 'all',
			forceSelection: true,
					
			fieldLabel: '<?php echo $tm->_("Operator"); ?>',
			valueField: 'id',
			displayField: 'label',
			width: 110,
			
			hiddenName: meta.name + '-operator',
			
			name: '__' + meta.name + '-operator',
			id: meta.name + '-operator'
			
		});
		
		// Set the default value after rendering
		oCombo.on('render', function(c) {
			c.setValue(oOpDefault[type]);
		});
		
		// Pack all together in a container
		var p = new Ext.Panel({border: false});
		p.add(oCombo);
		return p;
	};
	
	function getFilterComponent(meta) {
		
		var item_config = {
			name: meta.name + '-value',
			id: meta.name + '-value'
		};
		
		var t = new Ext.form.TextField(item_config);
		
		return t;
	};
	
	
	
	return {
		
		createComponent : function(meta) {
			return componentDispatch(meta);
		}
		
	}
}();