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
           return this.loadTask.delay(200,null,this,arguments);
       }
    });
    Ext.ns("Ext.ux").LazyGroupingStore = Ext.extend(Ext.data.GroupingStore,{
       constructor: function() {
           this.loadTask = new Ext.util.DelayedTask(Ext.data.GroupingStore.prototype.load);
           return Ext.data.GroupingStore.prototype.constructor.apply(this,arguments);
       },
       
       load: function() {
           return this.loadTask.delay(200,null,this,arguments);
       }
    });
})();


