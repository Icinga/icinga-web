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

Ext.ns("Cronk.grid.handler");

(function () {

    "use strict",

    /**
     * Create a new template grid from configuration
     * @cfg {String} idPrefix Make your grid types unique while flagging a prefix
     * @cfg {String} titlePrefix First string to the title
     * @cfg {String} labelField Field from source template, added to the title
     * @cfg {String} additionalSort Field to initially sort the data
     * @cfg {Object} filterMap Mapping from sourcefield values to target for initially filter
     * @cfg {String} sourceField Single filter map source field name
     * @cfg {String} targetField Single filter map target field name
     *
     * If filterMap is not used sourceField and targetField are used to
     * connect the grids together
     */
    Cronk.grid.handler.Grid = {

        openTemplateGrid: function() {
            var args = this.getHandlerArgs();
            var record = this.getRecord();

            var id = (args.idPrefix || 'empty') + '_subgrid_component';

            var cronk = {
                id: id,
                parentid: id,
                title: (args.titlePrefix || '') + " " + record.get(args.labelField),
                crname: 'gridProc',
                closable: true,
                allowDuplicate: true,
                params: {
                    template: args.template,
                    module: 'Cronks',
                    action: 'System.ViewProc'
                }
            };

            var filter = {};

            if (args.filterMap) {
                Ext.iterate(args.filterMap, function (k, v) {
                    filter["f[" + v + "-value]"] = record.data[k];
                    filter["f[" + v + "-operator]"] = 50;
                });
            } else {
                filter["f[" + args.targetField + "-value]"] = record.data[args.sourceField];
                filter["f[" + args.targetField + "-operator]"] = 50;
            }

            if (args.additionalSort) {
                filter.additional_sort_field = args.additionalSort;
            }

            filter.connection = this.getGrid().selectedConnection;

            Cronk.util.InterGridUtil.gridFilterLink(cronk, filter);
        }

    };

})();
