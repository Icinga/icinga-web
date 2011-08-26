Ext.ns('Icinga.Grid.Plugins');
Icinga.Grid.Plugins.PageableGrid = function(cfg) {
    this.target = null;
    this.tbarRef = null;
    
    var gridPageTbar = Ext.extend(Ext.PagingToolbar,{
        doLoad : function(start){
            var o = {}, pn = this.getParams();
            o[pn.start] = start;
            o[pn.limit] = this.pageSize;
            if(this.fireEvent('beforechange', this, o) !== false){
                this.ownerCt.super.load.call(this.ownerCt,{params:o});
            }
        },
        onLoad : function(store, r, o) {
    
            Ext.PagingToolbar.prototype.onLoad.apply(this,[store,r,{params: store.dispatcherParams}]);       
        }
    });
    this.constructor = function(descriptor, gridCfg) {
        descriptor = descriptor.pagination; 
        
        gridCfg.storeParamNames.start = descriptor.params.offset;
        gridCfg.storeParamNames.limit = descriptor.params.limit;
       

        this.tbarRef =  new gridPageTbar({
            
            displayInfo:true
        }); 
        gridCfg.bbar = this.tbarRef;
    }
    this.init = function(grid) {
        this.target = grid;
        this.target.super = {
            load: this.target.load
        }
        this.target.load = this.tbarRef.doLoad.createDelegate(this.tbarRef);
        this.tbarRef.bindStore(grid.getStore());
    }
    this.constructor.apply(this,arguments);

};
