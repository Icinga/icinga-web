// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}

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

