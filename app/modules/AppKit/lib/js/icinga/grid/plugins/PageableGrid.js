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
                this.ownerCt._super.load.call(this.ownerCt,{params:o});
            }
        },
        onLoad : function(store, r, o) {
    
            Ext.PagingToolbar.prototype.onLoad.apply(this,[store,r,{params: store.dispatcherParams}]);       
        }
    });
    this.constructor = function(descriptor, gridCfg) {
        

        this.tbarRef =  new gridPageTbar({
            
            displayInfo:true
        }); 
        gridCfg.bbar = this.tbarRef;
    };
    this.init = function(grid) {
        this.target = grid;
        this.target._super = {
            load: this.target.load
        };
        this.target.load = this.tbarRef.doLoad.createDelegate(this.tbarRef);
        this.tbarRef.bindStore(grid.getStore());
    };
    this.constructor.apply(this,arguments);

};
