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
/*global Ext: false, Icinga: false, AppKit: false, _: false, Cronk: false */

Ext.ns("Ext.grid");

(function () {

    "use strict";
    
    Ext.override(Ext.grid.GridView, {
        /**
         * @private
         * Updates the size of every column and cell in the grid
         */
        updateAllColumnWidths : function() {
            var totalWidth = this.getTotalWidth(),
                colCount   = this.cm.getColumnCount(),
                rows       = this.getRows(),
                rowCount   = rows.length,
                widths     = [],
                row, rowFirstChild, trow, i, j;

            for (i = 0; i < colCount; i++) {
                widths[i] = this.getColumnWidth(i);
                var cell = this.getHeaderCell(i);
                if (cell) {
                    cell.style.width = widths[i];
                } else {
                    // Call later on, things not ready yet
                    this.updateAllColumnWidths.defer(20, this);
                }
                
            }

            this.updateHeaderWidth();

            for (i = 0; i < rowCount; i++) {
                row = rows[i];
                row.style.width = totalWidth;
                rowFirstChild = row.firstChild;

                if (rowFirstChild) {
                    rowFirstChild.style.width = totalWidth;
                    trow = rowFirstChild.rows[0];

                    for (j = 0; j < colCount; j++) {
                        trow.childNodes[j].style.width = widths[j];
                    }
                }
            }

            this.onAllColumnWidthsUpdated(widths, totalWidth);
        }
    });
    
})();