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

Ext.ns("Cronk.grid");

(function () {

    "use strict";

    /**
     * Grid panel created by json configuration, handles some useful
     * controls internally like filters, command, events and so on
     * 
     * @class
     */
    Cronk.grid.MetaGridPanel = Ext.extend(Ext.grid.GridPanel, {
        trackMouseOver: false,
        disableSelection: false,
        loadMask: true,
        collapsible: false,
        animCollapse: true,
        unstyled: true,
        border: false,
        emptyText: _("No data was found"),
        layout: 'fit',
        //baseCls: 'icinga-metagrid', // Allow style moifications

        selectedConnection: 'icinga',

        stateEvents: ['autorefreshchange', 'activate', 'columnmove ', 'columnresize', 'groupchange', 'sortchange', 'afterrender', 'connectionmodify'],

        /**
         * Constructor
         * @param {Object} config
         */
        constructor: function (config) {

            if (Ext.isEmpty(config.meta)) {
                throw new Error("config.meta not set");
            }
            
            if (Ext.isEmpty(config.template)) {
                throw new Error("config.template not set");
            }
            
            this.metaCache = {};
            

            Cronk.grid.MetaGridPanel.superclass.constructor.call(this, config);
        },
        
        /**
         * Getter for the template name
         * @return {String} Name of the template (id)
         */
        getTemplate: function() {
            return this.template;
        },

        /**
         * Helper function to crawl the meta information in form
         * od ns identifiers: keys, field.name, template.option
         * 
         * This method writes cache information to speed up return
         * values
         * 
         * @param {String} ns
         * @param {Any} defaultValue
         * @returns {Any}
         */
        getOption: function (ns, defaultValue) {
            if (Ext.isEmpty(this.metaCache[ns])) {

                var parts = String(ns).split(".");
                var meta = this.meta;

                Ext.iterate(parts, function (part) {
                    if (!Ext.isEmpty(meta[part])) {
                        meta = meta[part];
                    } else {
                        meta = null;
                        return false;
                    }
                }, this);

                var value = meta || defaultValue;

                this.metaCache[ns] = value;

                return value;
            }

            return this.metaCache[ns];
        },

        /**
         * Helper to iterate over all fields
         * @param {Function} cb
         */
        fieldIterator: function (cb) {
            Ext.iterate(this.getOption("keys"), function (dataIndex) {
                var field = this.getOption("fields." + dataIndex);
                var rv = cb.call(this, dataIndex, field);
                if (rv === false) {
                    return false;
                }
            }, this);
        },

        /**
         * Creates a simple store, json meta package required on
         * datasource
         * @return {Ext.data.Store}
         * @private
         */
        createStore: function () {
            var storeConfig = {
                autoLoad: false,
                autoDestroy: true,
                remoteSort: true,

                proxy: new Ext.data.HttpProxy({
                    url: this.url
                }),

                paramNames: {
                    start: 'page_start',
                    limit: 'page_limit',
                    dir: 'sort_dir',
                    sort: 'sort_field'
                }
            };

            var StoreClass = Ext.ux.LazyStore;
            var grouping = this.getOption("template.grouping", {});

            if (grouping.enabled === true) {
                StoreClass = Ext.ux.LazyGroupingStore;

                if (!Ext.isEmpty(grouping["Ext.data.GroupingStore"])) {
                    Ext.apply(storeConfig, grouping["Ext.data.GroupingStore"]);
                }

                

                storeConfig.groupField = grouping.field;
                storeConfig.groupOnSort = true;
            }
            
            this.fieldIterator(function (fieldName, field) {
                if (field.order['default'] === true) {
                    field.order.order = field.order.order || field.order.direction;
                    storeConfig.sortInfo = {
                        direction: (field.order.order ? field.order.order.toUpperCase() : 'ASC'),
                        field: fieldName
                    };
                    return false;
                }
            });

            /*
             * This is very inconvenient. Because of using two types
             * of stores (default and grouping) we need to create our
             * field definitions out of records and predefine a reader
             * object.
             * 
             * We need to pre add the fields here. If the meta package
             * from json is requested, it's too late
             * 
             * (Because of sorting and apply state)
             */

            var fields = [];

            this.fieldIterator(function (item, field) {
                fields.push({
                    name: item,
                    mapping: item,
                    sortType: "asText"
                });
            });


            var record = Ext.data.Record.create(fields);

            storeConfig.reader = new Ext.data.JsonReader({
                idProperty: "id",
                root: "rows",
                successProperty: "success",
                totalProperty: "total"
            }, record);

            /*
             * Configuration is done, just create out object and 
             * provide  to our grid
             */
            var store = new StoreClass(storeConfig);

            if (!this.parameters.storeDisableAutoload) {
                store.load();
            }

            return store;
        },

        /**
         * Calls a grid function to create callbacks or create
         * simple call backs based on specific XML formats.
         * 
         * This method is used to make functions ready to use on grid events
         * or for renderes for columns
         * 
         * @param {Object} struct
         * @param {String} columnName
         * @return {Object}
         * @private
         */
        createCallback: function (struct, columnName) {
            if (!Ext.isEmpty(struct["function"])) {
                var fname = null;
                var f = null;
                var ns = null;

                if (!Ext.isEmpty(struct.namespace)) {
                    ns = Ext.decode(struct.namespace);
                    fname = struct.namespace + "." + struct["function"];
                } else {
                    fname = struct["function"];
                }
                try {
                    var fcreate = Ext.decode(fname);
                } catch(ex) {
                    AppKit.log("Function : "+fname+" not defined anywhere");
                    return false;
                }
                var args = Ext.apply({}, struct["arguments"] || {});

                /*
                 * If we have a column, add this to the arguments and
                 * call the method to create a callback
                 */
                if (!Ext.isEmpty(columnName)) {
                    args.field = columnName;
                    f = fcreate.call(this, args);
                } else {

                    /*
                     * Otherwise this is a event, add our object to method
                     * delegation
                     */
                    Ext.apply(args, {
                        grid: this,
                        store: this.getStore(),
                        meta: this.meta
                    });

                    f = fcreate.createDelegate(ns || this, [args], true);
                }

                if (Ext.isFunction(f)) {
                    return {
                        fn: f,
                        scope: ns || this
                    };
                }
            }
        },

        /**
         * Decide where the callback should be connected, renderer
         * group renderer or column event (more gridevent ...)
         * @param string dataIndex Internal column name
         * @param {Object} column Column description
         * @param {Object} struct Callback description
         * @private
         */
        addGridColumnEvent: function (dataIndex, column, struct) {

            if (Ext.isEmpty(struct.type)) {
                struct.type = "renderer";
            }

            var cb = this.createCallback(struct, dataIndex);
            if(!cb)
                return false;
            if (struct.type === "renderer") {
                column.renderer = cb;
            } else if (struct.type === "grouprenderer") {
                column.groupRenderer = cb;
            } else {
                this.on(struct.type, cb.fn, cb.scope || this);
            }
        },

        /**
         * Take initial events bound to the whole component and register
         * @private
         */
        addGridGlobalEvents: function () {
            var events = this.getOption("template.option.gridEvents", []);

            Ext.iterate(events, function (event) {
                var cb = this.createCallback(event);
                this.on(event.type, cb.fn, cb.scope || this);
            }, this);
        },
        
        /**
         * Return row events
         * @return {Object}
         */
        getRowEvents: function() {
            return this.getEvents("template.option.rowEvents");
        },
        
        /**
         * Return global events
         * @return {Object}
         */
        getGlobalEvents: function() {
            return this.getEvents("template.option.globalEvents");
        },
        
        /**
         * @private
         * Return event structure suitable for 
         * {@link Cronk.grid.plugins.RowActionPanel RowActionPanel Plugin}
         * @param {String} configns
         * @return {Object}
         */
        getEvents: function(configns) {
            var configuration = this.getOption(configns, []);
            
            Ext.iterate(configuration, function(group) {
                if (Ext.isArray(group.items)) {
                    Ext.iterate(group.items, function(menuItem) {
                        if (Ext.isObject(menuItem.handler)) {
                            Ext.iterate(menuItem.handler, function(eventName, fn) {
                                if (Ext.isFunction(fn) === false) {
                                    try {
                                        var localFn = Ext.decode(fn);
                                        
                                        if (Ext.isEmpty(localFn)) {
                                            throw new Error("Method not found: " + fn);
                                        }
                                        
                                        menuItem.handler[eventName] = localFn;
                                    } catch(e) {
                                        AppKit.log("Could not install handler (" + fn + "): " + e.message);
                                    }
                                }
                            }, this);
                        }
                        
                        if (!Ext.isEmpty(menuItem.model)) {
                            AppKit.log("Model config found, not implemented yet!");
                        }
                        
                        // Adding grid referende
                        menuItem.grid = this;
                        
                    }, this);
                }
            }, this);
            
            return configuration;
        },

        /**
         * Create the column model based on the JSON/XML meta description
         * @return {Ext.grid.ColumnModel}
         */
        createColModel: function () {

            var iconTemplate = new Ext.XTemplate([
                '<div class="icinga-grid-header icinga-grid-header-icon">',
                '<div ext:qtip="{label}" class="icon-16 {icon}"></div>',
                '</div>'
            ].join(""));
            
            var columns = [];
            var header = null;
            
            this.fieldIterator(function (val, field) {
                
                if (!Ext.isEmpty(field.display.icon)) {
                    // For very small columns, render icons if
                    // needed. (fixes #3288)
                    header = iconTemplate.apply(field.display);
                } else {
                    header = field.display.label;
                }
                
                var i = columns.push({
                    header: header,
                    dataIndex: val,
                    sortable: (field.order.enabled ? true : false),
                    hidden: (field.display.visible ? false : true)
                });

                if (field.display.width) {
                    columns[i - 1].width = field.display.width;
                }

                if (field.display["Ext.grid.Column"]) {
                    Ext.apply(columns[i - 1], field.display["Ext.grid.Column"]);
                }

                if (field.display.jsFunc) {
                    Ext.each(field.display.jsFunc, function (method) {
                        this.addGridColumnEvent(val, columns[i - 1], method);
                    }, this);
                }

            });
            
            columns.push({
                header: '&#160;',
                dataIndex: '__',
                editable: false,
                fixed: true,
                hideable: false,
                menuDisabled: true,
                width: 25
            });
            
            var colModel = new Ext.grid.ColumnModel({
                columns: columns
            });

            return colModel;
        },

        /**
         * Create our view based on JSON/XML meta description
         * @protected
         * @return {Ext.grid.GridView}
         */
        getView: function () {
            if (!this.view) {
                var viewConfig = {
                    forceFit: false,
                    groupTextTpl: '{text} ({[values.rs.length]}' + ' {[values.rs.length > 1 ? "Items" : "Item"]})'
                };

                var option = this.getOption("template.option");

                if (option && !Ext.isEmpty(option["Ext.grid.GridView"])) {
                    Ext.apply(viewConfig, "Ext.grid.GridView");
                }

                var ViewClass = Ext.grid.GridView;

                var grouping = this.getOption("template.grouping", {});

                if (grouping.enabled === true) {
                    ViewClass = Ext.grid.GroupingView;

                    if (!Ext.isEmpty(grouping["Ext.grid.GroupingView"])) {
                        Ext.apply(viewConfig, grouping["Ext.grid.GroupingView"]);
                    }
                }

                this.view = new ViewClass(viewConfig);
            }

            return Cronk.grid.MetaGridPanel.superclass.getView.call(this);
        },

        /**
         * Create our bottom tool bar and decide which paging tool bar
         * to add
         * @return {Any}
         * @private
         */
        createBottomBar: function () {
            var pagerOptions = this.getOption("template.pager", {});
            if (pagerOptions.enabled === true) {
                var datasource = this.getOption("template.datasource");
                var PagerClass = Ext.PagingToolbar;
                var pagerConfig = {
                    pageSize: parseInt(AppKit.getPrefVal('org.icinga.grid.pagerMaxItems'), 10),
                    store: this.getStore(),
                    displayInfo: true,
                    displayMsg: _('Displaying topics {0} - {1} of {2}'),
                    emptyMsg: _('No topics to display'),
                    listeners: {
                        change: function() {
                            var sm = this.getSelectionModel();
                            if (sm && typeof sm.syncWithPage === 'function')
                                sm.syncWithPage();
                        },
                        scope: this
                    }
                };

                if (Ext.isEmpty(datasource.countmode) || datasource.countmode === "none") {
                    pagerConfig.displayInfo = false;
                    PagerClass = Cronk.grid.OptimisticPagingToolbar;
                }

                var bar = new PagerClass(pagerConfig);
                return bar;
            }
        },

        /**
         * Build our top tool bar
         * @return {Ext.Toolbar}
         * @private
         */
        buildTopToolbar: function () {

            var autoRefresh = AppKit.getPrefVal('org.icinga.grid.refreshTime') || 300;
            var autoRefreshDefault = AppKit.getPrefVal('org.icinga.autoRefresh') && AppKit.getPrefVal('org.icinga.autoRefresh') !== 'false';

            return new Ext.Toolbar({
                items: [{
                    text: _('Refresh'),
                    iconCls: 'icinga-icon-arrow-refresh',
                    tooltip: _('Refresh the data in the grid'),
                    handler: function (oBtn, e) {
                        this.store.reload();
                    },
                    scope: this
                }, {
                    text: _('Settings'),
                    iconCls: 'icinga-icon-application-edit',
                    toolTip: _('Grid settings'),
                    menu: {
                        items: [{
                            text: String.format(_('Auto refresh ({0} seconds)'), autoRefresh),
                            checked: autoRefreshDefault,
                            checkHandler: function (checkItem, checked) {
                                if (checked === true) {
                                    this.startRefreshTimer();
                                } else {
                                    this.stopRefreshTimer();
                                }
                            },
                            listeners: {
                                render: function (btn) {
                                    if (this.autoRefreshEnabled !== null) {
                                        btn.setChecked(this.autoRefreshEnabled, true);
                                    }
                                    this.on("autorefreshchange", function (v) {
                                        btn.setChecked(v, true);
                                    });
                                },

                                scope: this

                            },
                            scope: this
                        }, {
                            text: _('Get this view as URL'),
                            iconCls: 'icinga-icon-anchor',
                            handler: function (oBtn, e) {
                                var urlParams = this.extractGridParams();
                                var win = null;
                                win = new Ext.Window({
                                    renderTo: Ext.getBody(),
                                    modal: true,
                                    initHidden: false,
                                    width: 500,
                                    autoHeight: true,
                                    padding: 10,
                                    closeable: true,
                                    layout: 'form',
                                    title: _('Link to this view'),
                                    items: {
                                        xtype: 'textfield',
                                        fieldLabel: _('Link'),
                                        width: 350,
                                        value: AppKit.util.Config.getBaseUrl() + "/modules/web/customPortal/" + urlParams
                                    },
                                    buttons: [{
                                        text: _('Close'),
                                        iconCls: 'icinga-icon-close',
                                        handler: function (b, e) {
                                            win.close();
                                        }
                                    }]

                                });
                            },
                            scope: this
                        }, {
                            // Fixes #3432
                            text: _('Reset grid action icons'),
                            iconCls: 'icinga-icon-bin',
                            scope: this,
                            handler: function(button, event) {
                                var actionPanel = this.rowActionPanel.getPanel();
                                actionPanel.removeAllOverrides();
                            }
                        }]
                    }
                }],
                listeners: {
                    render: function (cmp) {
                        if (autoRefreshDefault && this.autoRefreshEnabled === null) {
                            this.startRefreshTimer();
                        }
                    },
                    scope: this
                }
            });
        },

        /**
         * Create a selection model based on JSON/XML meta description
         * and add them to our column model if needed
         * @return {Ext.grid.CheckboxSelectionModel}
         * @private
         */
        createSelectionModel: function () {
            var options = this.getOption("template.option", {});
            if (!Ext.isEmpty(options.selection_model)) {
                if (options.selection_model === "checkbox") {
                    var sm = new Cronk.grid.ObjectSelectionModel({
                        dataIndex: "id"
                    });

                    // We need the checkbox at first
                    this.colModel.columns.splice(0, 0, sm);

                    return sm;
                }
            }
        },

        /**
         * Create filters from JSON/XML meta description. Also enhance the
         * toolbar to append the filter manager
         * @private
         */
        createFilters: function () {
            var filters = [];

            this.fieldIterator(function (item, field) {
                if (field.filter.enabled === true && field.filter.type === 'extjs' && field.filter.subtype) {
                    var filter = field.filter;
                    filter.name = (filter.name ? filter.name : item);
                    filter.id = item;
                    filter.label = (filter.label ? filter.label : field.display.label);
                    filters.push(filter);
                }
            });

            Ext.iterate(this.getOption("template.option.filter", []), function (k,v) {
                if (v.enabled === true && v.type === 'extjs') {
                    var f = v;
                    f.name = (f.name ? f.name : k);
                    f.id = k;
                    f.label = (f.label ? f.label : "NO LABEL");
                    filters.push(f);
                }
            });

            if (filters.length) {

                // Button is extracted from creation because we need it
                // on the filter window to mark active state #3928
                var btn = Ext.create({
                    xtype: 'button',
                    text: _('View filter'),
                    iconCls: 'icinga-icon-pencil',
                    id: this.id+'_viewFilterBtn',
                    handler: function(cmp,state) {
                        Ext.getCmp('west-frame').resetCronkView();
                        // update filter window with current filter
                        if(this.store.baseParams.filter_json && this.store.baseParams.filter_json != 'null')
                            this.filterHdl.updateFromJsonString(this.store.baseParams.filter_json);
                        this.filterHdl.show();
                    },
                    scope: this
                });

                this.filterHdl = new Icinga.Cronks.util.FilterEditorWindow(this, filters, btn);

                this.topToolbar.add(['-', btn]);
                this.topToolbarFilterPos = this.topToolbar.items.length;
            }
        },

        applyDecorators: function() {
            var decorators = this.meta.template.decorators;
            if(!Ext.isArray(decorators))
                return;
            for(var i=0;i<decorators.length;i++) {
                var dec = (new Function("return "+decorators[i]))();

                if(dec) {
                   dec(this);
                } else {
                    AppKit.log("Unknown grid decorator ",decorators[i]);
                }
            }
        },

        /**
         * Collect command definitions and enhance the toolbar to append
         * our command handler
         * @private
         */
        createCommandBar: function () {
            var tbEntry = this.topToolbar.add({
                text: _("Commands"),
                iconCls: 'icinga-icon-server-lightning',
                menu: {
                    items: []
                }
            });

            // An instance to work with
            var cHandler = new Cronk.grid.CommandHandler(this.meta);

            // The entry point to start
            cHandler.setToolbarEntry(tbEntry);

            // We need some selection from a grid panel
            cHandler.setGrid(this);

            // Where we can get some info
            cHandler.setInfoUrl(AppKit.c.path + "/modules/cronks/commandproc/{0}/json/inf");
            cHandler.setSendUrl(AppKit.c.path + "/modules/cronks/commandproc/{0}/json/send");

            // We need something to click on
            cHandler.enhanceToolbar();
        },
        
        createHoverTarget: function() {
            this.rowActionPanel = new Cronk.grid.plugins.RowActionPanel();
            
            this.initPlugin(this.rowActionPanel);
        },

        /**
         * Start refresh task
         */
        startRefreshTimer: function () {
            var autoRefresh = AppKit.getPrefVal('org.icinga.grid.refreshTime') || 300;
            this.stopRefreshTimer();

            this.trefresh = AppKit.getTr().start({
                run: function () {
                    this.refreshGrid();
                },
                interval: (autoRefresh * 1000),
                scope: this
            });
            this.autoRefreshEnabled = true;
            this.fireEvent('autorefreshchange', true);
        },

        /**
         * Stop the refresh timer
         * @param {Boolean} noVisualUpdate
         */
        stopRefreshTimer: function (noVisualUpdate) {
            if (this.trefresh) {
                AppKit.getTr().stop(this.trefresh);
                delete this.trefresh;
            }
            this.autoRefreshEnabled = false;
            if (!noVisualUpdate) {
                this.fireEvent('autorefreshchange', false);
            }

        },

        /**
         * Extract params from grid to use as URL to recreate the view
         * @return {String}
         */
        extractGridParams: function () {

            var store = this.store;
            var cronk = this.ownerCt.CronkPlugin.cmpConfig;
            var urlParams = "cr_base=";

            var jsonFilter = store.baseParams['filter_json'];
            var counter = 0;
            for (var i in store.baseParams) {
                if (i && i != 'filter_json') {
                    var name = i.replace(/(.*?)\[(.*?)\]/g, '$1|$2_' + counter);
                    urlParams += name + "=" + store.baseParams[i] + ";";
                    counter++;
                }
            }

            urlParams += '/';
            if (store.sortInfo) {
                if (store.groupField) {
                    urlParams += "groupDir=" + store.sortInfo.direction + "/";
                    urlParams += "groupField=" + store.sortInfo.field + "/";
                } else {
                    urlParams += "sortDir=" + store.sortInfo.direction + "/";
                    urlParams += "sortField=" + store.sortInfo.field + "/";
                }
            }

            if (Ext.isDefined(cronk.iconCls)) {
                urlParams += "iconCls=" + cronk.iconCls + "/";
            }


            urlParams += "template=" + this.initialConfig.meta.params.template + "/" + "crname=" + cronk.crname + "/" + "title=" + cronk.title + "/";
            if (Ext.isDefined(jsonFilter)) {
                urlParams += "?filter=" + jsonFilter;
            }
            return urlParams;
        },

        /**
         * Connect some events to the grid
         * @private
         */
        initEvents: function () {
            this.store.on('datachanged', function (store) {
                if (store.getCount() === 0) {
                    if (this.getGridEl()) {
                        this.getGridEl().child('div').addClass('x-icinga-nodata');
                    }
                } else {
                    if (this.getGridEl()) {
                        this.getGridEl().child('div').removeClass('x-icinga-nodata');
                    }
                }
            }, this);
            this.store.on("load",function() {
                this.saveState();
            },this)
            this.on("show", function () {
                if (this.autoRefreshEnabled) {
                    this.startRefreshTimer();
                }
            }, this);

            this.on("connectionmodify", function (value) {
                if (this.connectionComboBox && this.connectionComboBox.isVisible()) {
                    this.connectionComboBox.selectByValue(value);
                }
            }, this);
            
            this.on("afterrender", function(grid) {
                this.mask = new Ext.LoadMask(this.getEl(), {
                    store: this.getStore(),
                    msg: _("Loading ...")
                });
            }, this);

            this.on("columnmove", function(grid) {
                this.saveState();
            }, this);
        },

        /**
         * Create a combo box to select the data connections
         * @return {Ext.form.ComboBox}
         */
        createConnectionComboBox: function () {
            var connArr = this.initialConfig.meta.connections;
            for (var i = 0; i < connArr.length; i++) {
                connArr[i] = [connArr[i]];
            }

            this.connectionComboBox = new Ext.form.ComboBox({
                store: new Ext.data.ArrayStore({
                    autoDestroy: true,
                    fields: ['connection'],
                    data: connArr
                }),
                displayField: 'connection',
                typeAhead: true,
                mode: 'local',
                forceSelection: true,
                defaultValue: this.selectedConnection,
                triggerAction: 'all',
                emptyText: this.selectedConnection,
                selectOnFocus: true,
                hidden: (connArr.length < 2) ? true : false,
                width: 135,
                listeners: {
                    afterrender: function (me) {

                    },
                    select: function (me, record) {
                        this.setConnection(record.get("connection"));

                        this.getStore().setBaseParam("connection", this.selectedConnection);
                        this.refreshGrid();
                    },
                    scope: this
                },

                getListParent: function () {
                    return this.el.up('.x-menu');
                },
                iconCls: 'no-icon' //use iconCls if placing within menu to shift to right side of menu
            });

            this.topToolbar.add(["->", this.connectionComboBox]);

            return this.connectionComboBox;
        },


        /**
         * Returns parsable object structure to persist column informations
         * @return {Object}
         */
        getPersistentColumnModel: function () {
            var o = {
                groupField: null,
                columns: []
            };

            if (Ext.isDefined(this.store.groupField)) {
                o.groupField = this.store.getGroupState();
                o.groupDir = this.store.groupDir;
                o.groupOnSort = this.store.groupOnSort;
            }

            Ext.iterate(this.colModel.lookup, function (colId, col) {
                if (Ext.isEmpty(col.dataIndex) === false) {
                    var colData = {};
                    Ext.copyTo(colData, col, ['hidden', 'width', 'dataIndex', 'id', 'sortable']);
                    o.columns.push(colData);
                }
            }, this);

            return o;
        },

        /**
         * Takes structure to reapply column states
         * @param {Object} data
         */
        applyPersistentColumnModel: function (data) {
            var cm = this.colModel;

            if (Ext.isArray(data.columns)) {
                Ext.each(data.columns, function (item, index) {
                    if (Ext.isDefined(item.dataIndex)) {
                        var ci = cm.findColumnIndex(item.dataIndex);
                        if (ci > 0) {
                            var org = cm.getColumnById(ci);
                            if (Ext.isDefined(org)) {

                                if (Ext.isDefined(data.groupField) && data.groupField === org.dataIndex) {
                                    cm.setHidden(org.id, false);
                                } else {
                                    cm.setHidden(org.id, item.hidden);
                                }

                                cm.setColumnWidth(org.id, item.width);
                            }
                        }
                    }
                }, this);
            }

            if (Ext.isDefined(data.groupField) && Ext.isDefined(this.store.groupBy)) {
                this.store.on('beforeload', function () {
                    (function () {

                        var dir = Ext.isEmpty(data.groupDir) ? 'ASC' : data.groupDir;

                        if (Ext.isDefined(data.groupOnSort)) {
                            this.store.groupOnSort = data.groupOnSort;
                        }

                        this.store.groupBy(data.groupField, true, dir);
                        this.store.reload();
                    }).defer(50, this);
                    return false;
                }, this, {
                    single: true
                });
            }
        },

        /**
         * Delayed method to search for what is the best method to 
         * refresh our grid data
         *  @private
         */
        refreshTask: null, // not creating the singleton here

        refreshTaskImpl: function () {
            //NOTE: hidden tabs won't be refreshed
            if (!this.store || this.ownerCt.hidden) {
                return true;
            }
            if (Ext.isFunction((this.getTopToolbar() || {}).doRefresh)) {
                this.getTopToolbar().doRefresh();
            } else if (Ext.isFunction((this.getBottomToolbar() || {}).doRefresh)) {
                this.getBottomToolbar().doRefresh();
            } else if (this.getStore()) {
                this.getStore().reload();
            }
        },

        /**
         * Calls the refreshTask 200ms delayed
         */
        refreshGrid: function () {
            // create the refreshTask for this MetaGridPanel
            if (!this.refreshTask)
                this.refreshTask = new Ext.util.DelayedTask(this.refreshTaskImpl);
            this.refreshTask.delay(200, null, this);
        },

        /**
         * Return component state
         * @return {Object}
         */
        getState: function () {
            var store = this.getStore();
            var aR = null;

            if (this.autoRefreshEnabled === true) {
                aR = 1;
            }

            if (this.autoRefreshEnabled === false) {
                aR = -1;
            }

            var o = {
                filter_params: this.filter_params || {},
                filter_types: this.filter_types || {},
                filter: this.store.baseParams.filter_json,
                nativeState: Ext.grid.GridPanel.prototype.getState.apply(this),
                store_origin_params: ("originParams" in store) ? store.originParams : {},
                sortToggle: store.sortToggle,
                sortInfo: store.sortInfo,
                colModel: this.getPersistentColumnModel(),
                autoRefresh: aR,
                connection: this.store.baseParams.connection
            };

            return o;
        },

        /**
         * Recreate component state from object
         * @param {Object} state
         * @return {Boolean} Operation success marker
         */
        applyState: function (state) {
            if (!Ext.isObject(state)) {
                return false;
            }
            var reload = false;
            var store = this.getStore();
            if (Ext.isObject(state.colModel)) {
                this.applyPersistentColumnModel(state.colModel);
            }
            if (state.filter) {
                this.filterHdl.updateFromJsonString(state.filter);
                this.store.setBaseParam("filter_json",state.filter);
            }

            if (state.filter_types) {
                this.filter_types = state.filter_types;
            }
            if (state.sortToggle) {
                store.sortToggle = state.sortToggle;
            }
            if (state.sortInfo && Ext.isDefined(state.sortInfo.field)) {
                var direction = Ext.isDefined(state.sortInfo.direction) ? state.sortInfo.direction : 'ASC';
                store.sort(state.sortInfo.field, direction);
            }

            if (state.groupOnSort) {
                store.groupOnSort = state.groupOnSort;
            }
            if (state.store_origin_params) {
                store.originParams = state.store_origin_params;
                this.applyParamsToStore(store.originParams, store);
                reload = true;
            }

            if (state.filter_params) {
                this.filter_params = state.filter_params;
                this.applyParamsToStore(this.filter_params, store);
                reload = true;
            }

            if (state.autoRefresh === 1) {
                this.startRefreshTimer();
            } else if (state.autoRefresh === -1) {
                this.stopRefreshTimer();
            }

            if (reload === true) {
                this.refreshGrid();
            }

            if (state.connection) {
                this.setConnection(state.connection);
            }
            if (Ext.isObject(state.nativeState)) {
                return Ext.grid.GridPanel.prototype.applyState.call(this, {
                    columns: state.nativeState.columns
                });
            }
            return true;
        },

        /**
         * Sets the current connection which is used by the grid
         * @param {String} connection
         */
        setConnection: function (connection) {
            this.selectedConnection = connection;
            if (typeof this.connectionComboBox !== "undefined" &&
                this.connectionComboBox.isVisible()) {
                this.connectionComboBox.selectByValue(connection);
            }

            this.getStore().setBaseParam("connection", this.selectedConnection);
            this.fireEvent("connectionmodified");
        },

        /**
         * Add persistent parameters to the data store to be reload safe
         * @param {Object} params
         * @param {Boolean} persist
         */
        applyParamsToStore: function (params, persist) {
            
            persist = persist || false;
            
            for (var i in params) {
                if (i) {
                    if (i === "connection") {
                        this.setConnection(params[i]);
                    }
                    this.store.setBaseParam(i, params[i]);
                    
                    if (persist === true) {
                        this.store.originParams[i] = params[i];
                    }
                }
            }
        },
        
        /**
         * Apply persistent params to store
         * @param {Object} params
         */
        applyPersistentParamsToStore: function(params) {
            this.applyParamsToStore(params, true);
        },
        
        /**
         * @private
         * Get global events and add then to the toolbar
         */
        addGlobalEventsToToolbar: function() {
            var eventPanel = new Cronk.grid.components.JsonActionPanel({
                configurable: false, // No need to customize here
                organizeAs: "button",
                config: this.getGlobalEvents(),
                grid: this
            });
            
            eventPanel.applyToolbarElements(this.getTopToolbar());
        },

        /**
         * Inherited method to create the component. This is a dispatcher
         * to call all the other initialize methods
         */
        initComponent: function () {
            this.addEvents({
                'autorefreshchange': true,
                'connectionmodify': true
            });

            /*
             * Copy some settings back to origin constructor
             * 
             * initial state indicates that this is a custom cronk
             */
            this.stateful = Ext.isDefined(this.initialstate) ? false : true;

            this.autoRefreshEnabled = null;
            this.store = this.createStore();
            this.colModel = this.createColModel();
            this.bbar = this.createBottomBar();
            this.tbar = this.buildTopToolbar();
            this.sm = this.createSelectionModel();

            Cronk.grid.MetaGridPanel.superclass.initComponent.call(this);

            this.createFilters();
            this.createHoverTarget();
            this.addGridGlobalEvents();
            this.initEvents();
            
            var commandOptions = this.getOption("template.option.commands");
            if (commandOptions && commandOptions.enabled && this.enableCommands) {
                this.createCommandBar();
            }
            
            this.addGlobalEventsToToolbar();

            this.createConnectionComboBox();

            if (!Ext.isEmpty(this.parameters.autoRefresh)) {
                this.startRefreshTimer();
            }
            this.applyDecorators();
            if (this.getOption("template.option.mode") === "minimal") {
                this.topToolbar.hide();
            }


        }

    });

    /*
     * Create this class as a XType component to identify later on
     */
    Ext.reg('cronkgrid', Cronk.grid.MetaGridPanel);

})();
