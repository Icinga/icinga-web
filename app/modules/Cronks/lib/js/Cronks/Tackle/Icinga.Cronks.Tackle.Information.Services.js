Ext.ns('Icinga.Cronks.Tackle.Information');

Icinga.Cronks.Tackle.Information.Services = Ext.extend(Ext.Panel, {
	
	title : _('Services'),
	iconCls : 'icinga-icon-service',
	
    constructor : function(config) {
        Icinga.Cronks.Tackle.Information.Services.superclass.constructor.call(this, config);
    },
    
    initComponent : function() {
        Icinga.Cronks.Tackle.Information.Services.superclass.initComponent.call(this);
    }
});

Ext.reg('cronks-tackle-information-services', Icinga.Cronks.Tackle.Information.Services);