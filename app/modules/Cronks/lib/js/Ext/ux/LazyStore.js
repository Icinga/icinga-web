/* 
 * Ext.ux.Store that delays loading 
 */
(function() {
    "use strict";
    Ext.ns("Ext.ux").LazyStore = Ext.extend(Ext.data.Store,{
       constructor: function() {
           this.loadTask = new Ext.util.DelayedTask(Ext.data.Store.prototype.load);
           return Ext.data.Store.prototype.constructor.apply(this,arguments);
       },
       
       load: function() {
           this.loadTask.delay(200,null,this,arguments);
           return true;
       }
    });
    Ext.ns("Ext.ux").LazyGroupingStore = Ext.extend(Ext.data.GroupingStore,{

       load: function() {
           if(!this.loadTask)
               this.loadTask = new Ext.util.DelayedTask(Ext.data.Store.prototype.load);
           this.loadTask.delay(200,null,this,arguments);
           return true;
       }
    });
})();


