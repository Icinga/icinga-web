Ext.ns('Icinga.Reporting.util');

Icinga.Reporting.util.ResourceTree = Ext.extend(Ext.Panel, {
	
	layout : 'fit',
	minWidth: 200,
	maxWidth: 300,
	
	
	constructor : function(config) {
		Icinga.Reporting.util.ResourceTree.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Reporting.util.ResourceTree.superclass.initComponent.call(this);
		
		this.rootNode = new Ext.tree.AsyncTreeNode({
			text : _('Repository'),
			id : 'root'
		});
		
		
		this.treeLoader = this.createTreeLoader();
		
		this.treePanel = new Ext.tree.TreePanel({
			useArrows : true,
		    autoScroll : true,
		    animate : true,
		    enableDD : true,
		    containerScroll : true,
		    border : false,
		    loader: this.treeLoader,
		    root: this.rootNode
		});
		
		this.treePanel.on('afterrender', function(c) {
			this.rootNode.expand();
		}, this, { single : true })
		
		this.add(this.treePanel);
	},
	
	getRootNode : function() {
		return this.rootNode;
	},
	
	getTreeLoader : function() {
		return this.treeLoader;
	},
	
	getTreePanel : function() {
		return this.treePanel;
	},
	
	createTreeLoader : function() {
		var tl = new Ext.tree.TreeLoader({
			dataUrl : this.treeloader_url
		});
		
		var filter = "";
		
		if (!Ext.isEmpty(this.treeloader_filter)) {
			filter = this.treeloader_filter;
		}
		
		tl.on('beforeload', function(treeLoader, node) {
			this.baseParams.filter = filter;
		});
		
		return tl;
	}
});