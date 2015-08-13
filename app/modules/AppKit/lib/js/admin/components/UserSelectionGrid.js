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

/*global Ext: false, Icinga: false, _: false, AppKit: false */
Ext.ns("AppKit.Admin.Components");

(function () {

    "use strict";

    AppKit.Admin.Components.UserSelectionGrid = Ext.extend(Ext.Panel, {
        title: 'Users',
        iconCls: 'icinga-icon-user',
        layout:'fit',
        constructor: function (cfg) {
            this.store = cfg.store;
            this.userProviderURI = cfg.userProviderURI;

            //cfg.bbar = this.getBbarDefinition();
            cfg.tbar = this.getTbarDefinition();
            cfg.items = this.getItems();
            Ext.Panel.prototype.constructor.call(this, cfg);
        },
        getBbarDefinition: function () {
            return new Ext.PagingToolbar({
                store: this.store,
                displayInfo: true,
                pageSize: 25,
                displayMsg: _('Displaying users') + ' {0} - {1} ' + _('of') + ' {2}',
                emptyMsg: _('No user to display')
            });
        },

        getItems: function () {
            return [{
                xtype: 'grid',
                autoScroll: true,
                store: this.store,
                multiSelect: true,
                columns: [{
                    header: _('User'),
                    dataIndex: 'name',
                    sortable: true,
                    width: 200
                }, {
                    header: _('Firstname'),
                    dataIndex: 'firstname',
                    sortable: true,
                    width: 200
                }, {
                    header: _('Lastname'),
                    dataIndex: 'lastname',
                    sortable: true,
                    width: 300
                },
                    new(Ext.extend(Ext.grid.BooleanColumn, {
                        trueText: '<div style="width:16px;height:16px;margin-left:25px" class="icinga-icon-accept"></div>',
                        falseText: '<div style="width:16px;height:16px;margin-left:25px" class="icinga-icon-cancel"></div>'
                    }))({
                        header: _('Active'),
                        sortable: true,
                        dataIndex: 'active',
                        width: 120
                    })],
                viewConfig: {
                    forceFit: true
                }
            }];
        },
        getTbarDefinition: function () {
            return [{
                text: _('Add user'),
                iconCls: 'icinga-icon-add',
                handler: function (c) {
                    var panel = c.ownerCt.ownerCt;
                    panel.showUserSelectionDialog();
                },
                scope: this
            }, {
                text: _('Remove selected'),
                iconCls: 'icinga-icon-cancel',
                handler: function (c) {
                    var panel = c.ownerCt.ownerCt;
                    var grid = panel.findByType('grid')[0];
                    if (grid) {
                        this.store.remove(grid.getSelectionModel().getSelections());
                    } else {
                        throw("Grid could not be found");
                    }

                },
                scope: this
            },'->',{
                xtype: 'textfield',
                iconCls: 'icinga-icon-zoom',
                emptyText: _('Type to search'),
                enableKeyEvents: true,
                listeners: {
                    keyup: function(field) {
                        if(field.getRawValue() !== _('Type to search')) {
                            this.store.filter("name",field.getRawValue(),true,true);
                        } else {
                            this.store.clearFilter();
                        }

                    },
                    scope: this
                }

            }];
        },
        showUserSelectionDialog: function () {
            var groupsStore = new Ext.data.JsonStore({
                url: this.userProviderURI,
                remoteSort: true,
                totalProperty: 'totalCount',
                proxy: new Ext.data.HttpProxy({
                    api: {
                        read: {
                            method: 'GET',
                            url: this.userProviderURI
                        }
                    }
                }),

                autoDestroy: true,
                root: 'users',
                fields: [{
                    name: 'id'
                }, {
                    name: 'name'
                }, {
                    name: 'firstname'
                }, {
                    name: 'lastname'
                }, {
                    name: 'active'
                }, {
                    name: 'disabled_icon',
                    mapping: 'disabled',
                    convert: function (v) {
                        return '<div style="width:16px;height:16px;margin-left:25px" class="' + (v === 1 ? 'icinga-icon-cancel' : 'icinga-icon-accept') + '"></div>';
                    }
                }]
            });
            var grid = new Ext.grid.GridPanel({

                bbar: new Ext.PagingToolbar({
                    pageSize: 25,
                    store: groupsStore,
                    displayInfo: true,
                    displayMsg: _('Displaying users') + ' {0} - {1} ' + _('of') + ' {2}',
                    emptyMsg: _('No users to display'),
                    listeners: {
                        render: function(cmp) {
                            cmp.doRefresh();
                        }
                    }
                }),
                store: groupsStore,

                autoScroll:true,
                viewConfig: {
                    forceFit: true
                },
                colModel: new Ext.grid.ColumnModel({
                    defaults: {
                        sortable: true
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
                        header: _('Firstname'),
                        dataIndex: 'firstname',
                        sortable: true
                    }, {
                        header: _('Lastname'),
                        dataIndex: 'lastname',
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
                title: _('Select users'),
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
                            item.set('active', item.get('disabled') !== 1);
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
