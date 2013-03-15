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
Ext.ns("AppKit.Admin");
(function () {
    "use strict";
    // private static
    var userProviderURI = "";
    var userGridCmp = null;
    var userFormCmp = null;
    var userList = null;

    var initUserListStore = function (cfg) {
            userList = new Ext.data.JsonStore({
                autoDestroy: true,
                storeId: 'userListStore',
                totalProperty: 'totalCount',
                root: 'users',
                idProperty: 'id',

                url: userProviderURI,
                remoteSort: true,

                baseParams: {
                    hideDisabled: false
                },
                proxy: new Ext.data.HttpProxy({
                    api: {
                        read: {
                            method: 'GET',
                            url: userProviderURI
                        }
                    }
                }),
                fields: [{
                    name: 'id',
                    type: 'int'
                }, 'name', 'lastname', 'firstname', 'email', 'authsrc',
                {
                    name: 'disabled',
                    type: 'boolean'
                }, {
                    name: 'disabled_icon',
                    mapping: 'disabled',
                    convert: function (v) {
                        return '<div style="width:16px;height:16px;margin-left:25px" class="' + (v == 1 ? 'icinga-icon-cancel' : 'icinga-icon-accept') + '"></div>';
                    }
                }, {
                    name: 'created'
                }, {
                    name: 'modified'
                }]
            });
        };

    var initUserGridComponent = function (cfg) {
            userGridCmp = new Ext.grid.GridPanel({
                title: _('Available users'),
                stateful: false,
                sm: new Ext.grid.RowSelectionModel(),
                iconCls: 'icinga-icon-user',

                deleteSelected: function () {
                    Ext.Msg.confirm(_("Delete user"), _("Do you really want to delete these users and their settings and cronks?"), function (btn) {
                        if (btn !== "yes") {
                            return false;
                        }
                        var selModel = this.getSelectionModel();
                        var selected = selModel.getSelections();
                        var ids = [];

                        Ext.each(selected, function (record) {
                            ids.push(record.get("id"));
                        }, this);
                        var uri = userProviderURI + "/ids=" + ids.join(",");
                        Ext.Ajax.request({
                            url: uri,
                            method: 'DELETE',
                            success: function () {
                                this.getStore().reload();
                            },
                            scope: this,
                            params: ids

                        });
                    }, this);
                },
                viewConfig: {
                    scrollOffset: 30,
                    forceFit: true
                },
                tbar: {
                    items: [{
                        xtype: 'button',
                        iconCls: 'icinga-icon-arrow-refresh',
                        scope: this,
                        text: 'Refresh',
                        handler: function () {
                            userGridCmp.getBottomToolbar().doRefresh();
                        }

                    }, {
                        xtype: 'button',
                        iconCls: 'icinga-icon-cancel',
                        text: _('Remove selected'),
                        handler: function (ev, btn) {
                            userGridCmp.deleteSelected();
                        },
                        scope: this
                    }, ' ',
                    {
                        xtype: 'button',
                        iconCls: 'icinga-icon-add',
                        text: _('Add new user'),
                        handler: function () {
                            AppKit.Admin.UserEditForm.bindUser('new', userProviderURI);
                            Ext.getCmp('userEditor').setDisabled(false);
                            Ext.getCmp('btn-save-user').setText(_('Create user'));
                            Ext.getCmp('btn-save-user').setIconClass('icinga-icon-user-add');
                            Ext.getCmp('progressbar-field').setValue();
                        }

                    }, '->',
                    {
                        xtype: 'button',
                        enableToggle: true,
                        text: _('Hide disabled'),
                        id: 'hide_disabled',
                        name: 'disabled',
                        listeners: {
                            toggle: function (btn, checked) {
                                userGridCmp.getStore().setBaseParam('hideDisabled', checked);
                                return true;
                            }
                        }
                    }]

                },
                bbar: new Ext.PagingToolbar({
                    pageSize: 50,
                    store: userList,
                    displayInfo: true,
                    displayMsg: _('Displaying users') + ' {0} - {1} ' + _('of') + ' {2}',
                    emptyMsg: _('No users to display'),
                    listeners: {
                        render: function(cmp) {
                            cmp.doRefresh();
                        }
                    }
                }),

                store: userList,

                listeners: {
                    rowclick: function (grid, index, _e) {
                        var id = grid.getStore().getAt(index).get("id");
                        Ext.getCmp('userEditor').setDisabled(false);
                        Ext.getCmp('btn-save-user').setText(_('Save'));
                        Ext.getCmp('btn-save-user').setIconClass('icinga-icon-disk');
                        Ext.getCmp('progressbar-field').setValue();
                        AppKit.Admin.UserEditForm.bindUser(id, userProviderURI);
                    }

                },


                colModel: new Ext.grid.ColumnModel({
                    defaults: {
                        width: 120,
                        sortable: true
                    },
                    columns: [{
                        id: 'id',
                        header: 'ID',
                        width: 75,
                        dataIndex: 'id',
                        hidden: true
                    }, {
                        header: _('username'),
                        dataIndex: 'name'
                    }, {
                        header: _('lastname'),
                        dataIndex: 'lastname'
                    }, {
                        header: _('firstname'),
                        dataIndex: 'firstname'
                    }, {
                        header: _('email'),
                        dataIndex: 'email'
                    }, {                     
                        header: _('authsrc'),
                        dataIndex: 'authsrc',
                        hidden: true
                    }, {
                        header: _('active'),
                        dataIndex: 'disabled_icon',
                        width: 75
                    }]
                })

            });
        };

    var initContainerComponent = function (cfg) {
            if (userGridCmp === null) {
                throw "User grid component not correctly initialized, aborting container creation";
            }

            return {
                layout: 'fit',

                items: new Ext.Panel({
                    layout: 'border',
                    border: false,
                    defaults: {
                        margins: {
                            top: 10,
                            left: 10,
                            bottom: 0
                        }
                    },
                    items: [{
                        region: 'center',
                        xtype: 'panel',
                        layout: 'fit',
                        id: 'userListPanel',

                        items: userGridCmp,
                        autoScroll: true
                       
                    }, {
                        region: 'east',
                        xtype: 'panel',
                        padding: 5,
                        width: '50%',
                        minWidth:225,
                        disabled: true,
                        split: true,
                        id: 'userEditor',
                        autoScroll: true,
                        title: _('Edit user'),
                        items: userFormCmp,
                        buttons: [{
                            xtype: 'displayfield',
                            id: 'progressbar-field',
                            width: 200
                        }, {
                            iconCls: 'icinga-icon-disk',
                            id: 'btn-save-user',
                            text: _('Save'),
                            handler: function (b) {
                                b.setIconClass('icinga-icon-throbber');
                                b.setText(_("Saving user"));
                                b.setDisabled(true);
                                AppKit.Admin.UserEditForm.saveUser(
                                userProviderURI, function () {
                                    Ext.getCmp('progressbar-field').setValue("<span style='color:green;margin:4px;'>" + _("User saved successfully") + "</span>");
                                    b.setIconClass('icinga-icon-disk');
                                    b.setText(_("Save"));
                                    b.setDisabled(false);
                                    userList.load({
                                        params: {
                                            start: 0,
                                            limit: 25
                                        }
                                    });

                                }, function () {
                                    Ext.getCmp('progressbar-field').setValue("<span style='color:red;margin:4px;'>" + _("Couldn't save user, review your settings") + "</span>");
                                    b.setIconClass('icinga-icon-disk');
                                    b.setText(_("Retry"));
                                    b.setDisabled(false);
                                });

                            }
                        }]
                    }]
                })
            };

        };

    var initUserFormComponent = function (cfg) {

            userFormCmp = new Ext.form.FormPanel({
                border: false,
                items: AppKit.Admin.UserEditForm(cfg)
            });
        };
    AppKit.Admin.UserManager = Ext.extend(Ext.Container, {
        constructor: function (cfg) {
            userProviderURI = cfg.userProviderURI;
            initUserListStore(cfg);
            initUserGridComponent(cfg);
            initUserFormComponent(cfg);

            Ext.apply(cfg, initContainerComponent(cfg));
            Ext.Container.prototype.constructor.call(this, cfg);
        }
    });
/*
    this.construct = function() {
        initUserGridComponent();
        initUserFormComponent();
        initContainerComponent();

        AppKit.util.Layout.getCenter().add(containerCmp);
        
    }
    
    this.construct();*/
})();