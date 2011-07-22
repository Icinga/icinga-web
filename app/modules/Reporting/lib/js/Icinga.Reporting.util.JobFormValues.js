Ext.ns('Icinga.Reporting.util', 'Icinga.Reporting.form');

Icinga.Reporting.form.converter = {};

Ext.apply(Icinga.Reporting.form.converter, {
	'default.arrayValues' : {
		encode : function(value, fieldName) {
			
		},
		
		decode : function(value, fieldName) {
			return value.join(', ');
		}
	},
	
	'mailNotification.toAddresses' : 'default.arrayValues',
	'calendarTrigger.weekDays' : 'default.arrayValues',
	'calendarTrigger.months' : 'default.arrayValues',
	
	'outputFormats' : {
		decode : function(value, fieldName) {
			out = {};
			for (var i in value) {
				out[fieldName + '.' + value[i]] = true
			}
			return out;
		}
}
});

Icinga.Reporting.form.FieldConverterUtil = (function() {
	var pub = {};
	
	Ext.apply(pub, {
		hasConverter : function(fieldName) {
			return (fieldName in Icinga.Reporting.form.converter);
		},
		
		getConverter :  function(name) {
			if (this.hasConverter(name)) {
				var c = Icinga.Reporting.form.converter[name];
				if (Ext.isString(c)) {
					return Icinga.Reporting.form.converter[c]; 
				}
				else if (Ext.isObject(c)) {
					return c;
				}
				
			}
		}
	});
	
	return pub;
})();

Icinga.Reporting.util.JobFormValues = Ext.extend(Object, {
	
	data : {},
	
	form : null,
	
	constructor : function(config) {
		Icinga.Reporting.util.JobFormValues.superclass.constructor.call(this);
		
		this.initialConfig = config;
		
		Ext.apply(this, config);
	},
	
	getForm : function() {
		return this.form;
	},
	
	applyFormValues : function(data) {
		data = data || this.data;
		
		if (Ext.isEmpty(this.flat_data)) {
			this.flat_data = this.flattenObject(data, {}, '');
		}
		
		this.applyFlatData(this.flat_data);
	},
	
	flattenObject : function(object, out, prefix) {
		Ext.iterate(object, function(key, val) {
			if (key) {
				var newKey = prefix+key;
				
				if (Icinga.Reporting.form.FieldConverterUtil.hasConverter(newKey)) {
					var converter = Icinga.Reporting.form.FieldConverterUtil.getConverter(newKey);
					out[newKey] = converter.decode(val, newKey);
				} else if (Ext.isArray(val) || Ext.isObject(val)) {
					this.flattenObject(val, out, newKey + '.')
				} else {
					out[newKey] = val;
				}
			}
		}, this);
		return out;
	},
	
	applyFlatData : function(data) {
		Ext.iterate(data, this.applySingleFieldValue, this);
	},
	
	applySingleFieldValue : function(fieldName, value) {
		var fieldElement = this.form.findField(fieldName);
		
		if (!fieldElement) {
			AppKit.log('Field not found', fieldName);
			return true;
		}
		
		AppKit.log('Set value on field', fieldName, value);
		fieldElement.setValue(value);
	}
});