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
        Ext.TaskMgr.start({
            run: this.updateClock,
            interval: 10000,
            scope:this
        });
        cfg.style = {margin : "3px"};
        Ext.menu.BaseItem.prototype.constructor.call(this,cfg);
        
   },
   // private
   onRender : function(container, position){
        Ext.menu.BaseItem.superclass.onRender.apply(this, arguments);
    }
    
});

