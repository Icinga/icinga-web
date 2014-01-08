// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-present Icinga Developer Team.
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

/*jshint browser:true, curly:false */
/*global Ext:true */
(function() {
    "use strict";
    Ext.ns("Icinga.Cronks.util").FilterState = Ext.extend(Ext.util.Observable,{

        constructor: function(cfg) {
            this.grid = cfg.grid;

            this.tree = cfg.tree;
            Ext.util.Observable.prototype.constructor.apply(this,arguments);
            this.tree.on("filterchanged",this.applyFilterToGrid,this);
        },

        update: function(filter) {
            this.tree.setLastState(filter);
            this.grid.getStore().setBaseParam("filter_json", Ext.encode(filter));
        },

        applyFilterToGrid: function(filter) {
            var store = this.grid.getStore();
            if(filter)
                store.setBaseParam("filter_json", Ext.encode(filter));
            else
                delete store.baseParams.filter_json;
            store.reload();
        }

    });

})();
