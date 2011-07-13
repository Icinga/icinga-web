Ext.ns('Icinga.Reporting.util');

Icinga.Reporting.util.OnTheFly = Ext.extend(Ext.Container, {
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
			treeloader_url: this.treeloader_url,
			treeloader_filter: 'reports'
		});
		
		this.paramPanel = new Icinga.Reporting.util.RunReportPanel({
			region : 'center',
			parampanel_url: this.parampanel_url,
			creator_url : this.creator_url,
			parentCmp : this
		});
		
		this.add([
			this.resourceTree,
			this.paramPanel
		]);
		
		var resizeFn = function(c) {
			var p = this.findParentByType('tabpanel');
			if (p) {
				this.setHeight(p.getInnerHeight());
			}
		}
		
		this.on('afterrender', resizeFn, this, { single : true });
		this.on('resize', resizeFn, this, { single : true });
		
		Ext.EventManager.onWindowResize(resizeFn, this);
		
		this.on('afterrender', this.processApplication, this);
	},
	
	processApplication : function() {
		this.resourceTree.getTreePanel().on('click', function(node, e) {
			this.paramPanel.initUi(node.attributes);
		}, this);
	}
	
});