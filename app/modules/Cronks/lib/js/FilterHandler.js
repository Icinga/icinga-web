
/**
 * IcingaApiComboBox
 * Extended to let meta data from xml template
 * configure the store to fetch data from the IcingaAPI
 */
Cronk.IcingaApiComboBox = Ext.extend(Ext.form.ComboBox, {

	def_webpath : '/web/api/json',
	def_sortorder : 'asc',

	constructor : function(cfg, meta) {

		var kf = meta.api_keyfield;		// ValueField
		var vf = meta.api_valuefield;	// KeyField

		var fields = [];
		var cols = [];

		var cfields = {};
		cfields[kf] = true;
		cfields[vf] = true;

		if (meta.api_id) {
			cfields[meta.api_id] = true;
		}

		// If we need more fields to work with
		if (meta.api_additional) {
			var i = meta.api_additional.split(',');
			for (var k in i) {
				if (Ext.isString(i[k])) {
					cfields[i[k]] = true;
				}
			}
		}

		for (var f in cfields) {
			cols.push(f);
			fields.push({
				name: f
			});
		}

		var apiStore = new Ext.data.JsonStore({
			autoDestroy : true,
			url : AppKit.c.path + this.def_webpath,

			baseParams : {
				target : meta.api_target,
				order_col: (meta.api_order_col || meta.api_keyfield),
				order_dir: (meta.api_order_dir || this.def_sortorder),
				columns: cols
			},

			idProperty : (meta.api_id || meta.api_keyfield),

			fields : fields
		});

		cfg = Ext.apply(cfg || {}, {
			store : apiStore,
			displayField: vf,
			valueField : vf,
			keyField : kf
		});

		// To display complex multi column layouts
		if (meta.api_exttpl) {
			cfg.tpl = '<tpl for="."><div class="x-combo-list-item">' + meta.api_exttpl + '</div></tpl>';
		}

		// Notify the parent class
		Cronk.IcingaApiComboBox.superclass.constructor.call(this, cfg);
	}
});

// Our class
Cronk.FilterHandler = function() {
	Cronk.FilterHandler.superclass.constructor.call(this);
};

// Extending
Cronk.FilterHandler = Ext.extend(Ext.util.Observable, {
	
	oFilterOp : {
		'appkit.ext.filter.text': 'text',
		'appkit.ext.filter.number': 'number',
		'appkit.ext.filter.servicestatus': 'number',
		'appkit.ext.filter.hoststatus': 'number'
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
		
		Cronk.FilterHandler.superclass.constructor.call();
	},
	
	setMeta : function (meta) {
		if (tis.fireEvent('metaload', this, meta) !== false) {
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
			return new Ext.Panel({border: false});
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
	
	getComboComponent : function(data, meta) {
		var def = {
			store: new Ext.data.ArrayStore({
				idIndex: 0,
				fields: ['fId', 'fStatus', 'fLabel'],
				data: data
			}),
			
			'name': '__status_name_' + meta.name,
			'id': '__status_name_' + meta.name,
			// 'name': meta.name + '-value',
			
			mode: 'local',
			typeAhead: true,
			triggerAction: 'all',
			forceSelection: true,
			
			
			fieldLabel: 'Status',
			
			valueField: 'fStatus',
			displayField: 'fLabel',
			
			width: 150,
			
			hiddenName: meta.name + '-value',
			hiddenId: meta.name + '-value'
		};
		
		return new Ext.form.ComboBox(def);
		
	},

	getApiCombo : function(meta) {
		return new Cronk.IcingaApiComboBox({
			typeAhead: true,
			triggerAction: 'all',
			forceSelection: false,
			'name': meta.name + '-field',
			'id': meta.name + '-field',
			hiddenName: meta.name + '-value',
			hiddenId: meta.name + '-value'
		}, meta);
	},
	
	getFilterComponent : function(meta) {
		var oDef = {
			'name' : meta.name + '-value',
			id : meta.name + '-value'
		};
		
		switch (meta.subtype) {
			
			case 'appkit.ext.filter.servicestatus':
				return this.getComboComponent([
					['1', '0', 'OK'],
					['2', '1', 'Warning'],
					['3', '2', 'Critical'],
					['4', '3', 'Unknown']
				], meta);
			break;
			
			case 'appkit.ext.filter.hoststatus':
				return this.getComboComponent([
					['1', '0', 'UP'],
					['2', '1', 'Down'],
					['3', '2', 'Unreachable']
				], meta);
			break;

			case 'appkit.ext.filter.api':
				return this.getApiCombo(meta);
			break;

			default:
				return new Ext.form.TextField(oDef);	
			break;
			
		}
		
		
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
				{items: this.getLabelComponent(meta), columnWidth: .19},
				{items: this.getOperatorComponent(meta), columnWidth: .3},
				{items: this.getFilterComponent(meta), columnWidth: .4},
				{items: this.getRemoveComponent(meta), columnWidth: .1}
			]);
			
		}
		
		// All panels there
		this.fireEvent('aftercompcreate', this, panel, meta)
		
		return panel;
		
	}
	
});

// Adding the blank events
Ext.apply(Cronk.FilterHandler, {
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
