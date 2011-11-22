Ext.ns('Icinga.Cronks.StatusMap');

Icinga.Cronks.StatusMap.Cronk = Ext.extend(Ext.Panel, {
	
	url : null,
	rgraph : null,
	refreshTime : 300,
	
	constructor : function(config) {
		Icinga.Cronks.StatusMap.Cronk.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {		
		this.tbar = [{
            xtype: 'button',
            iconCls: 'icinga-icon-application-edit',
            text: _('Settings'),
            menu: [{
                text: _('Autorefresh'),
                xtype: 'menucheckitem',
                checkHandler: function(item, state) {
                	var tr = AppKit.getTr();
                	if (state === true) {
                		tr.start(this.refreshTask);
                	} else if (state === false) {
                		tr.stop(this.refreshTask);
                	}
                },
                scope:this
            }]
        }];
		
		Icinga.Cronks.StatusMap.Cronk.superclass.initComponent.call(this);
		
		this.rgraph = new Icinga.Cronks.StatusMap.RGraph({
			url : this.url,
			parentId : this.getId()
		});
		
		this.refreshTask = {
            run :  this.rgraph.reloadTree.createDelegate(this.rgraph),
            interval : (this.refreshTime * 1000)
        };
	},
	
	getRGraph : function() {
		return this.rgraph;
	}
});