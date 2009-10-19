// Namespace
Ext.ns('AppKit.Ext');

// Our class
AppKit.Ext.FilterHandler = function() {
	AppKit.Ext.FilterHandler.superclass.constructor.call(this);
};

// Extending
AppKit.Ext.FilterHandler = Ext.extend(Ext.util.Observable, {
	
	oFilterOp : {
		'appkit.ext.filter.text': 'text',
		'appkit.ext.filter.number': 'number'
	},
	
	oOpList : {
		text: [
			[60, 'contain'],
			[61, 'does not contain'],
			[50, 'is'],
			[51, 'is not']
		]	,
		
		number: [
			[50, 'is'],
			[51, 'is not'],
			[70, 'less than'],
			[71, 'greater than']
		]
	},
	
	oOpDefault : {
		number: 50,
		text: 60
	},
	
	meta : {},
	config : {}, 
	
	cList : {},
	
	constructor : function(config) {
		
		Ext.apply(this.config, config);
		
		if (this.config.meta) {
			this.setMeta(this.config.meta);
		}
		
		this.listener = {};
		
		this.addEvents({
			'aftercompremove' : true,
			'compremove' : true,
			'aftercompadd' : true,
			'compcreate' : true,
			'aftercompcreate' : true,
			'metaload' : true
		});
		
		AppKit.Ext.FilterHandler.superclass.constructor.call();
	},
	
	setMeta : function (meta) {
		if (this.fireEvent('metaload', this, meta) !== false) {
			this.meta = meta;
		}
		
		return true;
	},
	
	getRemoveComponent : function(meta) {
		var button = new Ext.Button({
			xtype: 'button',
			iconCls: 'silk-cross',
			handler: function(b, e) {
				this.removeComponent(meta);
			},
			
			scope: this
		});
		
		return button;
	},
	
	removeAllComponents : function() {
		Ext.iterate(this.cList, function(k, v) {
			this.removeComponent(v);
		}, this);
	},
	
	removeComponent : function(meta) {
		
			var cid = 'fco' + meta.id;
		
			// Retrieve the comp_id
			var p = Ext.getCmp('fco' + meta.id);
			
			// Removing the panel construct
			if (this.fireEvent('compremove', this, p, meta) !== false) {
				
				var form = p.findParentByType('form');
				
				if (form) {
					form.remove(p, true).destroy();
					delete this.cList[cid];
				}
			}
			
			this.fireEvent('aftercompremove', this, p, meta)

			return true;
	},
	
	getLabelComponent : function(meta) {
		return new Ext.Panel({
			html: meta['label'],
			border: false
		});
	},
	
	getOperatorComponent : function(meta) {
		var  type = null;
		
		// Disable the operator
		if (meta.no_operator && meta.no_operator == true) {
			return new Ext.Panel({ border: false });
		} 
		
		if (meta.operator_type) {
			type = meta.operator_type;
		}
		
		if (!type) {
			type = this.oFilterOp[meta.subtype];
		}
		
		// this is our combo field
		var oCombo = new Ext.form.ComboBox({
			
			store : new Ext.data.ArrayStore({
				idIndex : 0,
				fields : ['id', 'label'],
				data : this.oOpList[type] || []
			}),
			
			mode : 'local',
			
			typeAhead : true,
			triggerAction : 'all',
			forceSelection : true,
					
			fieldLabel : "Operator",
			
			valueField : 'id',
			displayField : 'label',
			
			hiddenName : meta.id + '-operator',
			hiddenId : meta.id + '-operator',
			
			'name' : '___LABEL' + meta.id + '-operator',
			id : '___LABEL' + meta.id + '-operator',
			
			width : 110
		});
		
		// Select tester
		// oCombo.on('select', function(c, record, index) {
		// 
		// }, this);
		
		// Set the default value after rendering
		oCombo.on('render', function(c) {
			c.setValue(this.oOpDefault[type]);
		}, this);
		
		// Pack all together in a container
		// var p = new Ext.Panel({border: false});
		// p.add(oCombo);
		// return p;
		
		return oCombo;
	},
	
	getFilterComponent : function(meta) {
		
		var item_config = {
			'name' : meta.name + '-value',
			id : meta.name + '-value'
		};
		
		return new Ext.form.TextField(item_config);
	},
	
	createComponent : function(meta) {
			return this.componentDispatch(meta);
	},
	
	componentDispatch : function(meta) {
		
		var cid = 'fco' + meta.id;
		
		var panel = new Ext.Panel({
			id: cid,
			border: false,
			style: 'padding: 2px;',
			layout: 'column',
			
			defaults: {
				border: false,
				style: 'padding: 2px;'				
			}
		});
		
		// Before adding stage
		if (this.fireEvent('compcreate', this, panel, meta) !== false) {
			
			this.cList[cid] = meta;
			
			// Adding the label
			panel.add([
				{items: this.getLabelComponent(meta), columnWidth: .19 },
				{items: this.getOperatorComponent(meta), columnWidth: .3},
				{items: this.getFilterComponent(meta), columnWidth: .4 },
				{items: this.getRemoveComponent(meta), columnWidth: .1}
			]);
			
		}
		
		// All panels there
		this.fireEvent('aftercompcreate', this, panel, meta)
		
		return panel;
		
	}
	
});

// Adding the blank events
Ext.apply(AppKit.Ext.FilterHandler, {
	afterCompRemove : function(fh, p, meta) {
		return true;
	},
	
	compRemove : function(fh, p, meta) {
		return true;
	},
	
	afterCompCreate : function(fh, panel, meta) {
		return true;
	},
	
	compCreate : function(fh, panel, meta) {
		return true;
	},
	
	metaLoad : function(fh, meta) {
		return true;
	}
});