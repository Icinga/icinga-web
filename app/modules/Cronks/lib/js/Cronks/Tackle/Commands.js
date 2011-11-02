Ext.ns('Icinga.Cronks.Tackle.Information');

Icinga.Cronks.Tackle.Information.Commands = Ext.extend(Ext.Panel, {
	
	title : _('Commands'),
	iconCls : 'icinga-icon-bricks',
	
	constructor : function(config) {
		Icinga.Cronks.Tackle.Information.Commands.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Cronks.Tackle.Information.Commands.superclass.initComponent.call(this);
	}
});

Ext.reg('cronks-tackle-information-commands', Icinga.Cronks.Tackle.Information.Commands);