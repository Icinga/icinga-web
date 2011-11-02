Ext.ns('Icinga.Cronks.Tackle.Information');

Icinga.Cronks.Tackle.Information.Comments = Ext.extend(Ext.Panel, {
	
	title : _('Comments'),
	iconCls : 'icinga-icon-comment',
	
	dataLoaded : false,
	objectId : null,
	border : false,
	
    constructor : function(config) {
        Icinga.Cronks.Tackle.Information.Comments.superclass.constructor.call(this, config);
    },
    
    initComponent : function() {
    	
    	this.tbar = [{
    		text : _('Refresh'),
    		iconCls : 'icinga-action-refresh',
    		scope : this,
    		handler : function() {
    			this.store.reload();
    		}
    	}]
    	
        Icinga.Cronks.Tackle.Information.Comments.superclass.initComponent.call(this);
        
        this.setLayout(new Ext.layout.FitLayout(Ext.apply({}, this.layoutConfig)));
        
        this.store = new Icinga.Api.RESTStore({
        	autoDestroy : true,
        	idIndex : 0,
        	target : 'comment',
        	columns : [
        	   'COMMENT_ID',
        	   'COMMENT_DATA',
        	   'COMMENT_AUTHOR_NAME',
        	   'COMMENT_TIME',
        	   'COMMENT_OBJECT_ID',
        	   'COMMENT_TYPE'
        	]
        });
        
        this.expander = new Ext.ux.grid.RowExpander({
            tpl : '<div class="x-icinga-comment-expander-row">{COMMENT_DATA}</div>'
        })
        
        this.grid = new Ext.grid.GridPanel({
        	layout : 'fit',
        	store : this.store,
        	colModel : new Ext.grid.ColumnModel({
        		defaults : {
        			
        		},
        		columns : [this.expander, {
        			header : _('Id'),
        			dataIndex : 'COMMENT_ID'
        		}, {
        			header : _('Entry time'),
        			dataIndex : 'COMMENT_TIME'
        		}, {
        			header : _('Author'),
        			dataIndex : 'COMMENT_AUTHOR_NAME'
        		}]
        	}),
        	
        	viewConfig: {
        		forceFit : true
        	},
        	
        	plugins : [
        	   this.expander
        	]
        });
        
        this.add(this.grid);
        
        this.doLayout();
    },
    
    setObjectId : function(oid) {
    	if (oid === this.objectId && this.dataLoaded === true) {
    		return true;
    	} 
    	
    	this.loadCommentsForObjectId(oid);
    },
    
    getObjectId : function() {
    	return this.objectId;
    },
    
    loadCommentsForObjectId : function(oid) {
    	
    	this.store.setFilter({
    		type : 'AND',
    		field : [{
    			type : 'atom',
    			field : ['COMMENT_OBJECT_ID'],
    			method : ['='],
    			value : [oid]
    		}]
    		
    	});
    	
    	this.store.load();
    }
});

Ext.reg('cronks-tackle-information-comments', Icinga.Cronks.Tackle.Information.Comments);