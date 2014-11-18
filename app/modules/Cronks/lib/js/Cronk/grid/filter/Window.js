// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2014 Icinga Developer Team.
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

Ext.ns('Cronk.util');

(function () {

    "use strict";

    /**
     * Single scope object to handle filter changes
     */
    Cronk.grid.filter.Window = function () {
        /**
         * @property {Ext.Window} oWin Filter window
         * @private
         */
        var oWin = null;
        
        /**
         * @property {Object} oFilter Filter descriptor from grid
         * @private
         */
        var oFilter = null;
        
        /**
         * @property {Ext.form.FormPanel} oCoPanel Formpanel the fields are arranged
         * @private
         */
        var oCoPanel = null;
        
        /**
         * @property {Ext.form.ComboBox} oCombo Restrictions selector
         * @private
         */
        var oCombo = null;
        
        /**
         * @property {Cronk.grid.MetaPanel} Grid object created before
         * @property
         */
        var oGrid = null;
        
        /**
         * @property {Ext.data.JsonStore} Store containing restrictions from oFilter
         * @private
         */
        var oRestrictionsStore = null;
        
        /**
         * @property {Object} oOrgBaseParams Filters set by grid creators
         * @private
         */
        var oOrgBaseParams = {};
        
        /**
         * @property {Object} oTemplateMeta Template information from grid
         * @private
         */
        var oTemplateMeta = {};
        
        /**
         * @property {Cronk.grid.filter.Handler} Filterhandler
         * @private
         */
        var oFilterHandler = new Cronk.grid.filter.Handler();

        oFilterHandler.on('compremove', function (fh, panel, meta) {
            if (!meta.id) {
                return true;
            }
            
            var store = getRestrictionsStore();
            var record = new Ext.data.Record(meta);
            store.add(record);
            store.sort("label", "ASC");
            
            if (oGrid.filter_types) {
                delete oGrid.filter_types[meta.id];
            }

            oWindow().doLayout(false, true);

            return true;
        });
        
        /**
         * Creates the window containing the filter
         * @return {Ext.Window}
         */
        function oWindow() {
            if (!oWin) {
                oWin = new Ext.Window({
                    title: _("Modify filter"),
                    closeAction: 'hide',
                    width: 500,
                    autoHeight: true,
                    // layout: 'fit',

                    defaults: {
                        border: false,
                        padding: 5
                    },

                    listeners: {
                        render: function (oc) {

                            if (oGrid.filter_types) {
                                var i = 0;

                                Ext.iterate(oGrid.filter_types, function (key, item) {

                                    // New format (since 1.7.0)
                                    if (Ext.isEmpty(item['fType'])) {
                                        var r = new Ext.data.Record(item);
                                        selectRestrictionHandler(oCombo, r, i);

                                    // Old format, 1.6 versions
                                    } else {
                                        var searchId = item['fType'];
                                        var record = oCombo.getStore().getById(searchId);
                                        if (record) {
                                            selectRestrictionHandler(oCombo, record, i);
                                        }
                                    }
                                    i++;
                                });
                            }

                            if (oGrid.filter_params && oCoPanel) {
                                Ext.iterate(oGrid.filter_params, function (key, val) {
                                    key = key.replace(/^f\[|\]$/g, "");
                                    var c = oCoPanel.findBy(function (ti) {
                                        return ti.hiddenName === key || ti.name === key;
                                    });

                                    if (c[0]) {
                                        // Set value as String (cause of ===)
                                        // and after component is rendered ready
                                        c[0].on('afterrender', function() {
                                            c[0].setValue(String(val));
                                        }, c[0], {single:true});
                                    }
                                });
                            }

                            // Handler to recalculate the window height if
                            // adding or removing components
                            var armh = function (c, item, index) {
                                    this.syncSize();
                                    this.syncShadow();
                                };

                            oc.on('add', armh, oc, {
                                delay: 40
                            });
                            oc.on('remove', armh, oc, {
                                delay: 40
                            });
                        },

                        afterrender: function () {
                            this.doLayout(false, true);
                        },

                        hide: function (oc) {
                            oGrid.filter_params = getFormValues(false);
                            oGrid.fireEvent('activate');
                        }
                    },

                    keys: {
                        key: 13,
                        fn: function () {
                            this.applyFilters();
                        },
                        scope: pub
                    },
                    tbar: [{
                        text: _("Reset"),
                        iconCls: 'icinga-icon-delete',
                        handler: function (b, y) {
                            pub.resetFilterForm();
                        }

                    }],
                    buttons: {
                        items: [{
                            text: _("Apply"),
                            iconCls: 'icinga-icon-accept',
                            handler: function (b, e) {
                                pub.applyFilters();
                            }
                        }, {
                            text: _("Cancel"),
                            iconCls: 'icinga-icon-cross',
                            handler: function (b, y) {
                                oWin.hide();
                            }
                        }]
                    }
                });
            }

            return oWin;
        }
        
        /**
         * Getter for restrictions store
         * @return {Ext.data.JsonStore}
         */
        function getRestrictionsStore() {
            if (oRestrictionsStore === null) {
                    oRestrictionsStore = new Ext.data.JsonStore({
                    autoDestroy: true,
                    root: "filter",
                    fields: ["api_keyfield", "api_target", "api_valuefield", 
                        "enabled", "id", "label", "name", "operator_type", 
                        "subtype", "type"]
                });

                oRestrictionsStore.loadData({filter: oFilter});
                
                oRestrictionsStore.sort("label", "ASC");
            }
            
            return oRestrictionsStore;
        }
        
        /**
         * Create needed controls to work with filters, form panel
         * restriction combo box and so on
         * @return {Boolean} Always true
         */
        function prepareFilter() {
            var w = oWindow();

            if (!oCoPanel) {

                oCoPanel = new Ext.form.FormPanel({
                    id: 'filter-' + oGrid.getId(),
                    defaults: {
                        border: false
                    }
                });
                
                oCombo = new Ext.form.ComboBox({
                    store: getRestrictionsStore(),
                    'name': '__restriction_selector',
                    mode: 'local',
                    typeAhead: true,
                    triggerAction: 'all',
                    forceSelection: false,
                    fieldLabel: _("Add restriction"),
                    valueField: 'type',
                    displayField: 'label',

                    listeners: {
                        select: selectRestrictionHandler
                    }
                });

                oCoPanel.add({
                    layout: 'form',
                    style: 'padding: 5px;',
                    items: oCombo
                });

                // Glue together
                w.add(oCoPanel);
            }

            return true;

        }
        
        /**
         * Event handler if a restriction is picked
         * @param {Ext.form.ComboBox} oCombo
         * @param {Ext.data.Record} record
         * @param {Number} index
         */
        function selectRestrictionHandler(oCombo, record, index) {
            // Reset the combo
            oCombo.setValue('');

            // Add a new field construct
            addResctriction(record);

            // Remove the selected item from the store
            oCombo.getStore().removeAt(index);

            var tmp = oGrid.filter_types || {};
            tmp[record.get("id")] = record.data;
            oGrid.filter_types = tmp;
        }
        
        /**
         * Let the filter handler create a component and add to our
         * window
         * 
         * @param {Ext.data.Record} record
         */
        function addResctriction(record) {
            
            oCoPanel.add(oFilterHandler.createComponent(record.data));

            // Notify about changes
            oCoPanel.doLayout(false, true);
        }
        
        /**
         * Return a ready to use filter configuration you can pass
         * as post filter
         * 
         * @return {Object}
         */
        function getFormValues() {
            var data = {};

            var items = oCoPanel.getForm().items;

            // Trigger fields are very slow to update its "raw value"
            // Force validation when getting the values (KeyPress event
            // is often faster than the element validation
            // (fixes #1955)
            items.each(function (item, index, len) {
                if (Ext.isFunction(item.assertValue)) {
                    // AppKit.log("-> Can assert");
                    item.assertValue();
                }
            });

            try {
                data = oCoPanel.getForm().getValues();
            } catch (e) {
                data = {};
            }
            var o = {};

            for (var k in data) {
                if (k.indexOf('__') !== 0) {
                    o['f[' + k + ']'] = data[k];
                }
            }

            return o;
        }

        var pub = {

            removeRestrictionHandler: function (b, e) {

            },

            /**
             * The handler to init the window and show the filter restrictinos
             */
            startHandler: function (b, e) {
                var win = oWindow();
                win.setPosition(b.el.getLeft(), b.el.getTop());
                win.show(b.el);
            },

            /**
             * Sets the filter cfg parsed from IcingaMetaGridCreator
             * @param {Object} f
             */
            setFilterCfg: function (f) {
                oFilter = f;
                prepareFilter();
            },

            /**
             * Sets the grid object, we need this to apply 
             * the filter to the store
             * @param {Cronk.grid.MetaPanel} g
             */
            setGrid: function (g) {
                oGrid = g;
                var store = oGrid.getStore();
                
                oGrid.on('activate', function () {
                    if (oCoPanel) {
                        oGrid.filter_params = getFormValues(false);
                    }
                    return true;
                }, this);

                store.on('datachanged', function (store) {
                    this.markActiveFilters();
                }, this);
                
                // Sets the persistent filter so nobody can remove
                // it later
                store.on("beforeload", function() {
                    if (Ext.isEmpty(store.originParams) === false) {
                        oOrgBaseParams = store.originParams;
                        
                        var rstore = getRestrictionsStore();
                        
                        Ext.iterate(oOrgBaseParams, function(key, val) {
                            if (key.match(/\[(\w+)-(value|operator)\]/)) {
                                var filter_id = RegExp.$1;
                                var index = rstore.find("id", filter_id);
                                
                                if (index > -1) {
                                    rstore.removeAt(index);
                                }
                            }
                        }, this);
                    }
                }, this, {buffer: 200});
                
                if (oGrid.filter_params) {
                    oGrid.on("render", this.applyFilters.createDelegate(this, oGrid.filter_params));
                }
            },
            
            /**
             * Setter for meta data
             * @param {Object} meta
             */
            setMeta: function (meta) {
                oTemplateMeta = meta;
                oFilterHandler.setMeta(oTemplateMeta);
            },

            /**
             * If the parent object destroys, destroy our objects too
             */
            destroyHandler: function () {
                oWindow().hide();

                // Objects              
                oWindow().destroy();
                if (oCoPanel) {
                    oCoPanel.destroy();
                }

                // Data
                oOrgBaseParams = {};
                oFilter = {};
            },

            /**
             * Tests for filters and colorize
             */
            markActiveFilters: function () {
                var btn = Ext.getCmp(oGrid.id + "_filterBtn");
                
                if (!btn) {
                    this.markActiveFilters.defer(200, this);
                    return true;
                }
                
                var check = false;
                for (var i in oGrid.filter_params) {
                    if (i) {
                        check = true;
                        break;
                    }
                }

                if (check) {
                    btn.addClass("activeFilter");
                } else {
                    btn.removeClass("activeFilter");
                }
                
                return true;
            },

            /**
             * If a restriction was made, this method applies the restrictins
             * to the store
             */
            applyFilters: function (owd) {
                var data = owd || getFormValues();
                oGrid.getStore().baseParams = {};
                Ext.apply(oGrid.getStore().baseParams, oOrgBaseParams);
                Ext.apply(oGrid.getStore().baseParams, data);


                oGrid.getStore().load();

                oGrid.fireEvent('activate');

                oWindow().hide();
            },

            /**
             * Reset the base params to its default and reload
             * the store
             */
            removeFilters: function () {
                oGrid.getStore().baseParams = oOrgBaseParams;
                oGrid.filter_params = null;
                oGrid.filter_types = null;
                oGrid.getStore().load();

                var btn = Ext.getCmp(oGrid.id + "_filterBtn");

                if (btn) {
                    btn.removeClass("activeFilter");
                }

                oFilterHandler.removeAllComponents();
                oGrid.fireEvent('activate');
            },
            
            /**
             * Remove all filter from panel the fire
             * 'activate'
             */
            resetFilterForm: function () {
                oFilterHandler.removeAllComponents();
                oGrid.fireEvent('activate');
            }

        };

        return pub;

    };

})();