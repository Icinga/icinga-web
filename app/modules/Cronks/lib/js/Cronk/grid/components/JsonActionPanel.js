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

Ext.ns("Cronk.grid.components");

(function () {

    "use strict";
    
    /**
     * Panel which controls grid actions display on our toolbar
     */
    Cronk.grid.components.JsonActionPanel = Ext.extend(Ext.Panel, {
        
        /**
         * @cfg {Boolean} border
         * Yes / no for borders
         */
        border: false,
        
        /**
         * @cfg {Boolean} hidden
         * Default hidden rendering
         */
        hidden: true,
        
        /**
         * @property
         * @type Array
         * Items which configures for us, bis displayed in the grid itself
         */
        inlineItems: [],
        
        /**
         * @property
         * @type Number
         * Counter how much items we hold on out toolbar
         */
        subItems: 0,
        
        /**
         * @property
         * @type Number
         * Current index of selected row
         */
        currentRowIndex: null,
        
        /**
         * @property
         * @type Ext.data.Record
         * Current record, representing the selected row
         */
        currentRecord: null,
        
        /**
         * @property
         * @type String
         * State id, because this component is stateful, systemwide
         */
        stateId: null,
        
        /**
         * @property
         * @type Object
         * State information. If someone changes event settings in a grid
         * this is the place where information resides
         */
        overrides: {},
        
        /**
         * Constructor
         * @param {Object} config Panel configuration
         */
        constructor: function(config) {
            
            this.hidden = true;
            
            this.stateId = "grid-event-action-configuration";
            
            this.inlineItems = [];
            
            /**
             * @event rowselect
             * Fires when a row was select and the panel is expanded
             * @param {Number} row index number
             * @param {Ext.data.Record} current record
             * @param {Ext.data.Store} current store
             */
            this.addEvents("rowselect");
            
            Cronk.grid.components.JsonActionPanel
                .superclass.constructor.call(this, config);
        },
        
        /**
         * Make the component ready
         */
        initComponent: function() {
            
            this.loadState();
            
            this.setConfig(this.config, true);
            
            Cronk.grid.components.JsonActionPanel
                .superclass.initComponent.call(this);
            
            this.contextMenu = this.createContextMenu();
            
        },
        
        /**
         * Save the persistens to database
         * @param {Boolean} reload Reloads the whole cronk (parent component) 
         * if needed
         */
        persistState: function(reload) {
            var state = {
                overrides: this.overrides
            };
            
            if (reload) {
                Ext.state.Manager.getProvider().on("statechange", function() {
                    this.getGrid().reloadCronk();
                }, this, {single:true});
            }
            
            Ext.state.Manager.set(this.stateId, state);
        },
        
        /**
         * Load the initial state from state manager and fill
         * our local vars
         */
        loadState: function() {
            var state = Ext.state.Manager.get(this.stateId);
            
            if (state && state.overrides) {
                this.overrides = state.overrides;
            } else {
                this.overrides = {};
            }
        },
        
        /**
         * Add override setting
         * @param {String} componentid Component ID see 
         * {@link Cronk.grid.events.EventMixin#getObjectIdentifier EventMixin}
         * for more information
         * @param {Object} override Object of new configuration
         */
        addObjectOverride: function(componentid, override) {
            
            var gridid = this.getGridIdentifier();
            
            if (!this.overrides[gridid]) {
                this.overrides[gridid] = {};
            }
            
            if (!this.overrides[gridid][componentid]) {
                this.overrides[gridid][componentid] = {};
            }
            
            Ext.apply(this.overrides[gridid][componentid], override);
            
            this.persistState(true);
        },
        
        /**
         * Remove override setting for component
         * @param {String} componentid
         */
        removeObjectOverride: function(componentid) {
            var gridid = this.getGridIdentifier();
            if (this.overrides[gridid]) {
                if (this.overrides[gridid][componentid]) {
                    this.overrides[gridid][componentid] = {};
                } else {
                    delete this.overrides[gridid];
                }
            }
            
            this.persistState(true);
        },
        
        /**
         * Remove all override settings from a grid
         * @param {String} gridid This is the xml template name of grid
         */
        removeAllOverrides: function(gridid) {
            
            if (gridid) {
                if (this.overrides[gridid]) {
                    delete this.overrides[gridid];
                }
            } else {
                this.overrides = {};
            }
            
            
            this.persistState(true);
        },
        
        /**
         * Return override settings for a object identified
         * @param {String} id
         */
        getOverride: function(id) {
            var gridid = this.getGridIdentifier();
            
            if (gridid && this.overrides[gridid]) {
                if (this.overrides[gridid][id]) {
                    return this.overrides[gridid][id];
                }
            }
            
            return null;
        },
        
        /**
         * Setter for grid component working on
         * @param {Ext.data.GridPanel} grid
         */
        setGrid: function(grid) {
            this.grid = grid;
        },
        
        /**
         * Getter for grid
         * @return Ext.grid.GridPanel
         */
        getGrid: function() {
            return this.grid;
        },
        
        /**
         * Return the grid identified from our grid object
         * @return String
         */
        getGridIdentifier: function() {
            if (this.getGrid().isXType("cronkgrid")) {
                return this.getGrid().getTemplate();
            }
            
            return null;
        },
        
        /**
         * Initialize method to create a context menu
         * @return Ext.menu.Menu
         */
        createContextMenu: function() {
            var ctx = new Ext.menu.Menu({
                items: [{
                    text: _("Move into grid"),
                    iconCls: "icinga-icon-dock-90",
                    handler: function(button, event) {
                        this.moveComponentIntoGrid(this.getContextItem());
                    },
                    scope: this
                }, {
                    text: _("Reset this grid"),
                    iconCls: "icinga-icon-bin",
                    handler: function(button, event) {
                        this.removeAllOverrides(this.getGridIdentifier());
                    },
                    scope: this
                }]
            });
            
            return ctx;
        },
        
        /**
         * Find a component by its object identifier see
         * {@link Cronk.grid.events.EventMixin#getObjectIdentifier EventMixin}
         * for more information
         * @return Ext.BoxComponent
         */
        findByObjectIdentifier: function(oi) {
            var component = this.getTopToolbar().findBy(function(o) {
                if (Ext.isFunction(o.getObjectIdentifier)) {
                    if (o.getObjectIdentifier() === oi) {
                        return true;
                    }
                }
            }, this);
            
            if (Ext.isArray(component)) {
                return component.shift();
            }
        },
        
        /**
         * Writes override configuration for a event component to be displayed
         * inline
         * @param {Ext.BoxComponent} component
         */
        moveComponentIntoGrid: function(component) {
            var oi = component.getObjectIdentifier();
            
            this.addObjectOverride(oi, {
                target: "inline"
            });
        },
        
        /**
         * Getter for contextmenu
         * @return Ext.menu.Menu
         */
        getContextMenu: function() {
            return this.contextMenu;
        },
        
        /**
         * Returns the current underlaying component from which the 
         * contextmenu was started
         * @return Ext.BoxComponent
         */
        getContextItem: function() {
            return this.contextitem;
        },
        
        /**
         * Setter for contextmenu item
         * @param {Ext.BoxComponent} item
         */
        setContextItem: function(item) {
            this.contextitem = item;
        },
        
        /**
         * Install our contextmenu on a component and handle the
         * contextmenu event
         * @param {Ext.BoxComponent} object
         */
        installContextMenuEvent: function(object) {
            object.on("afterrender", function() {
                object.getEl().on("contextmenu", function(event) {
                    event.preventDefault();
                    this.setContextItem(object);
                    this.contextMenu.show(object.getEl());
                }, this);
            }, this, {single:true});
        },
        
        /**
         * Aggregated setter for current row information, also fires the
         * row select event to connected listeners
         * @param {Number} rowIndex
         * @param {Ext.data.Record} record
         */
        setRowInformation: function(rowIndex, record) {
            this.setCurrentRecord(record);
            this.setCurrentRowIndex(rowIndex);
            
            this.fireEvent("rowselect", this.currentRowIndex, this.currentRecord, this.getStore());
        },
        
        /**
         * Setter for the current record
         * @param {Ext.data.Record} record
         */
        setCurrentRecord: function(record) {
            this.currentRecord = record;
        },
        
        /**
         * Setter for the current rowIndex
         * @param {Number} rowIndex
         */
        setCurrentRowIndex: function(rowIndex) {
            this.currentRowIndex = rowIndex;
        },
        
        /**
         * Setter for the store object (This is done only once at initializing
         * @param {Ext.data.Store} store
         */
        setStore: function(store) {
            this.store = store;
        },
        
        /**
         * Getter for store
         * @return Ext.data.Store
         */
        getStore: function() {
            return this.store;
        },
        
        /**
         * This method rewrites event configuration from sub item to inline
         * item (Removes text
         */
        modifyInlineItem: function(localItem, override) {
            Ext.apply(localItem, override);

            // Remove the text if we put this into the gris
            // Because we doesn't have enough space for that!
            if (override.target === "inline" && localItem.text && localItem.iconCls) {
                if (!localItem.tooltip) {
                    localItem.tooltip = localItem.text;
                }

                delete localItem.text;
            }
            
            return localItem;
        },
        
        /**
         * Drops items from our panel and readd all items
         */
        resetConfig: function() {
            this.getTopToolbar().removeAll(true);
            this.setConfig(this.config, false);
        },
        
        /**
         * Build our panel items from object configuration
         * @param {Object} config
         * @param {Boolean} initial first start or changing/readding
         */
        setConfig: function(config, initial) {
            
            var tbar = null;
            
            if (initial===true) {
                this.tbar = new Ext.Toolbar();
                tbar = this.tbar;
            } else {
                tbar = this.getTopToolbar();
            }
            
            this.inlineItems = [];
            
            Ext.each(config, function(group) {
                var items = [];
                Ext.iterate(group.items, function(item) {
                    
                    var localItem = Ext.apply({}, item);
                    
                    var id = String(group.menuid + "_" + localItem.menuid).replace(/\s+/g, "_").toLowerCase();
                    
                    var override = this.getOverride(id);
                    if (override) {
                        localItem = this.modifyInlineItem(localItem, override);
                    }
                    
                    if (localItem.target === "sub") {
                        var component = Ext.create(localItem);
                        component.setStore(this.getStore());
                        this.on("rowselect", component.onRowSelect, component);
                        this.installContextMenuEvent(component);
                        this.subItems++;
                        items.push(component);
                    } else if (localItem.target === "inline") {
                        // Need configuration only here, because of 
                        // creating multiple elements
                        this.inlineItems.push(localItem);
                    }
                    
                }, this);
                
                if (items.length) {
                    
                    // Copy, because we're loosing our objects after
                    // deleting items
                    var groupConfig = Ext.apply({
                        bodyStyle: "padding: 0 8px 0 0",
                        columns: items.length
                    }, group);
                    
                    delete groupConfig.items;
                    
                    var componentGroup = this.createButtonGroup(groupConfig);
                    
                    componentGroup.add(items);
                    
                    tbar.add(componentGroup);
                }
                
            }, this);
            
            this.doLayout();
        },
        
        /**
         * Interceptor if a button group wants to be created.
         * 
         * Appends event of no event is left (overrides), buttongroup
         * is autodestroyed
         * 
         * @param {Object} config
         */
        createButtonGroup: function(config) {
            var buttonGroup = new Ext.ButtonGroup(config);
            
            buttonGroup.on("remove", function(group) {
                if (group.items.getCount() <= 0) {
                    group.destroy();
                } else {
                    this.doLayout();
                }
            }, this);
            
            return buttonGroup;
        },
        
        /**
         * Return all inline components (events we can't handle, because
         * we need that in the grid panel)
         * @return Array
         */
        getInlineComponents: function() {
            return this.inlineItems;
        },
        
        /**
         * Check method if inline items available
         * @return Boolean
         */
        hasInlineComponents: function() {
            return (this.inlineItems.length > 0) ? true : false;
        },
        
        /**
         * Checker if sub items registered
         * @return Boolean
         */
        hasSubItems: function() {
            return (this.subItems > 0) ? true : false;
        }
    });
    
})();