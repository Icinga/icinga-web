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

Ext.ns("Cronk.grid.events");

(function () {
    
    "use strict";
    
    /**
     * This class can be used to apply the handling of grid event buttons and 
     * more to different classes:
     * 
     * <code></pre>
        My.Class = Ext.extend(Ext.Button, {..});
        Ext.override(My.Class, Cronk.grid.events.EventMixin);
     * </pre></code>
     * 
     * @class
     */
    Cronk.grid.events.EventMixin = {
        
        /**
         * Identifier if a component use this EventMixin
         * @type {Boolean}
         */
        eventMixin: true,
        
        /**
         * Record of the current row
         * @type {Ext.data.Record}
         */
        record: null,
        
        /**
         * Row index of the current row
         * @type {Number}
         */
        rowIndex: null,
        
        /**
         * Store of the grid
         * @type {Ext.data.Store}
         */
        store: null,
        
        /**
         * Handler to call if event triggered
         * @type Function
         */
        handler: null,
        
        /**
         * Array of events the handler is bind to
         * @type Array
         */
        handlerEvents: [],
        
        /**
         * Target to bind the events
         * @type Ext.util.Observable
         */
        handlerTarget: null,
        
        /**
         * A model containing information for this event component
         * @type Ext.data.Store
         */
        model: null,
        
        /**
         * Reference to the grid
         * @type Ext.grid.GridPanel
         */
        grid: null,
        
        /**
         * Configuration of the handler object for special params
         * @type Object
         */
        handlerArguments: {},
        
        /**
         * Caching templates for reuse
         */
        templateCache: {},
        
        /**
         * @cfg {Object} conditions
         * A object of condition functions
         */
        conditions: null,
        
        /**
         * @private
         * @property {Object} conditionEvents Collection of events but
         * treated special to test show/hide conditions
         */
        conditionEvents: {
            showcondition: true,
            hidecondition: false,
            activationcondition: true
        },
        
        /**
         * @private
         * @property {Object} internalConditionMethods Internal cache of tests
         */
        internalConditionMethods: {},
        
        /**
         * Setter for handler arguments. This method appends to the args
         * and allows overwriting, but never starts with an empty hash
         * @param {Object} config
         */
        setHandlerArgs: function(config) {
            Ext.apply(this.handlerArguments, config);
        },
        
        /**
         * Getter for handler args
         * @return {Object}
         */
        getHandlerArgs: function() {
            return this.handlerArguments;
        },
        
        /**
         * Return a single configured handler argument from config
         * @param {String} item
         * @param {Any} def Defautl value if item not found
         * @return {Any}
         */
        getHandlerArg: function(item, def) {
            if (Ext.isDefined(this.handlerArguments[item])) {
                return this.handlerArguments[item];
            }
            
            return def || null;
        },
        
        /**
         * Return handler argument substituted by Ext.XTemplate and
         * the current record data
         * @param {String} item
         * @param {Any} def
         * @return {String}
         */
        getHandlerArgTemplated: function(item, def) {
            
            // This possible for global events where no record
            // is configured
            if (!(this.getRecord() instanceof Ext.data.Record)) {
                return this.getHandlerArg(item, def);
            }
            
            var data = this.getHandlerArg(item, def);
            
            // Test if object or array was configured
            if (Ext.isPrimitive(data) === false) {
                return data;
            }
            
            if (Ext.isEmpty(this.templateCache[item])) {
                this.templateCache[item] =
                    new Ext.XTemplate(data);
                
                this.templateCache[item].compile();
            }
            
            return this.templateCache[item].apply(this.getRecord().data);
        },
        
        /**
         * Return all configured handler args templated by the
         * current record
         * @return {Object}
         */
        getHandlerArgsTemplated: function() {
            var out = {};
            Ext.iterate(this.getHandlerArgs(), function(key, val) {
                out[key] = this.getHandlerArgTemplated(key);
            }, this);
            return out;
        },
        
        /**
         * Setter for model
         * @param {Ext.data.Store} model
         */
        setModel: function(model) {
            this.model = model;
        },
        
        /**
         * Getter for model
         * @return Ext.data.Store
         */
        getModel: function() {
            return this.model;
        },
        
        /**
         * Setter for grid
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
         * Setter for handler
         * @param {Object} handler
         */
        setHandler: function(handler) {
            this.handler = handler;
        },
        
        /**
         * Getter for handler
         * @return Object
         */
        getHandler: function() {
            return this.handler;
        },
        
        
        /**
         * Setter for handlerEvents
         * @param {Array} events
         */
        setHandlerEvents: function(events) {
            this.handlerEvents = events;
        },
        
        /**
         * Getter for handlerEvents
         * @return Array
         */
        getHandlerEvents: function() {
            return this.handlerEvents;
        },
        
        /**
         * Setter for handlerTarget
         * @param {Function} target
         */
        setHandlerTarget: function(target) {
            this.handlerTarget = target;
        },
        
        /**
         * Getter for handlerTarget. If the handlerTarget is a callback
         * the return value of the callback is returned
         * @return Ext.util.Observable
         */
        getHandlerTarget: function() {
            var handlerTarget = this.handlerTarget ? this.handlerTarget : this;
            
            if (Ext.isFunction(this.handlerTarget)) {
                handlerTarget = this.handlerTarget.call(this);
            }
            
            return handlerTarget;
        },
        
        /**
         * @private
         * This method really register the events to the handlerTarget
         */
        registerHandler: function() {
            
            // Remove all existing listeners to avoid errors while
            // component tries default handling
            this.getHandlerTarget().purgeListeners();
            
            var handlerConfiguration = {};
            
            if (Ext.isArray(this.handlerEvents) && Ext.isFunction(this.handler)) {
                Ext.iterate(this.handlerEvents, function(event) {
                    handlerConfiguration[event] = this.handler;
                });
            } else if (Ext.isObject(this.handler)) {
                Ext.iterate(this.handler, function(event, fn) {
                    if (Ext.isFunction(fn)) {
                        handlerConfiguration[event] = fn;
                    }
                });
            }
            
            Ext.iterate(handlerConfiguration, function(event, fn) {
                this.getHandlerTarget()
                    .on(event, this.createEventDelefationCallback(fn), this);
            }, this);
            
            // Register dummy function if the element needs
            // on to work properly
            this.handler = Ext.emptyFn;
        },
        
        createEventDelefationCallback: function(fn) {
            return fn.createDelegate(this);
        },
        
        /**
         * Configuration method of the consuming component. All events are bount
         * after rendering time
         * @param {Function} handlerTarget call back for getting the element
         * where event should be bound to
         */
        initEventMixin: function(handlerTarget) {
            this.setHandlerTarget(handlerTarget);
            
            this.on("afterrender", this.registerHandler, this);
            
            // Works only after component is rendered. Chaining
            // problem
            this.on("afterrender", this.registerConditions, this);
            
            this.templateCache = {};
        },
        
        /**
         * Add our concrete testing functions to cache and
         * checks if a conditions config was submitted, add these
         * functions to the listeners
         */
        registerConditions: function() {
            this.addEvents(this.conditionEvents);
            this.internalConditionMethods = {};
            
            if (this.conditions) {
                Ext.iterate(this.conditions, function(c) {
                    var name = c.condition + "condition";
                    var internalName = "test" + Ext.util.Format.capitalize(name);
                    
                    var fn = Ext.decode(c.fn);
                    
                    if (Ext.isFunction(this[internalName])) {
                        this.internalConditionMethods[name] = this[internalName];
                    }
                    
                    if (Ext.isFunction(fn)) {
                        this.on(name, fn, this);
                    }
                }, this);
            }
        },
        
        /**
         * Test all conditions configured for this object (fire *condition
         * events and call test result functions for processing)
         */
        testConditions: function() {
            Ext.iterate(this.internalConditionMethods, function(eventName, runFn) {
                runFn.call(this, this.fireEvent(eventName));
            }, this);
        },
        
        /**
         * Condition result method, show or hide the component based
         * on showcondition event
         * @param {Boolean} val
         */
        testShowcondition: function(val) {
            if (val === true) {
                if (this.isVisible() === false) {
                    this.show();
                }
            } else {
                if (this.isVisible() === true) {
                    this.hide();
                }
            }
        },
        
        /**
         * Condition result method, tests against the result
         * if we should hide the component
         * @param {Boolean} val
         */
        testHidecondition: function(val) {
            this.testShowcondition(!val);
        },
        
        /**
         * Test condition results against activation
         * @param {Boolean} val
         */
        testActivationcondition: function(val) {
            this.setDisabled(!val);
        },
        
        /**
         * Setter for store
         * @param {Ext.data.Store} record
         */
        setRecord: function(record) {
            this.record = record;
        },
        
        /**
         * Getter for store
         * @return {Ext.data.Store}
         */
        getRecord: function() {
            return this.record;
        },
        
        /**
         * Setter for row index
         * @param {Number} rowIndex
         */
        setRowIndex: function(rowIndex) {
            this.rowIndex = rowIndex;
        },
        
        /**
         * Getter for row index
         * @return {Number}
         */
        getRowIndex: function() {
            return this.rowIndex;
        },
        
        /**
         * Setter for store
         * @param {Ext.data.Store} store
         */
        setStore: function(store) {
            this.store = store;
        },
        
        /**
         * Getter for store
         * @return {Ext.data.Store}
         */
        getStore: function() {
            return this.store;
        },
        
        /**
         * Event hook. This method is called then the "rowselect" comes
         * from the JsonActionPanel
         * @param {Number} rowIndex
         * @param {Ext.data.Record} record
         * @param {Ext.data.Store} store
         */
        onRowSelect: function(rowIndex, record, store) {
            this.setRowIndex(rowIndex);
            this.setRecord(record);
            
            // This is done on object creates, store does not
            // change under default conditions
            // this.setStore(store);
        },
        
        /**
         * Return a object identified (mix of menuids registers on
         * the corresponding button group and item itsef)
         * @return String
         */
        getObjectIdentifier: function() {
            var parent = this.findParentByType("buttongroup", false);
            if (parent) {
                var parts = parent.menuid + "_" + this.menuid;
                return parts.replace(/\s+/g, "_").toLowerCase();
            }
        }
    };
})();
