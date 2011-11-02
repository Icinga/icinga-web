Ext.ns('Icinga.Api.Command.Type');

Icinga.Api.Command.Type.Generic = Ext.extend(Icinga.Api.Command.Type.Abstract, {
	
	buildForm : function(o) {
		Icinga.Api.Command.Type.Generic.superclass.buildForm.call(this, o);
		
		Ext.iterate(o.parameters, function(key, value) {
			if (this.isSourceField(key) === false) {
				var field = this.getFieldByName(key, value);
                this.add(field);
			}
		}, this);
	},
	
	getFieldByName : function(fieldName, fieldParams) {
		
		var oDef = this.getExtFieldDefinition(fieldParams);
		
		this.changeFieldAttributes(oDef, fieldName, fieldParams)
		
		var field = Ext.ComponentMgr.create(oDef, fieldName);
		
		return field;
	},
	
	changeFieldAttributes : function(oDef, fieldName, fieldParams) {
		switch (fieldName) {
			case 'COMMAND_AUTHOR':
			     oDef.value = AppKit.getPreferences().author_name;
			break;
		}
		
		switch (fieldParams.type) {
			case 'ro':
			    oDef.readOnly = true;
			break;
		}
		
		return oDef;
	}
});