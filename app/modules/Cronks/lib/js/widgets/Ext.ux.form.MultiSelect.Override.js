/**
 * Some methods for the MultiSelect box to fix missing features
 */
Ext.override(Ext.ux.form.MultiSelect, {
	/**
	 * To set values by displayField
	 */
	setValueByDisplayValues : function(value) {
		var old = this.valueField;
		this.valueField = this.displayField;
		this.setValue(value);
		this.valueField = old;
		this.hiddenField.dom.value = this.getValue();
		this.validate();
	}	
});