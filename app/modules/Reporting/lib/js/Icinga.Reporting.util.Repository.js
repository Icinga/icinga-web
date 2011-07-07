Ext.ns('Icinga.Reporting.util');

Icinga.Reporting.util.Repository = Ext.extend(Ext.Container, {
	layout : 'border',
	height : 800, // Don't worry, we resize later
	border : false,
	
	constructor : function(config) {
		Icinga.Reporting.util.OnTheFly.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		
		Icinga.Reporting.util.OnTheFly.superclass.initComponent.call(this);
		
		this.resourceTree = new Icinga.Reporting.util.ResourceTree({
			region : 'west',
			width : 350,
			split: true,
			collapsible: true,
			treeloader_url: this.treeloader_url
		});
		
		this.targetPanel = new Ext.Panel({
			region : 'center',
			parentCmp : this,
			title : 'test',
			html: 'test'
		});
		
		this.add([this.resourceTree, this.targetPanel]);
		
		var resizeFn = function(c) {
			var p = this.findParentByType('tabpanel');
			if (p) {
				this.setHeight(p.getInnerHeight()-28);
			}
		}
		
		this.on('afterrender', resizeFn, this, { single : true });
		this.on('resize', resizeFn, this, { single : true });
		
		Ext.EventManager.onWindowResize(resizeFn, this);
		
		this.doLayout();
	}
});