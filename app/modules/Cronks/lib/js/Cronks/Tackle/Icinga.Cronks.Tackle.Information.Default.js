Ext.ns('Icinga.Cronks.Tackle.Information');

Icinga.Cronks.Tackle.Information.Default = Ext.extend(Ext.Panel, {
	
	title : _('Default'),
	iconCls : 'icinga-icon-information',
	
    constructor : function(config) {
        Icinga.Cronks.Tackle.Information.Default.superclass.constructor.call(this, config);
    },
    
    initComponent : function() {
        Icinga.Cronks.Tackle.Information.Default.superclass.initComponent.call(this);
    }
});

Ext.reg('cronks-tackle-information-default', Icinga.Cronks.Tackle.Information.Default);