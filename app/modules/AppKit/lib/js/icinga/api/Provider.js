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

