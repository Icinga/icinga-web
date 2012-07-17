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
Ext.ns('Cronk.grid.filter');


(function () {

    "use strict";

    /**
     * IcingaApiComboBox
     * Extended to let meta data from xml template
     * configure the store to fetch data from the IcingaAPI
     */
    Cronk.grid.filter.ApiComboBox = Ext.extend(Ext.form.ComboBox, {

        def_webpath: '/modules/web/api/json',
        def_sortorder: 'asc',

        constructor: function (cfg, meta) {

            var kf = meta.api_keyfield; // ValueField
            var vf = meta.api_valuefield; // KeyField

            var fields = [];
            var cols = [];

            var cfields = {};
            cfields[kf] = true;
            cfields[vf] = true;

            if (meta.api_id) {
                cfields[meta.api_id] = true;
            }

            // If we need more fields to work with
            if (meta.api_additional) {
                var i = meta.api_additional.split(',');
                for (var k in i) {
                    if (Ext.isString(i[k])) {
                        cfields[i[k]] = true;
                    }
                }
            }

            for (var f in cfields) {
                if (f) { // jshint requirement
                    cols.push(f);
                    fields.push({
                        name: f
                    });
                }
            }

            var apiStore = new Ext.data.JsonStore({
                autoDestroy: true,
                url: AppKit.c.path + this.def_webpath,

                root: 'result',

                baseParams: {
                    target: meta.api_target,
                    order_col: (meta.api_order_col || meta.api_keyfield),
                    order_dir: (meta.api_order_dir || this.def_sortorder),
                    columns: cols
                },

                idProperty: (meta.api_id || meta.api_keyfield),

                fields: fields,

                listeners: {
                    beforeload: function (store, options) {
                        if (!Ext.isEmpty(store.baseParams.query)) {
                            store.baseParams.filters_json = Ext.util.JSON.encode({
                                type: 'AND',
                                field: [{
                                    type: 'atom',
                                    field: [vf],
                                    method: ['like'],
                                    value: [String.format('*{0}*', store.baseParams.query)]
                                }]
                            });
                        }
                    }
                }
            });

            apiStore.load();



            cfg = Ext.apply(cfg || {}, {
                store: apiStore,
                displayField: vf,
                valueField: vf,
                keyField: kf
            });

            // To display complex multi column layouts
            if (meta.api_exttpl) {
                cfg.tpl = '<tpl for="."><div class="x-combo-list-item">' + meta.api_exttpl + '</div></tpl>';
            }

            // Notify the parent class
            Cronk.grid.filter.ApiComboBox.superclass.constructor.call(this, cfg);


        }
    });

})();