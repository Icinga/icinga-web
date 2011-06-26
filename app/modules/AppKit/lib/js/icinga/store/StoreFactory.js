/**
* Creates Ext.data.JsonStore objects by a descriptor returned from
* Icinga-webs Parsing api
**/
Ext.ns("Icinga.Store");
Icinga.Store.StoreFactory = function() {
    var getFieldDefinition = function(descriptor) {
        var fields = [];
        for(var i in descriptor.fields.allowedFields) {
            fields.push(descriptor.fields.allowedFields[i]);
        }
        return fields;
    } 
    
    var getBaseParams = function(descriptor,module,provider,db) {
        var params = {
            module: module,
            action: provider,
            output_type: 'json',
            params: {
                databse: db
            }
        };
         
    }
  
    var extendStore = function(store) {
    }

    this.getStoreFor = function(module,provider,store,db,overrides) {
        db = db || "icinga";
        var descriptor = Icinga.Api.Provider.getProviderDescriptor(module,provider,store);
        
        if(!descriptor.fields) {
            AppKit.log("Missing field description in store "+store+"("+module+","+provider+")");
            return null
        }
        var cfg = {
            fields: getFieldDefinition(descriptor), 
            url: AppKit.c.path+'/appkit/dispatch',
            storeId: module+"_"+provider+"_"+store,
            baseParams: getBaseParams(descriptor,module,provider,db)
        };
        for(var i in overrides) 
            cfg[i] = overrides[i]; 
        var store = Ext.extend(Ext.data.Store,cfg);
        extendStore(store);
        return store; 
    }

}

