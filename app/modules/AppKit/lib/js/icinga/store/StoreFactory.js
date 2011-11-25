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
    }; 
  
     
    var getBaseParams = function(descriptor,module,provider,db) {
        return {
            module: module,
            action: provider,
            output_type: 'json',
            params: Ext.encode({
                database: db
            })
        };         
    };

    
  
    var extendStore = function(store) {
        return Ext.extend(store,{
            dispatcherParams: {}, 
            setDispatcherParam: function(field,value) { 
                this.dispatcherParams[field] = value;
            },
            load: function(options) {
                options =  options || {};
                var dispatcherParams = this.dispatcherParams;
                for(var i in options.dispatcherParams) {
                    dispatcherParams[i] = options.dispatcherParams[i];
                }
                options.params = options.params || {};
                options.params.params = Ext.encode(dispatcherParams);
                return store.prototype.load.call(this,options);
            }
        });
    };

    this.getStoreFor = function(module,provider,store,db,overrides) {
        db = db || "icinga";
        var descriptor = Icinga.Api.Provider.getProviderDescriptor(module,provider,store);

        if(!descriptor.fields) {
            AppKit.log("Missing field description in store "+store+"("+module+","+provider+")");
            return null;
        }
        var cfg = {
            
            fields: getFieldDefinition(descriptor), 
            url: AppKit.c.path+'/modules/appkit/dispatch',
            totalProperty: 'totalCount',       
            storeId: module+"_"+provider+"_"+store,
            baseParams: getBaseParams(descriptor,module,provider,db)
        };
        for(var i in overrides) 
            cfg[i] = overrides[i]; 
        var store = Ext.extend(Ext.data.JsonStore,cfg);
        store = extendStore(store);
       
        
        return store; 
    };

};

