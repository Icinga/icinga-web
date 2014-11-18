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

Ext.ns('Icinga.Cronks.util');

(function() {
    
    "use strict";
    
    Icinga.Cronks.util.CategoryEditor = Ext.extend(Ext.Window, {
        title: _('Category editor'),

        closeAction: 'hide',
        height: 400,
        resizable: false,
        layout: 'fit',

        tools: [{
            id: 'help',
            handler: Ext.emptyFn,
            qtip: {
                title: _('Category editor'),
                text: _('To edit double click into the field you want to edit. Note: System categories and catids are not editable!')
            }
        }],

        constructor: function (cfg) {
            Icinga.Cronks.util.CategoryEditor.superclass.constructor.call(this, cfg);
        },

        initComponent: function () {

            Icinga.Cronks.util.CategoryEditor.superclass.initComponent.call(this);

            this.grid = this.buildGrid();

            this.add(this.grid);

            this.on('beforeshow', function (w) {
                w.grid.getStore().reload();
            }, this);
            
            // Category permission window
            this.permissionWindow = 
                new Icinga.Cronks.util.CategoryPermissionWindow();
            
            this.permissionWindow.on('load', function() {
                this.permissionWindow.show();
            }, this);
            
            this.permissionWindow.on('save', function() {
                this.grid.getStore().reload();
            }, this);
        },

        buildGrid: function () {

            var systemRenderer = function (value, o, record, rowIndex, colIndex, store) {
                    if (value === true) {
                        o.css = 'icinga-icon-shield';
                    } else {
                        o.css = 'icinga-icon-user';
                    }
                    return '';
                };

            var writer = new Ext.data.JsonWriter({
                encode: true,
                encodeDelete: true,
                writeAllFields: true
            });

            var editor = new Ext.form.TextField();

            var intEditor = new Ext.form.TextField({
                maskRe: new RegExp(/[0-9]+/)
            });

            var booleanEditor = new Ext.form.ComboBox({
                typeAhead: true,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'local',
                store: new Ext.data.ArrayStore({
                    id: 0,
                    fields: ['value', 'label'],
                    data: [
                        [false, 'false'],
                        [true, 'true']
                    ]
                }),
                valueField: 'value',
                displayField: 'label'
            });

            var grid = new(Ext.extend(Ext.grid.EditorGridPanel, {
                width: 580,
                height: 400,

                selModel: new Ext.grid.RowSelectionModel({
                    singleSelect: true
                }),
                buttons: [{
                    text: _('OK'),
                    iconCls: 'icinga-icon-accept',
                    handler: function (b, e) {
                        this.grid.store.save();
                        this.hide();
                    },
                    scope: this
                },{
                    text: _('Cancel'),
                    iconCls: 'icinga-action-icon-cancel',
                    handler: function (b, e) {
                        this.grid.store.rejectChanges();
                        this.hide();
                    },
                    scope: this
                }],
                store: new Ext.data.JsonStore({
                    url: AppKit.c.path + '/modules/cronks/provider/cronks/categories',
                    writer: writer,
                    autoLoad: false,
                    autoSave: true,
                    paramsAsHash: true,
                    baseParams: {
                        all: 1,
                        invisible: 1
                    },
                    listeners: {
                        write: function (store, action, result, transaction, record) {
                            store.reload();
                        }
                    }
                }),

                colModel: new Ext.grid.ColumnModel({
                    defaults: {
                        sortable: false
                    },

                    columns: [{
                        header: _('CatId'),
                        dataIndex: 'catid',
                        width: 100,
                        fixed: true
                    }, {
                        header: _('Title'),
                        dataIndex: 'title',
                        editor: editor
                    }, {
                        header: "",
                        dataIndex: 'system',
                        width: 16,
                        renderer: systemRenderer,
                        fixed: true
                    }, {
                        header: _('Visible'),
                        dataIndex: 'visible',
                        editor: booleanEditor,
                        width: 80,
                        fixed: true

                    }, {
                        header: _('Position'),
                        dataIndex: 'position',
                        editor: intEditor,
                        width: 80,
                        fixed: true
                    }, {
                        header: _('Cronks'),
                        dataIndex: 'count_cronks',
                        width: 60,
                        fixed: true
                    }, {
                        header: "",
                        dataIndex: "permission_set",
                        width: 30,
                        fixed: true,
                        renderer: function(value) {
                            var iconCls = "icinga-icon-lock-open";
                            var tooltip = _("No permissions set");
                            
                            if (value===true) {
                                iconCls = 'icinga-icon-lock';
                                tooltip = _("Permissions active");
                            }
                            
                            return String.format(
                                '<div ext:qtip="{1}" class="icon-24 {0}"></div>',
                                iconCls,
                                tooltip
                            );
                        }
                    }, {
                        header: "",
                        dataIndex: "catid",
                        width: 65,
                        fixed: true,
                        renderer: Cronk.grid.WidgetRenderer.button({
                            iconCls: 'icinga-icon-lock',
                            text: _('Edit'),
                            handler: function(b, e, record) {
                                /*
                                 * Rest of the handling is controled with events
                                 * See construction of permission window
                                 * for details (line ~ 64)
                                 */
                                this.permissionWindow.alignTo(e.getTarget(), "tl?");
                                this.permissionWindow.update(record);
                            },
                            scope: this
                        })
                    }]
                }),

                viewConfig: {
                    forceFit: true
                },

                tbar: [{
                    text: _('Reload'),
                    iconCls: 'icinga-icon-arrow-refresh',
                    handler: function (b, e) {
                        this.grid.getStore().reload();
                    },
                    scope: this
                }, '-', {
                    text: _('Add'),
                    iconCls: 'icinga-icon-add',
                    handler: function (b, e) {

                        var win = new Icinga.Cronks.util.CategoryEditorForm({
                            renderTo: Ext.getBody()
                        });

                        win.on('submitForm', function (formPanel, values, form) {
                            // AppKit.log(values);

                            var record = new this.grid.store.recordType({
                                id: this.grid.store.getCount() + 1,
                                catid: values.catid,
                                title: values.title,
                                system: false,
                                position: values.position,
                                visible: Boolean(values.visible)
                            });

                            this.grid.store.addSorted(record);

                            return true;

                        }, this);

                        win.show(b);
                    },
                    scope: this
                }, {
                    text: _('Delete'),
                    iconCls: 'icinga-icon-delete',
                    handler: function (b, e) {
                        var record = this.grid.getSelectionModel().getSelected();

                        if (!record) {
                            return false;
                        }

                        if (record.data.system === true) {
                            AppKit.notifyMessage(_('Error'), _('You can not delete system categories'));
                            return false;
                        }

                        this.grid.store.remove(record);
                    },
                    scope: this
                }]
            }))();

            grid.on('beforeedit', function (e) {
                if (e.record.data.system === true) {
                    AppKit.notifyMessage(_('Error'), _('System categories are not editable!'));
                    return false;
                }
            }, this);

            return grid;
        }
    });
    
})();