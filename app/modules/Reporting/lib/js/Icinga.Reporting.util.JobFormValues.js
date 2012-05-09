// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}

Ext.ns('Icinga.Reporting.util', 'Icinga.Reporting.form');

Icinga.Reporting.form.converter = {};

Ext.apply(Icinga.Reporting.form.converter, {
	'default.arrayValues' : {
		encode : function(value, fieldName) {
			return String(value).split(',');
		},
		
		decode : function(value, fieldName) {
			return value;
		}
	},
	
	'default.simpleArray' : {
		encode : function(value, fieldName) {
			return String(value).split(',');
		},
		
		decode : function(value, fieldName) {
			return value
		}
	},
	
	'mailNotification.toAddresses' : 'default.arrayValues',
	'calendarTrigger.weekDays' : 'default.simpleArray',
	'calendarTrigger.months' : 'default.simpleArray',
	
	'outputFormats' : {
		decode : function(value, fieldName) {
			out = {};
			for (var i in value) {
				out[fieldName + '.' + value[i]] = true
			}
			return out;
		}
	},
	
	'mailNotification.resultSendType' : {
		encode : function(value, fieldName) {
			if (value == 'true') {
				return 'SEND_ATTACHMENT';
			} else {
				return 'SEND';
			}
		},
		
		decode : function(value, fieldName) {
			if (value == 'SEND_ATTACHMENT') {
				return true;
			}
			
			return false;
		}
	},
	
	'parameters' : {
		encode : function(value, fieldName) {
			
		},
		
		decode : function(value, fieldName) {
			var out = {};
			Ext.iterate(value, function(val, key) {
				out[val.name] = val.value;
			});
			
			/**
			 * Return this structure to add another
			 * formvalues from this converter
			 */
			return {
				flatData : true,
				data : out
			};
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
	
	triggerFields : {
		simpleTrigger : false,
		calendarTrigger : false
	},
	
	constructor : function(config) {
		Icinga.Reporting.util.JobFormValues.superclass.constructor.call(this);
		
		this.initialConfig = config;
		
		Ext.apply(this, config);
	},
	
	getForm : function() {
		return this.form;
	},
	
	createJsonStructure : function() {
		var values = this.form.getValues();
		
		var o = {};
		
		Ext.iterate(values, function(key, val) {
			
			if (Icinga.Reporting.form.FieldConverterUtil.hasConverter(key)) {
				val = Icinga.Reporting.form.FieldConverterUtil.getConverter(key).encode(val, key);
			}
			
			var subitems = String(key).split('.');
			var subkey = null;
			var oo = o;
			while ((subkey = subitems.shift())) {
				oo = (oo[subkey] = oo[subkey] || (subitems.length ? {} : val));
			}
		}, this);
		
		// AppKit.log(o);
		
		return o;
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
					var newVal  = converter.decode(val, newKey);
					
					if (Ext.isObject(newVal) && !Ext.isEmpty(newVal.flatData) && !Ext.isEmpty(newVal.data)) {
						/*
						 * Flattening again the structue for setting sub form values
						 * from object source
						 */
						this.flattenObject(newVal.data, out, newKey + '.');
					} else {
						out[newKey] = newVal;
					}
					
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
		
		var triggerPrefix = null;
		var trigger = null;
		
		// Decide which calendar trigger you want to use
		if (this.triggerFields.simpleTrigger == false && this.triggerFields.calendaTrigger == false) {
			this.form.findField('trigger').setValueForItem('recurrence-none');
			trigger = null; 
		} else if (this.triggerFields.simpleTrigger == true) {
			this.form.findField('trigger').setValueForItem('recurrence-simple');
			triggerPrefix = 'simpleTrigger';
			trigger = 'simple';
			this.triggerFields.simpleTrigger = false;
		} else if (this.triggerFields.calendarTrigger == true) {
			this.form.findField('trigger').setValueForItem('recurrence-calendar');
			triggerPrefix = 'calendarTrigger';
			trigger = 'calendar';
			this.triggerFields.calendarTrigger = false;
		}
		
		if (triggerPrefix) {
			if (!Ext.isEmpty(data[triggerPrefix + ".startDate"])) {
				this.form.findField(triggerPrefix + ".startType").setValue(2);
			} else {
				this.form.findField(triggerPrefix + ".startType").setValue(1);
			}
		}
		
		switch(trigger) {
			case 'simple':
				if (!Ext.isEmpty(data['simpleTrigger.occurrenceCount']) && data['simpleTrigger.occurrenceCount'] > -1) {
					this.form.findField('simpleTrigger.recurrenceType').setValue(2);
				} else if (!Ext.isEmpty(data['simpleTrigger.endDate'])) {
					this.form.findField('simpleTrigger.recurrenceType').setValue(3);
				} else {
					this.form.findField('simpleTrigger.recurrenceType').setValue(1);
				}
			break;
			
			case 'calendar':
				if (!Ext.isEmpty(data['calendarTrigger.months'])) {
					this.form.findField("calendarTrigger.monthsType").setValue(2);
				} else {
					this.form.findField("calendarTrigger.monthsType").setValue(1);
				}
			break;
		}
	},
	
	applySingleFieldValue : function(fieldName, value) {
		var fieldElement = this.form.findField(fieldName);
		
		if (fieldName == 'simpleTrigger' && value == null) {
			this.triggerFields.calendarTrigger = true;
		}
		
		if (fieldName == 'calendarTrigger' && value == null) {
			this.triggerFields.simpleTrigger = true;
		}
		
		if (!fieldElement) {
//			AppKit.log('Field not found', fieldName, value);
			return true;
		}
		
//		AppKit.log('Set value on field', fieldName, value);
		
		fieldElement.setValue(value);
	}
});