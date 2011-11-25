/*global Ext: false, Icinga: false, _: false, AppKit: false */
Ext.ns("AppKit.Admin.Components");

(function () {

    "use strict";

    AppKit.Admin.Components.UserSelectionGrid = Ext.extend(Ext.Panel, {
        title: 'Users',
        iconCls: 'icinga-icon-user',

        constructor: function (cfg) {
            this.store = cfg.store;
            this.userProviderURI = cfg.userProviderURI;

            cfg.bbar = this.getBbarDefinition();
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
                emptyMsg: _('No roles to display')
            });
        },

        getItems: function () {
            return [{
                xtype: 'listview',
                store: this.store,
                multiSelect: true,
                columns: [{
                    header: _('User'),
                    dataIndex: 'name'
                }, {
                    header: _('Firstname'),
                    dataIndex: 'firstname'
                }, {
                    header: _('Lastname'),
                    dataIndex: 'lastname'
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
                    var list = panel.findByType('listview')[0];
                    this.store.remove(list.getSelectedRecords());

                },
                scope: this
            }];
        },
        showUserSelectionDialog: function () {
            var groupsStore = new Ext.data.JsonStore({
                url: this.userProviderURI,
                proxy: new Ext.data.HttpProxy({
                    api: {
                        read: {
                            method: 'GET',
                            url: this.userProviderURI
                        }
                    }
                }),
                autoLoad: true,
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
                    mapping: 'active',
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
                    emptyMsg: _('No users to display')
                }),
                store: groupsStore,
                viewConfig: {
                    forceFit: true
                },
                columns: [{
                    header: _('Id'),
                    width: 20,
                    dataIndex: 'id'
                }, {
                    header: _('Name'),
                    dataIndex: 'name'
                }, {
                    header: _('Firstname'),
                    dataIndex: 'firstname'
                }, {
                    header: _('Lastname'),
                    dataIndex: 'lastname'
                }, {
                    header: _('Status'),
                    width: 50,
                    dataIndex: 'disabled_icon'
                }]
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