/*jshint browser:true, curly:false */
/*global Ext:true, _:true */
Ext.ns("Icinga.Cronks.util").FilterState = Ext.extend(Ext.util.Observable,{
   
    
    constructor: function(cfg) {
        this.grid = cfg.grid;
        
        this.tree = cfg.tree;
        Ext.util.Observable.prototype.constructor.apply(this,arguments);
        this.tree.on("filterchanged",this.applyFilterToGrid,this)
    },

    update: function(filter) {
        this.tree.setLastState(filter);
        this.grid.getStore().setBaseParam("filter_json", Ext.encode(filter));
    },

    save: function() {
        this.applyFilterToGrid(this.tree.treeToFilterObject());
        this.grid.fireEvent('activate');

    },

    applyFilterToGrid: function(filter) {
        var store = this.grid.getStore();
        if(filter)
            store.setBaseParam("filter_json", Ext.encode(filter));
        else 
            delete store.baseParams["filter_json"];
        store.reload();
    }
    
    
    
});