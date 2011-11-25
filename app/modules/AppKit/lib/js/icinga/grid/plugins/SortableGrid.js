Ext.ns('Icinga.Grid.Plugins');
Icinga.Grid.Plugins.SortableGrid = function(cfg) {
    this.target = null;
   
    this.constructor = function(descriptor, gridCfg) {
        
        gridCfg.storeParamNames.sort = descriptor.sort.params.sortfield;
        gridCfg.storeParamNames.dir = descriptor.sort.params.dir;
        for(var i = 0;i<descriptor.fields.sortFields.length;i++) {
            
            gridCfg.canSort[descriptor.fields.sortFields[i]] = true; 
        }
        
    };
    this.init = function(grid) {
        this.target = grid;
    };
    this.constructor.apply(this,arguments);

};
