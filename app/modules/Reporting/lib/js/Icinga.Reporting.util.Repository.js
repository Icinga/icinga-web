Ext.ns('Icinga.Reporting.util');

Icinga.Reporting.util.Repository = Ext.extend(Icinga.Reporting.abstract.ResizedContainer, {
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
		
		this.contentResource = new Icinga.Reporting.util.ContentResourcePanel({
			region : 'center',
			resource_url : this.resource_url,
			parentCmp : this
		});
		
		this.resourceTree.getTreePanel().on('click', this.contentResource.processNodeClick, this.contentResource);
		
		this.add([this.resourceTree, this.contentResource]);
		
		this.doLayout();
	}
});