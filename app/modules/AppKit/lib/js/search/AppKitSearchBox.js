Ext.ns('AppKit.search');

AppKit.search.Searchbox = Ext.extend(Ext.Panel, {
    
    width : 16,
    height : 16,
    border : false,
    
    stopShow : false,
    
    constructor : function(c) {
    	c = c || {};
    	
        Ext.apply(c, {
            bodyCfg : {
                tag : 'div',
                cls : 'icinga-action-icon-search',
                html : ''
            }
        });
    	
    	AppKit.search.Searchbox.superclass.constructor.call(this, c);
    },
    
    initComponent : function() {
        AppKit.search.Searchbox.superclass.initComponent.call(this);
        
        this.on('afterrender', function() {
        	this.getEl().on('mouseleave', this.onMouseleave, this);
        	this.getEl().on('mouseenter', this.onMouseenter, this);
        	this.getEl().on('click', this.onMouseenter, this);
        }, this, { single : true });
        
        this.keymap = new Ext.KeyMap(Ext.getBody(), {
        	key : Ext.EventObject.F,
        	ctrl : true,
        	alt : true,
        	fn : (function() {
        		
        		win = this.getSearchbox();
        		if (win.hidden === true) {
        		  this.onMouseenter.defer(100, this);
        		} else {
        			win.hide();
        		}
        		
        	}).createDelegate(this)
        })
    },
    
    getSearchbox : function() {
    	if (Ext.isEmpty(this.searchBox)) {
    		this.searchBox = new Ext.Window({
    			closable : true,
    			collapsible : false,
    			draggable : false,
    			resizable : false,
    			x : 100,
    			y : 20,
    			height : 55,
    			unstyled : false,
    			closeAction : 'hide',
    			title : _('Search'),
    			iconCls : 'icinga-action-icon-search',
    			renderTo : Ext.getBody(),
    			border : false,
    			shadow : false,
    			
    			items : [{
    				bubbleEvents : ['blur'],
    				xtype : 'textfield',
    				enableKeyEvents : true,
    				id : 'global_search',
    				name : 'global_search',
    				width : 300,
    				
    				listeners : {
    					keyup : function(field, event) {
    						AppKit.search.SearchHandler.doSearch(field.getValue());
    					}
    				}
    			}],
    			
    			listeners : {
    				show : function(wnd) {
    					var field = wnd.findById('global_search');
    					field.setValue("");
    					field.focus(true, 100);
    				},
    				
    				blur : function(field) {
    					this.hide();
    				},
    				
    				hide : this.onMouseleave.createDelegate(this)
    			}
    		});
    	}
    	
    	return this.searchBox;
    },
    
    showSearch : function() {
    	if (this.stopShow === false) {
            this.stopShow = true;
            
	    	var win = this.getSearchbox();
	    	
	        if (win.hidden === true) {
	        	
	            win.setPosition(this.getEl().getX() - 300, 27);
	        	
	            win.show(this.getEl());
	        }
    	}
    },
    
    onMouseenter : function(event, element, object) {
    	this.showSearch(event);
    },
    
    onMouseleave : function(event, element, object) {
    	this.stopShow = false;
    }
    
});