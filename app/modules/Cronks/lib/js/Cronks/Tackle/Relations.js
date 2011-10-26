Ext.ns('Icinga.Cronks.Tackle.Information');

Icinga.Cronks.Tackle.Information.Relations = Ext.extend(Ext.Panel, {
	
	title : _('Relations'),
	iconCls : 'icinga-icon-databases-relation',
	
    constructor : function(config) {
        Icinga.Cronks.Tackle.Information.Relations.superclass.constructor.call(this, config);
    },
    
    initComponent : function() {
        Icinga.Cronks.Tackle.Information.Relations.superclass.initComponent.call(this);
    }
});

Ext.reg('cronks-tackle-information-relations', Icinga.Cronks.Tackle.Information.Relations);