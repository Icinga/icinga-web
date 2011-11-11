Ext.ns('Icinga.Cronks.Tackle.Command');

Icinga.Cronks.Tackle.Command.View = Ext.extend(Ext.DataView, {
	
	tpl : new Ext.XTemplate(
	    '<tpl for=".">',
	    '<div class="tackle-command-view-item">',
	    '<div class="tackle-command-view-item-inline icon-16 {iconCls}"></div>',
	    '<div class="tackle-command-view-item-inline">{label} ({definition})</div>',
	    '</div>',
	    '</tpl>'
	),
	
	itemSelector : 'div.tackle-command-view-item',
	overClass : 'tackle-command-view-item-over',
	autoScroll : true,
	
	constructor : function(config) {
		Icinga.Cronks.Tackle.Command.View.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Cronks.Tackle.Command.View.superclass.initComponent.call(this);
	}
});