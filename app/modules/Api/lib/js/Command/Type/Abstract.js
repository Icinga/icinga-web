Ext.ns('Icinga.Api.Command.Type');

Icinga.Api.Command.Type.Abstract = Ext.extend(Ext.form.FormPanel, {
    constructor : function(config) {
    	Icinga.Api.Command.Type.Abstract.superclass.constructor.call(this, config);
    },
    
    initComponent : function() {
    	Icinga.Api.Command.Type.Abstract.superclass.constructor.call(this);
    }
});