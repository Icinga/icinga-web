Ext.ns('Icinga.Cronks.Tackle');

Icinga.Cronks.Tackle.ObjectTree = Ext.extend(Ext.Panel, {
	title : 'Object tree',
	
	constructor : function(config) {
		
		config = Ext.apply(config || {}, {
			layout : 'fit'
		});
		
		Icinga.Cronks.Tackle.ObjectTree.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Cronks.Tackle.ObjectTree.superclass.initComponent.call(this);
		
		this.rootNode = new Ext.tree.AsyncTreeNode({
            text : _('Hosts')
        });
        
        this.loader = new Ext.ux.tree.TreeGridLoader({
            
        });
        
        this.treeGrid = new Ext.ux.tree.TreeGrid({
        	rootVisible : true,
        	loader : this.loader,
        	root : this.rootNode,
        	
        	columns : [{
	            header : _('Name'),
	            width : 300 ,
	            dataIndex : 'object_name'
            }, {
            	header : _('State'),
            	width : 200,
            	dataIndex : 'object_state'
            }]
        });
        
        this.add(this.treeGrid);
	}
	
});

Ext.reg('cronks-tackle-objecttree', Icinga.Cronks.Tackle.ObjectTree);