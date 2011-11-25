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
                    name: 'daisabled_icon',
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
                    displayMsg: _('Displaying roles') + ' {0} - {1} ' + _('of') + ' {2}',
                    emptyMsg: _('No roles to display')
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
                    header: _('Description'),
                    dataIndex: 'description'
                }, {
                    header: _('Status'),
                    width: 50,
                    dataIndex: 'disabled_icon'
                }]
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