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

Ext.ns("Cronk.grid.columns");

(function () {
    
    "use strict";
    
    /**
     * Column which renders ExtJS components into grid columns
     */
    Cronk.grid.columns.ComponentFrame = Ext.extend(Ext.grid.Column, {
        /**
         * @cfg {Object} componentConfig
         * This is the configuration how to create the sub component into the
         * grid
         */
        componentConfig: {},
        
        /**
         * @cfg {String} defaultXType
         * If conponentConfig is missing of the XType this is set for you
         */
        defaultXType: 'container',
        
        /**
         * @property
         * Status for at least one component is rendered
         * @type Boolean
         */
        rendered: false,
        
        /**
         * @property
         * The grid where we're living on
         * @type Ext.grid.GridPanel
         */
        grid: null,
        
        /**
         * Constructor, sets one unique id
         */
        constructor: function(cfg) {
            this.id = "event-inline-frame";
            Cronk.grid.columns.ComponentFrame.superclass.constructor.call(this, cfg);
            this.components = new Ext.util.MixedCollection();
        },
        
        /**
         * Delegate rendering
         * @param {String} value
         * @param {Object} o
         * @param {Ext.data.Record} record
         * @param {Number} rowIndex
         * @param {Number} colIndex
         * @param {Ext.data.Store} store
         */
        renderer: function (value, o, record, rowIndex, colIndex, store) {
            var id = Ext.id();
            this.createComponent.defer(1, this, [id, record, rowIndex, colIndex, store]);
            return this.createDivTarget(id);
        },
        
        /**
         * Delegator to create the component and render into the grid column
         * @param {String} id
         * @param {Ext.data.Record} record
         * @param {Number} rowIndex
         * @param {Number} colIndex
         * @param {Ext.data.Store} store
         */
        createComponent: function(id, record, rowIndex, colIndex, store) {
            var element = Ext.get(id);
            
            if (element) {
            
                var componentConfig = Ext.apply({
                    renderTo: element,
                    xtype: this.defaultXType,
                    record: record,
                    rowIndex: rowIndex,
                    colIndex: colIndex,
                    store: store
                }, this.componentConfig);
                
                if (componentConfig.items) {
                    var items = [];
                    Ext.each(componentConfig.items, function(item) {
                        var subComponent = this.createItem(item);
                        subComponent.setStore(store);
                        subComponent.setRowIndex(rowIndex);
                        subComponent.setRecord(record);
                        
                        // If we add eventMixing component, test
                        // its conditions if we can display or not
                        //
                        // Because we can not hide a component which is
                        // not finally rendered, test this after rendering
                        if (subComponent.eventMixin) {
                            subComponent.on("afterrender", function() {
                                this.testConditions();
                            }, subComponent);
                        }
                        
                        items.push(subComponent);
                    }, this);
                    componentConfig.items = items;
                }
                
                var component = Ext.create(componentConfig);
                
                component.store = store;
                component.rowIndex = rowIndex;
                component.record = record;
                
                component.render();
                
                if (this.rendered === false) {
                    this.recalculateColumnWidth(component);
                    this.rendered = true;
                }
                
                
                
                this.components.add(component);
            }
        },
        
        /**
         * This is called if the first component was rendered
         * into the grid. The width of all child components
         * are the new width of the column
         * @param {Ext.Container} component
         */
        recalculateColumnWidth: function(component) {
            var cm = this.grid.getColumnModel();
            var ci = cm.getIndexById(this.id);
            
            if (ci) {
                var width = 0;
                component.items.each(function(o) {
                    width += o.getWidth() + 6;
                }, this);
                cm.setColumnWidth(ci, width+4);
            }
        },
        
        /**
         * Return all rendered components
         * @return Ext.util.MixedCollection
         */
        getComponents: function() {
            return this.components;
        },
        
        /**
         * Interceptor for new subitems of one component
         */
        createItem: function(config) {
            return Ext.create(config);
        },
        
        /**
         * Add one new item to all rendered containers in the gris
         * @param {Object} config
         */
        addToAll: function(config) {
            this.components.each(function(component) {
                var subComponent = this.createItem(config);
                subComponent.setStore(component.store);
                subComponent.setRowIndex(component.rowIndex);
                subComponent.setRecord(component.record);
                component.add(subComponent);
                component.doLayout();
            }, this);
        },
        
        /**
         * Remove all components from container and leave
         * the container blank
         */
        removeAllFromAll: function() {
            this.components.each(function(container) {
                container.removeAll(true);
            }, this);
        },
        
        /**
         * Create a simple div target with id as argument
         * @param {String} id
         * @return {String} HTML fragment
         */
        createDivTarget: function(id) {
            return String.format("<div id=\"{0}\"></div>", id);
        }
    });
    
})();