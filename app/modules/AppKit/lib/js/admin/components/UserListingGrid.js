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

    AppKit.Admin.Components.UserListingGrid = Ext.extend(Ext.Panel, {
        title: 'Roles',
        iconCls: 'icinga-icon-group',
        constructor: function (cfg) {
            Ext.apply(this, cfg);
            cfg.items = [{
                xtype: 'listview',
                store: this.store,
                multiSelect: true,
                columns: [{
                    header: _('Role'),
                    dataIndex: 'name'
                }, {
                    header: _('Description'),
                    dataIndex: 'description'
                },
                new(Ext.extend(Ext.list.BooleanColumn, {
                    trueText: '<div style="width:16px;height:16px;margin-left:25px" class="icinga-icon-accept"></div>',
                    falseText: '<div style="width:16px;height:16px;margin-left:25px" class="icinga-icon-cancel"></div>'
                }))({
                    header: _('Active'),
                    dataIndex: 'active',
                    width: 0.1
                })]
            }];
            Ext.Panel.prototype.constructor.call(this, cfg);
        },

        tbar: [{
            text: _('Add role'),
            iconCls: 'icinga-icon-add',
            handler: function (c) {
                var panel = c.ownerCt.ownerCt;
                panel.showRoleSelectionDialog();
            },
            scope: this
        }, {
            text: _('Remove selected'),
            iconCls: 'icinga-icon-cancel',
            handler: function (c) {
                var panel = c.ownerCt.ownerCt;
                var list = panel.findByType('listview')[0];
                panel.store.remove(list.getSelectedRecords());

            },
            scope: this
        }],
        showRoleSelectionDialog: function () {
            var groupsStore = new Ext.data.JsonStore({
                url: this.roleProviderURI,
                autoLoad: true,
                autoDestroy: true,
                root: 'roles',
                fields: [{
                    name: 'id'
                }, {
                    name: 'name'
                }, {
                    name: 'description'
                }, {
                    name: 'active'
                }, {
                    name: 'disabled_icon',
                    mapping: 'active',
                    convert: function (v) {
                        return '<div style="width:16px;height:16px;margin-left:25px" class="' + (v === 1 ? 'icinga-icon-cancel' : 'icinga-icon-accept') + '"></div>';
                    }
                }]
            });
            var grid = new Ext.grid.GridPanel({
                autoScroll: true,
                bbar: new Ext.PagingToolbar({
                    pageSize: 25,
                    store: groupsStore,
                    displayInfo: true,
                    displayMsg: _('Displaying roles') + ' {0} - {1} ' + _('of') + ' {2}',
                    emptyMsg: _('No roles to display')
                }),
                store: groupsStore,
                viewConfig: {
                    forceFit: true
                },
                
                colModel: new Ext.grid.ColumnModel({
                    defaults: {
                        sortable:true
                    },
                    columns: [{
                        header: _('Id'),
                        width: 20,
                        dataIndex: 'id',
                        sortable: true
                    }, {
                        header: _('Name'),
                        dataIndex: 'name',
                        sortable: true
                    }, {
                        header: _('Description'),
                        dataIndex: 'description',
                        sortable: true
                    }, {
                        header: _('Status'),
                        width: 50,
                        dataIndex: 'disabled_icon',
                        sortable: true
                    }]
                })
            });

            (new Ext.Window({
                title: _('Select roles'),
                modal: true,
                layout: 'fit',
                iconCls: 'icinga-icon-group',
                height: Ext.getBody().getHeight() * 0.5,
                width: Ext.getBody().getWidth() * 0.5,
                items: [grid],
                buttons: [{
                    text: _('Add selected'),
                    iconCls: 'icinga-icon-add',
                    handler: function (c) {
                        var selected = grid.getSelectionModel().getSelections();
                        Ext.iterate(selected, function (item) {
                            if (this.store.getById(item.get('id'))) {
                                return true;
                            }
                            this.store.add(item);
                            return true;
                        }, this);
                        c.ownerCt.ownerCt.close();
                    },
                    scope: this
                }, {
                    text: _('Cancel'),
                    iconCls: 'icinga-icon-cancel',
                    handler: function (c) {
                        c.ownerCt.ownerCt.close();
                    }
                }]
            })).show(Ext.getBody());
        }
    });

})();