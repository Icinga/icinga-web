Ext.ns('Icinga.Cronks.Tackle.Command');

Icinga.Cronks.Tackle.Command.View = Ext.extend(Ext.DataView, {
	
	tpl : new Ext.XTemplate(
	    '<tpl for=".">',
	    '<div>',
	    '<div>{definition}</div>',
	    '</div>',
	    '</tpl>'
	),
	
	constructor : function(config) {
		Icinga.Cronks.Tackle.Command.View.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Cronks.Tackle.Command.View.superclass.initComponent.call(this);
	}
});