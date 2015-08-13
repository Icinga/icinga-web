// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2015 Icinga Developer Team.
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

Ext.namespace('Ext.ux.grid');

(function() {

    "use strict";

    Ext.ux.grid.EllipsisColumn = Ext.extend(Ext.grid.Column, {
        selectableClass: 'x-icinga-grid-cell-selectable',

        constructor: function(c) {
            Ext.ux.grid.EllipsisColumn.superclass.constructor.call(this, c);
            var vname = '{' + this.dataIndex + '}';

            // Removed the record wrapper and added html encoded qtip
            // to provide HTML in customvars #4015
            this.tpl = new Ext.XTemplate('<span ext:qtip="{__data_encoded}">{__data}</span>');

            this.renderer = (function(value, p, r) {
                p.css += ' ' + this.selectableClass;
                var data = r.get(this.dataIndex);
                return this.tpl.apply({
                    __data_encoded: Ext.util.Format.htmlEncode(data),
                    __data: data
                });
            }).createDelegate(this);
        }
    });

    Ext.grid.Column.types.ellipsiscolumn = Ext.ux.grid.EllipsisColumn;

})();
