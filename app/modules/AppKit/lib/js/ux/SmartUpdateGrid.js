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
 * Grid implementation that doesn't refresh the whole page, but rather
 * updates rows that changed, removes rows that are not visible anymore and
 * adds new rows.
 *
 */

Ext.namespace('Ext.ux.grid').SmartUpdateGridView = Ext.extend(Ext.grid.GridView,{
    initialRender: false,
    
    onClear:function() {
        AppKit.log("Clear called");
        Ext.grid.GridView.prototype.onClear.apply(this,arguments);
    },

    onDataChange: function() {
        if(this.initialRender == true) {
            AppKit.log("updating rows");
            this.ds.each(function(record) {
                this.refreshRow(record);
            },this)
        } else {
            this.initialRender = true;
            Ext.grid.GridView.prototype.onDataChange.apply(this,arguments);
            AppKit.log("refreshing all rows");
        }
    }
});

Ext.namespace('Ext.ux.grid').SmartUpdateGrid = Ext.extend(Ext.grid.GridPanel,{


    getView : function() {
        if (!this.view) {
            this.view = new Ext.ux.grid.SmartUpdateGridView(this.viewConfig);
        }

        return this.view;
    }
});
