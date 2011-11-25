Ext.ns("Icinga.Api");
Icinga.Api.Provider = new (function() {
    var storeFactory = new Icinga.Store.StoreFactory();
    var gridFactory = new Icinga.Grid.GridFactory(); 

    this.getProviderDescriptor = function(module,provider,store) {
        var providerDesc = module+"_"+provider; 
        if(!Icinga.Api[providerDesc]) {
            AppKit.log("Provider "+module+"/"+provider+" does not exist");
            return null; 
        }
        if(!Icinga.Api[providerDesc][store]) {
            AppKit.log("Provider "+module+"/"+provider+" doesn't have a store target "+store);
            return null;
        }
        return Icinga.Api[providerDesc][store];

    };

    this.getStoreFor = function(module,provider,store,db,overrides) { 
        return storeFactory.getStoreFor.apply(storeFactory,arguments);
    };          

    this.getGridFor = function(module,provider,store,db) {
        return gridFactory.getGridFor.apply(gridFactory,arguments);
                          
    };   
})();

