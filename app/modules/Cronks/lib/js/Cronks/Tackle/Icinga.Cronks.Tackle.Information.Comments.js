Ext.ns('Icinga.Cronks.Tackle.Information');

Icinga.Cronks.Tackle.Information.Comments = Ext.extend(Ext.Panel, {
	
	title : _('Comments'),
	iconCls : 'icinga-icon-comment',
	
    constructor : function(config) {
        Icinga.Cronks.Tackle.Information.Comments.superclass.constructor.call(this, config);
    },
    
    initComponent : function() {
        Icinga.Cronks.Tackle.Information.Comments.superclass.initComponent.call(this);
    }
});

Ext.reg('cronks-tackle-information-comments', Icinga.Cronks.Tackle.Information.Comments);