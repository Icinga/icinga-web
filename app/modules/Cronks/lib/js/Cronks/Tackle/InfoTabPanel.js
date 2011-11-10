Ext.ns('Icinga.Cronks.Tackle');

Icinga.Cronks.Tackle.InfoTabPanel = Ext.extend(Ext.TabPanel, {
	activeTab : 2,
	border : false,
	
	constructor : function(config) {
		Icinga.Cronks.Tackle.InfoTabPanel.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Cronks.Tackle.InfoTabPanel.superclass.initComponent.call(this);
	}
	
});

Ext.reg('cronks-tackle-infotabs', Icinga.Cronks.Tackle.InfoTabPanel);