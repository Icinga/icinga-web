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

/*global Ext: false, Icinga: false, _: false, AppKit: false */
Ext.ns("AppKit.Admin.Components");

(function () {
    "use strict";
    
    AppKit.Admin.Components.ObjectRestrictionRecord = Ext.data.Record.create([{
        name: 'id'
    }, {
        name: 'value'
    }, {
        name: 'target'
    }]);
    
    /**
     * Class for our restriction window
     */
    AppKit.Admin.Components.ObjectRestrictionEditWindow = Ext.extend(Ext.Window, {
        
        width: 420,
        height: 150,
        layout: "fit",
        
        /**
         * 
         * @param {Object} config Ext.Window configuration options
         */
        constructor: function(config) {
            
            this.addEvents({
                beforecommit: true,
                commit: true
            });
            
            this.closeAction = 'hide';
            this.hidden = true;
            this.resizable = false;
            this.closable = false;
            
            AppKit.Admin.Components.ObjectRestrictionEditWindow
                .superclass.constructor.call(this, config);
            
            this.on("commit", function() {
                this.hide();
            }, this);
        },
        
        /**
         * @protected
         */
        initComponent: function() {
            
            this.initBottomBar();
            
            AppKit.Admin.Components.ObjectRestrictionEditWindow
                .superclass.initComponent.call(this);
            
            this.initLayout();
            
            this.doLayout();
        },
        
        /**
         * Add components to our window
         * @private
         */
        initLayout: function() {
            this.formPanel = Ext.create({
                xtype: "form",
                border: false,
                padding: "5px", 
                items: [{
                    xtype: "fieldset",
                    title: _("Values"),
                    columnWidth: 0.5,
                    padding: "2px",
                    items: [{
                        xtype: "textfield",
                        name: "value",
                        allowBlank: false,
                        width: 250,
                        fieldLabel: _("Match string")
                    }]
                }]
            });
            
            this.add(this.formPanel);
        },
        
        /**
         * Init our bottom toolbar
         * @private
         */
        initBottomBar: function() {
            this.bbar = ['->', {
                text: _("Add restriction"),
                iconCls: "icinga-icon-add",
                handler: function(button, event) {
                    this.commit();
                },
                scope: this
            }, {
                text: _("Cancel"),
                iconCls: "icinga-icon-cancel",
                handler: function(button, event) {
                    this.hide();
                },
                scope: this
            }, {
                text: _("Help"),
                iconCls: "icinga-action-icon-help",
                handler: function(button, event) {
                    var msg = _("HELP_MATCH_STRING");
                    
                    if (msg === "HELP_MATCH_STRING") {
                        msg = "The match string is a expression of a SQL LIKE <br />" +
                        "operation. You can use all valid wildcard characters:<br />"+
                        "'%' and '_'.<br /><br />"+
                        "'%' - Matches any number of characters, even zero characters<br />"+
                        "'_' - Matches exactly one character<br /><br />"+
                        "The expression 'db-%' will match e.g. 'db-mysql01' and so on.";
                    }
                    
                    Ext.Msg.show({
                        animEl: event.getTarget(),
                        icon: Ext.MessageBox.INFO,
                        modal: true,
                        buttons:  Ext.MessageBox.OK,
                        title: _("Match value"),
                        msg: msg
                    });
                },
                scope: this
            }];
        },
        
        reset: function() {
            var form = this.formPanel.getForm();
            form.items.each(function(item) {
                item.setValue("");
            });
        },
        
        commit: function() {
            var form = this.formPanel.getForm();
            
            if (form.isValid() !== true) {
                return;
            }
            
            var values = form.getValues();
            
            if (this.fireEvent("beforecommit", values, form, this) === true) {
                this.fireEvent("commit", values, form, this);
            }
        }
        
    });
    
    /**
     * Class for single object restrictions
     */
    AppKit.Admin.Components.ObjectRestrictionView = Ext.extend(Ext.Panel, {
        /*
         * @cfg {String} type
         * Context to use: role or user
         */
        type: null,
        
        /*
         * @cfg {String} target
         * Principals for which object, e.g. host or service
         */
        target: null,
        
        /*
         * @cfg {Ext.data.Store} Which to store to work on
         */
        store: null,
        
        layout: "fit",
        
        /*
         * @private
         * @type Ext.Window
         * @property restrictionWindow
         */
        restrictionWindow: null,
        
        constructor: function(config) {
            AppKit.Admin.Components.ObjectRestrictionView
                .superclass.constructor.call(this, config);
        },
        
        initComponent: function() {
            
            this.tbar = this.initToolbar();
            
            AppKit.Admin.Components.ObjectRestrictionView
                .superclass.initComponent.call(this);
            
            this.setTitle(Ext.util.Format.capitalize(this.getTarget()));
            this.setIconClass("icinga-icon-" + this.getTarget());
            this.initLayout();
            this.initRestrictionWindow();
            this.doLayout();
        },
        
        initLayout: function() {
            
            this.columnModel = new Ext.grid.ColumnModel([{
                header: this.getTarget() + " string",
                dataIndex: "value"
            }]);
            
            this.grid = Ext.create({
                xtype: "grid",
                store: this.getStore(),
                colModel: this.columnModel,
                viewConfig: {
                    forceFit: true
                }
            });
            
            this.add(this.grid);
        },
        
        initRestrictionWindow: function() {
            if (this.restrictionWindow === null) {
                this.restrictionWindow =
                new AppKit.Admin.Components.ObjectRestrictionEditWindow({
                    title: String.format(_("Add {0} restriction"), this.getTarget())
                });
                
                this.restrictionWindow.render(Ext.getBody());
                
                /**
                 * When we commiting changes from our window,
                 * add these to the grid
                 */
                this.restrictionWindow.on("commit", function(values, form, wnd) {
                    var record = new AppKit.Admin.Components
                        .ObjectRestrictionRecord(Ext.apply(values, {
                            target: this.getTarget()
                     }));
                    
                    this.getStore().add(record);
                }, this);
            }
            
            return this.restrictionWindow;
        },
        
        /**
         * @private
         */
        initToolbar: function() {
            return {
                xtype: "toolbar",
                items: [{
                    text: _('Add restriction'),
                    iconCls: 'icinga-icon-add',
                    handler: function (button, event) {
                        this.restrictionWindow.alignTo(event.getTarget(), 'tr?');
                        this.restrictionWindow.reset();
                        this.restrictionWindow.show();
                    },
                    scope: this
                }, {
                    text: _('Remove selected'),
                    iconCls: 'icinga-icon-cancel',
                    handler: function (button, event) {
                        var sm = this.getGrid().getSelectionModel();
                        var records = sm.getSelections();
                        Ext.iterate(records, function(record) {
                            this.getStore().remove(record);
                        }, this);
                    },
                    scope: this
                }]
            };
        },
        
        /**
         * Return the Target
         * @returns {String}
         */
        getTarget: function() {
            return this.target;
        },
        
        /**
         * Which type
         * @returns {String}
         */
        getType: function() {
            return this.type;
        },
        
        /**
         * Our corresponding store
         * @returns {Ext.data.Store}
         */
        getStore: function() {
            return this.store;
        },
        
        /**
         * Gridtable of this component
         * @returns {Ext.grid.GridPanel}
         */
        getGrid: function() {
            return this.grid;
        },
        
        selectValues: function(principals) {
            this.getStore().removeAll();
            
            var ctarget = "Icinga" + 
            Ext.util.Format.capitalize(this.getTarget());
            
            Ext.iterate(principals, function (p) {
                if (p.target.target_name === ctarget) {
                    
                    var record = new
                    AppKit.Admin.Components.ObjectRestrictionRecord({
                        target: this.getTarget()
                    });
                    
                    Ext.iterate(p.values, function(value) {
                        record.set(value.tv_key, value.tv_val);
                    }, this);
                    
                    this.getStore().add(record);
                }
            }, this);
        }
        
    });
    
})();