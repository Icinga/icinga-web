/* 
 * Little widget that displays the currents servertime
 * 
 */
Ext.ns('AppKit.util');

AppKit.util.Servertime = Ext.extend(Ext.menu.BaseItem, {
    TIME_URL: '/modules/appkit/servertime',
    updateClock: function () {
        this.getEl().getUpdater().showLoadIndicator = false;
       
        this.getEl().load({
            url:AppKit.c.path+this.TIME_URL
        });
    },
    constructor: function(cfg) {
        cfg = cfg || {};
        Ext.menu.BaseItem.prototype.constructor.call(this,cfg);
        
   },
   
   initComponent : function() {
   	    AppKit.util.Servertime.superclass.initComponent.call(this);
   	    
   	    this.on('afterrender', function() {
   	    	if (this.getEl()) {
	            AppKit.getTr().start({
	                run: this.updateClock,
	                interval: 60000,
	                scope:this
	            });
   	    	}
   	    }, this, { single : true  });
   },
   
   // private
   onRender : function(container, position){
        Ext.menu.BaseItem.superclass.onRender.apply(this, arguments);
    }
    
});

