Ext.ns('Icinga.Reporting.util');

Icinga.Reporting.util.ResourceTree = Ext.extend(Icinga.Reporting.abstract.ApplicationWindow, {
	
	layout : 'fit',
	minWidth: 200,
	maxWidth: 300,
	useArrows : true,
	autoScroll : false,	
	rootName : _('Repository'),
	title : _('Resources'),
	
	mask_text : _('Loading resource tree . . .'),
	
	constructor : function(config) {
		
		config = Ext.apply(config || {}, {
			'tbar' : [{
				text : _('Reload'),
				iconCls : 'icinga-icon-arrow-refresh',
				handler : this.reloadTree,
				scope : this
			}]
		});
		
		Icinga.Reporting.util.ResourceTree.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Reporting.util.ResourceTree.superclass.initComponent.call(this);
		
		this.rootNode = new Ext.tree.AsyncTreeNode({
			text : this.rootName,
			iconCls : 'icinga-icon-bricks',
			id : 'root'
		});
		
		
		this.treeLoader = this.createTreeLoader();
		
		this.treeLoader.on('beforeload', function(loader, node, cb) {
			this.showMask();
		}, this, { single : true });
		
		this.treeLoader.on('load', function(loader, node, cb) {
			this.hideMask();
		}, this);
		
		this.treePanel = new Ext.tree.TreePanel({
			useArrows : true,
		    autoScroll : true,
		    animate : true,
		    enableDD : false,
		    containerScroll : true,
		    border : false,
		    loader: this.treeLoader,
		    root: this.rootNode
		});
		
		this.treePanel.on('afterrender', function(c) {
			this.rootNode.expand();
		}, this, { single : true });
		
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
			dataUrl : this.treeloader_url,
			
			qtipTemplate : new Ext.XTemplate(
				'<strong>{name}</strong><br />'
				+ '<span>Type: {type}</span><br />'
				+ '<span>URI: {uri:ellipsis(60)}</span>', 
			{
				compiled : true
			}),
			
			createNode : function(attr) {
				attr.qtip = this.qtipTemplate.applyTemplate(attr);
				
				return Ext.tree.TreeLoader.prototype.createNode.call(this, attr);
			}
		});
		
		var filter = "";
		
		if (!Ext.isEmpty(this.treeloader_filter)) {
			filter = this.treeloader_filter;
		}
		
		tl.on('beforeload', function(treeLoader, node) {
			this.baseParams.filter = filter;
		});
		
		return tl;
	},
	
	reloadTree : function() {
		this.showMask();
		this.rootNode.collapse(true);
		this.rootNode.removeAll(true);
		this.treeLoader.load(this.rootNode);
		this.rootNode.expand();
	}
});