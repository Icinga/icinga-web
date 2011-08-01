Ext.ns('Icinga.Reporting.util');

 Icinga.Reporting.util.InputControlBuilder = Ext.extend(Object, {
	
	removeAll : false,
	
	constructor : function(config) {
		Icinga.Reporting.util.InputControlBuilder.superclass.constructor.call(this);
		
		this.initialConfig = config;
		Ext.apply(this, config);
		
		this.items = new Ext.util.MixedCollection();
	},
	
	setTarget : function(target) {
		this.target = target;
	},
	
	setControlStruct : function(controlStruct) {
		this.controlStruct = controlStruct;
	},
	
	buildFormItems : function() {
		this.items.clear();
		
		var namePrefix = this.namePrefix || '';
		
		Ext.iterate(this.controlStruct, function(k,v) {
			var inputConfig = {};
			
			Ext.apply(v.jsControl, {
				hidden : v.PROP_INPUTCONTROL_IS_VISIBLE=="false" ? true : false,
				readonly : v.PROP_INPUTCONTROL_IS_READONLY=="true" ? true : false,
				name : namePrefix + v.name,
				width: 250,
				fieldLabel : v['label'],
				allowBlank : false
			});
			
			Ext.applyIf(v.jsControl, Icinga.Reporting.DEFAULT_JSCONTROL);
			
			inputConfig = v.jsControl;
			
			if (!Ext.isEmpty(inputConfig.className)) {
				var inputClass = eval('window.' + inputConfig.className);
				var inputControl = new inputClass(inputConfig);
				this.items.add(inputConfig.name, inputControl);
			}
			
		}, this);
		
		return this.items;
	},
	
	applyToTarget : function(target) {
		target = target || this.target;
		
		if (this.items.getCount() < 1) {
			this.buildFormItems();
		}
		
		if (this.removeAll == true) {
			target.removeAll(true);
		}
		
		this.items.each(function(item, index, len) {
			target.add(item);
		}, this);
		
		target.doLayout();
	}
	
});