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

Ext.ns("Cronk.grid.plugins");

(function () {

    "use strict";

    /**
     *  Row action plugin for grids, this is a entry point for grids and
     *  a glue for all event components flying arround:
     *  
     *  <ul>
     *  <li>{@link Cronk.grid.columns.ComponentFrame ComponentFrame}</li>
     *  <li>{@link Cronk.grid.components.JsonActionPanel JsonActionPanel}</li>
     *  <li>Cronk.grid.events*</li>
     *  </ul>
     */
    Cronk.grid.plugins.RowActionPanel = Ext.extend(Ext.util.Observable, {
        
        /**
         * @cfg {Boolean} noautoclose
         * Panel is always shown
         */
        noautoclose: true,
        
        /**
         * @cfg {Boolean} nofx
         * Animate expansion of the sub event panel
         */
        nofx: true,
        
        /**
         * @cfg {String} iconCls
         * Icon for sub event panel column
         */
        iconCls: "icinga-action-events",
        
        /**
         * @cfg {Number} delayDefault
         * When to close if focus / mouse is lost
         */
        delayDefault: 800,
        
        /**
         * @cfg {Number} delayOnOpen
         * When only open and no action happens, keep panel
         * open for this time of ms
         */
        delayOnOpen: 2000,
        
        /**
         * @property {Number} lastRowIndex Last selected row index
         * @type Number
         */
        lastRowIndex: null,
        
        /**
         * @property {Ext.grid.GridPanel} component to work on
         * @type Ext.grid.GridPanel
         */
        grid: null,

        /**
         * Constructor, nothing special
         * @param {Object} config
         */
        constructor: function(config) {
            Ext.apply(this, config);

            this.elementCache = {};

            Cronk.grid.plugins.RowActionPanel.superclass.constructor.call(this);
        },
        
        /**
         * Create our JsonActionPanel
         * @return Cronk.grid.components.JsonActionPanel
         */
        createPanel: function() {
            var tb = new Cronk.grid.components.JsonActionPanel({
                config: this.grid.getRowEvents(),
                store: this.grid.getStore(),
                grid: this.grid,
                stateful: true
            });
            
            tb.render(Ext.getBody());
            
            tb.getEl().on("mouseenter", 
                this.resetHideDelay.createDelegate(this));
            tb.getEl().on("mouseleave", 
                this.triggerHideDelay.createDelegate(this, [this.delayDefault]));
            
            tb.getContextMenu().on("mouseover",
                this.resetHideDelay.createDelegate(this));
                
            tb.getContextMenu().on("mouseout",
                this.triggerHideDelay.createDelegate(this, [this.delayDefault]));
            
            return tb;
        },
        
        /**
         * Hook when the grid plugin is initialized
         * @param {Ext.data.GridPanel} grid
         */
        init: function(grid) {
            this.grid = grid;
            this.view = grid.getView();
            
            this.view.enableRowBody = true;
            this.view.getRowClass = this.getRowClass.createDelegate(this);
            
            this.panel = this.createPanel();
            
            this.initOurEvents();
            this.initHideDelay();
        },
        
        /**
         * Init interceptor, register events on our mother component
         */
        initOurEvents: function() {
            this.grid.on("afterrender", function() {
                this.addHoverArea();
            }, this);
        },
        
        /**
         * Add our magic columns to the grid:
         * <ul>
         * <li>Event hover area</li>
         * <li>Inline event area</li>
         * </ul>
         */
        addHoverArea: function() {
            var cm = this.grid.getColumnModel();
            
            var renderer = Cronk.grid.WidgetRenderer.eventIcon({
                iconCls: this.iconCls,
                tooltip: _("Click to expand ..."),
                scope: this,
                listener: {
                    mousedown: this.onMouseDown
                }
            });
            
            // Also show the panel on selection
            this.grid.on("cellclick", function(sm, rowIndex,cellIndex, record) {
                if(cellIndex == 1)
                    this.toggleHandler(rowIndex);
            }, this);
            
            this.grid.getSelectionModel().on("rowdeselect", function(sm, rowIndex, record) {
                this.hidePanel(rowIndex);
            }, this);
            
            //this.grid.on("rowclick", function(grid, rowIndex) {
            //    this.toggleHandler(rowIndex);
            //}, this);
            
            this.grid.getView().on("refresh", function() {
                if (this.lastRowIndex >= 0) {
                    this.toggleHandler(this.lastRowIndex);
                }
            }, this);
            
            var subEventColumn = new Ext.grid.Column({
                id: "event-sub-frame",
                name: "action-panel-hover",
                dataIndex: "id",
                header: String.format("<div class=\"icon-16 {0}\"></div>", this.iconCls),
                fixed: true,
                menuDisabled: true,
                width: 20,
                renderer: renderer
            });
            
            var inlineEventColumn = new Cronk.grid.columns.ComponentFrame({
                grid: this.grid,
                header: String.format("<div class=\"icon-16 {0}\"></div>", this.iconCls),
                dataIndex: "id",
                componentConfig: {
                    items: this.panel.getInlineComponents(),
                    layout: {
                        type: "hbox",
                        defaultMargins: {
                            top: 0,
                            right: 2,
                            bottom: 0,
                            left: 2
                        }
                    },
                    border: false
                }
            });
            
            Ext.iterate(cm.columns, function(col, idx) {
                if (col instanceof Ext.grid.Column) {

                    //  && this.noautoclose === false
                    if (this.panel.hasSubItems() === true) {
                        cm.addColumn(subEventColumn, idx++);
                    } else {
                        cm.addColumn(subEventColumn, idx++);
                        subEventColumn.on("click",function(el,grid,idx,ev) {
                            var menu = this.panel.createContextMenu();
                            AppKit.log(menu);
                            menu.items.removeAt(0);
                            menu.show(ev.target);
                            ev.preventDefault();
                        },this)
                    }
                    
                    if (this.panel.hasInlineComponents() === true) {
                        cm.addColumn(inlineEventColumn, idx++);
                    }
                    
                    this.view.refresh(true);
                    return false;
                }
                return true;
            }, this);
        },
        
        /**
         * Configure our hide delay timemachine
         */
        initHideDelay: function() {
            if (!this.hideDelayTask) {
                this.hideDelayTask = new Ext.util.DelayedTask(function() {
                    this.hidePanel();
                }, this);
            }
        },
        
        /**
         * Stop delayed hiding task e.g. on mouse enter
         */
        resetHideDelay: function() {
            this.hideDelayTask.cancel();
        },
        
        /**
         * Run delayed hiding task e.g. on show or mouseout
         */
        triggerHideDelay: function(delay) {
            if (this.noautoclose === false) {
            
                if (Ext.isEmpty(delay)) {
                    delay = this.delayDefault || 1800;
                }

                this.resetHideDelay();

                this.hideDelayTask.delay(delay);
            }
        },
        
        /**
         * Show or hide/show the event panel
         * @param {Number} rowIndex
         * @param {Boolean} stop marker to prevent deep recursion
         */
        toggleHandler: function(rowIndex, stop) {
            if (this.panel.isVisible() && !stop) {
                this.hidePanel(rowIndex, function(element, panelel) {
                    if (element.id !== panelel.id) {
                        this.toggleHandler(rowIndex, true);
                    }
                });
            } else {
                this.showPanel(rowIndex);
            }
        },
        
        /**
         * Show on panel
         * @param {Number} rowIndex
         * @param {Function} cb Callback fired after operation is complete (FX)
         */
        showPanel: function(rowIndex, cb) {
            var element = this.getTargetElement(rowIndex);
            
            if (this.panel.hasSubItems() === false) {
                return;
            }
            
            if (!element) {
                return;
            }
            
            var height = 55;
            var width = element.getWidth();
            var easeTime = 0.1;
            
            this.lastRowIndex = rowIndex;
            
            element.insertFirst(this.panel.getEl());
            
            this.panel.setRowInformation(rowIndex, this.grid.getStore().getAt(rowIndex));
                
            if (this.nofx===true) {
                this.panel.show();
                this.triggerHideDelay(this.delayOnOpen);
                if (Ext.isFunction(cb)) {
                    cb.call(this, element, this.panel.getEl().parent());
                }
            } else {
                element.scale(width, height, {
                    easing: 'easeOut',
                    duration: easeTime,
                    callback: function() {
                        this.panel.show();
                    },
                    scope: this
                }).syncFx();

                this.panel.getEl().fadeIn({
                    endOpacity: 1,
                    easing: 'easeOut',
                    duration: easeTime,
                    callback: function() {
                        this.panel.show();
                        this.triggerHideDelay(this.delayOnOpen);
                        if (Ext.isFunction(cb)) {
                            cb.call(this, element, this.panel.getEl().parent());
                        }
                    },
                    scope: this
                });
            }
        },
        
        /**
         * Hide the panel
         * @param {Number} rowIndex
         * @param {Function} cb Callback fired after operation is complete (FX)
         */
        hidePanel: function(rowIndex, cb) {
            var element = this.getTargetElement(rowIndex) || this.panel.getEl().parent();
            
            if (this.panel.hasSubItems() === false) {
                return;
            }
            
            if (!element) {
                return;
            }
            
            var height = 55;
            var width = element.getWidth();
            var easeTime = 0.1;
            
            if (this.panel.getContextMenu().isVisible()) {
                this.panel.getContextMenu().hide();
            }
            
            if (this.nofx===true) {
                this.panel.hide();
                if (Ext.isFunction(cb)) {
                    cb.call(this, element, this.panel.getEl().parent());
                }
            } else {
                this.panel.getEl().parent().scale(width, 0, {
                    easing: 'easeOut',
                    duration: easeTime,
                    remove: false
                }).syncFx();

                this.panel.getEl().fadeOut({
                    endOpacity: 0,
                    easing: 'easeOut',
                    duration: easeTime,
                    remove: false,
                    useDisplay: false,
                    callback: function() {
                        this.panel.hide();
                        if (Ext.isFunction(cb)) {
                            cb.call(this, element, this.panel.getEl().parent());
                        }
                    },
                    scope: this
                });
            }
        },
        
        /**
         * On mouse down handler for the event trigger frame
         * @param {Ext.EventObject} event
         * @param {HTMLDivElement} ho
         * @param {Object} lo
         * @param {Ext.data.Record} record
         * @param {Number} rowIndex
         * @param {Number} colIndex
         * @param {Ext.data.Store} store
         */
        onMouseDown: function(event, ho, lo, record, rowIndex, colIndex, store) {
            var sm = this.grid.getSelectionModel();
            if (sm.isSelected(rowIndex)) {
                this.grid.suspendEvents(false);
                sm.deselectRow(rowIndex);
                this.grid.resumeEvents.defer(20, this.grid);
            }
        },
        
        /**
         * Returns a element to render into the grid
         * @param {Number} rowIndex
         * @return {Ext.Element}
         */
        getTargetElement: function(rowIndex) {
            if (!Ext.isEmpty(this.elementCache[rowIndex])) {
                return Ext.get(this.elementCache[rowIndex]);
            }
        },
        
        /**
         * Adds a target element for our JsonActionPanel to the grid row
         * @param {Ext.data.Record} record
         * @param {Number} rowIndex
         * @param {Object} rowParams
         * @param {Ext.data.Store} store
         */
        getRowClass: function(record, rowIndex, rowParams, store) {
            var id=Ext.id(null, "action-component-");
            this.elementCache[rowIndex] = id;
            rowParams.body = String.format('<div id="{0}"></div>', id);
        }   
    });
    
})();