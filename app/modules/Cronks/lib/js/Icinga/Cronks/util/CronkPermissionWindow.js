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

(function () {

    "use strict";

    /**
     * Window to edit permissions for cronks
     */
    Icinga.Cronks.util.CronkPermissionWindow = Ext.extend(Ext.Window, {
        width: 450,
        height: 350,

        layout: 'fit',

        constructor: function (config) {

            this.addEvents({
                'save': true,
                'beforesave': true,
                'load': true,
                'beforeload': true
            });

            this.id = 'icinga-cronk-permission-window';
            this.closeAction = 'hide';
            this.hidden = true;
            this.resizable = false;

            this.baseUrl = AppKit.util.Config.get('baseurl') + '/modules/cronks/provider/cronks/security';

            Icinga.Cronks.util.CronkPermissionWindow.superclass.constructor.call(this, config);
        },

        initComponent: function () {

            this.initBottomBar();

            Icinga.Cronks.util.CronkPermissionWindow.superclass.initComponent.call(this);

            this.groupStore = new Ext.data.JsonStore({
                autoDestroy: true,
                url: AppKit.c.path + '/modules/appkit/provider/groups?oldBehaviour=0',
                fields: [{
                    name: 'id'
                }, {
                    name: 'name'
                }],
                idProperty: 'id',
                root: 'roles',
                totalProperty: 'totalCount',
                successProperty: 'success'
            });

            this.groupStore.load();

            this.initLayout();

            this.render(Ext.getBody());
        },

        initBottomBar: function () {
            this.bbar = ['->', {
                text: _('Save'),
                iconCls: 'icinga-action-icon-ok',
                handler: function (button, event) {
                    this.save();
                },
                scope: this
            }, {
                text: _('Cancel'),
                iconCls: 'icinga-action-icon-cancel',
                handler: function (button, event) {
                    this.hide();
                },
                scope: this
            }];
        },

        initLayout: function () {

            var itemWidth = 200;

            this.roleSelect = Ext.create({
                xtype: 'multiselect',
                name: 'roles',
                style: {
                    overflow: 'hidden'
                },
                width: itemWidth,
                height: 100,
                fieldLabel: _('Roles'),
                store: this.groupStore,
                valueField: 'id',
                displayField: 'name',
                msgTarget: 'side'
            });

            this.formPanel = Ext.create({
                xtype: 'form',
                padding: '10px',
                layout: {
                    type: 'vbox',
                    pack: 'start'
                },
                items: [{
                    xtype: 'fieldset',
                    title: _('Cronk information'),
                    width: 400,
                    defaults: {
                        width: itemWidth,
                        readOnly: true
                    },
                    items: [{
                        xtype: 'textfield',
                        name: 'system',
                        fieldLabel: _('Type')
                    }, {
                        xtype: 'textfield',
                        name: 'cronkid',
                        fieldLabel: _('Cronk ID')
                    }, {
                        xtype: 'textfield',
                        name: 'org_name',
                        fieldLabel: _('Name')
                    }]
                }, {
                    xtype: 'fieldset',
                    title: _('Assigned groups'),
                    width: 400,
                    items: [{
                        xtype: "container",
                        layout: {
                            type: 'hbox',
                            defaultMargins: '0px 5px 0px 0px'
                        },
                        width: 300,
                        height: 200,
                        fieldLabel: _('Roles'),
                        
                        items: [this.roleSelect, {
                            xtype: "container",
                            layout: {
                                type: 'vbox',
                                defaultMargins: '0px 0px 5px 0px'
                            },
                            defaults: { width: '65px' },
                            height: 200,
                            items: [{
                                xtype: "button",
                                text: _("Drop all"),
                                iconCls: "icinga-icon-cross",
                                handler: function(button, event) {
                                    this.roleSelect.oldValues = this.roleSelect.getValue();
                                    this.roleSelect.setValue([]);
                                },
                                scope: this
                            }, {
                                xtype: "button",
                                text: _("Reset"),
                                iconCls: "icinga-icon-arrow-undo",
                                handler: function(button, event) {
                                    if (!Ext.isEmpty(this.roleSelect.oldValues)) {
                                        this.roleSelect.setValue(
                                            this.roleSelect.oldValues
                                        );
                                    }
                                },
                                scope: this
                            }]
                        }]
                    }]
                }]
            });

            this.add(this.formPanel);
            this.doLayout();
        },

        setCronkUid: function (cronkuid) {
            this.cronkuid = cronkuid;
        },

        getCronkUid: function () {
            return this.cronkuid;
        },

        getCronkUrl: function () {
            return this.baseUrl + '/' + this.getCronkUid();
        },

        setRoles: function (roles) {
            if (Ext.isString(roles)) {
                roles = String(roles).split(",");
            }

            if (Ext.isArray(roles)) {
                this.formPanel.getForm().findField("roles").setValue(roles);
            }
        },

        getRoles: function () {
            var roles = this.formPanel.getForm().findField('roles').getValue();

            if (roles) {
                roles = roles.split(",");
            } else {
                roles = [];
            }

            return roles;
        },

        update: function (object) {
            if (this.fireEvent('beforeload', this, object) === true) {
                this.setTitle(String.format(_('Permissions for {0}'), object.org_name));

                var f = this.formPanel.getForm();

                Ext.iterate(object, function (key, val) {
                    var field = f.findField(key);
                    if (field) {

                        if (key === "system") {
                            if (val === true) {
                                val = _("System");
                            } else {
                                val = _("Custom");
                            }
                        }

                        field.setValue(val);
                    }
                }, this);

                this.setCronkUid(object.cronkid);

                Ext.Ajax.request({
                    url: this.getCronkUrl(),
                    success: this.handleCronkResponse,
                    scope: this
                });
            }
        },

        /*
         * @private
         */
        handleCronkResponse: function (response, opts) {
            var data = Ext.decode(response.responseText);
            if (data.success === true) {
                this.setRoles(data.role_uids);
                this.fireEvent('load', this);
            }
        },

        save: function () {
            if (this.fireEvent('beforesave', this) === true) {

                this.formPanel.getForm().submit({
                    url: this.getCronkUrl(),
                    params: {
                        xaction: 'write',
                        j: Ext.encode({
                            roles: this.getRoles()
                        })
                    },
                    success: function () {
                        if (this.fireEvent('save', this) === true) {
                            this.hide();
                            AppKit.notifyMessage(
                            _('Success'),
                            _('Cronk permissions has been updated'));
                        }
                    },
                    failure: function (form, action) {
                        if (action.failureType === "server") {
                            AppKit.notifyMessage(
                            _("Error"),
                            _("Could not save cronk restrictions"));
                        }
                    },
                    scope: this
                });

            }
        }

    });

})();