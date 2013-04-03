// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2013 Icinga Developer Team.
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
/*global Ext: false, Icinga: false, AppKit: false, _: false, Cronk: false */

Ext.ns("Ext.grid");

(function () {

    "use strict";

    Ext.override(Ext.grid.ColumnModel, {
        addColumn: function (column, colIndex) {
            if (typeof column === 'string') {
                column = {
                    header: column,
                    dataIndex: column
                };
            }
            var config = this.config;
            this.config = [];
            if (typeof colIndex === 'number') {
                config.splice(colIndex, 0, column);
            } else {
                colIndex = config.push(column);
            }
            
            
            
            this.setConfig(config, true);
            return colIndex;
        },
        
        removeColumn: function (colIndex) {
            var config = this.config;
            this.config = [config[colIndex]];
            config.splice(colIndex, 1);
            this.setConfig(config);
        }
    });

})();