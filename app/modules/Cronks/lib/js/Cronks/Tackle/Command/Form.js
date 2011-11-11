Ext.ns('Icinga.Cronks.Tackle.Command');

Icinga.Cronks.Tackle.Command.Form = Ext.extend(Ext.Panel, {
	title : _('Commands'),
	
	constructor : function(config) {
		Icinga.Cronks.Tackle.Command.Form.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Cronks.Tackle.Command.Form.superclass.initComponent.call(this);
	},
	
	rebuildFormForCommand : function(commandName) {
		var title = String.format(_('Command: {0}'), commandName);
		this.setTitle(title);
	}
});