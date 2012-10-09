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

Ext.ns("AppKit.Admin");

(function () {

    "use strict";

    /**
     * Helper function to generate a random api key
     * @return String
     **/
    var getApiKey = function () {

        var _string = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghiklmnopqrstuvwxyz1234567890";
        var rnd = parseInt(Math.random() * 5, 10) + 25;
        var key = "";

        while (rnd--) {
            var nr = parseInt(Math.random() * _string.length, 10);
            key += _string[nr];
        }
        return key;
    };

    var setInternalFieldsEnabled = function (bool) {
        var field = Ext.getCmp('form_user_disabled');
        var passfield = Ext.getCmp('password_fieldset');

        field.setVisible(bool);
        passfield.setVisible(bool);
        field.setDisabled(!bool);
        passfield.setDisabled(!bool);

    };



    AppKit.Admin.UserEditForm = function (cfg) {
        var authTypes = [];
        Ext.iterate(cfg.authTypes, function (type) {
            authTypes.push([type]);
        });


        var userRoleStore = new Ext.data.JsonStore({
            idProperty: 'id',
            fields: ['name', 'active', 'description', 'id']
        });

        var userCredentialStore = new Ext.data.JsonStore({
            idProperty: 'target_id',
            fields: ['target_id', 'target_name', 'target_description'],
            data: cfg.availablePrincipals
        });

        var userHostgroupPrincipalStore = new Ext.data.JsonStore({
            idProperty: 'hostgroup',
            fields: ['hostgroup']
        });
        var userServicegroupPrincipalStore = new Ext.data.JsonStore({
            idProperty: 'servicegroup',
            fields: ['servicegroup']
        });

        var userCustomvarPrincipalStore = new Ext.data.JsonStore({
            fields: ['id', 'name', 'value', 'target']
        });

        var userServicePrincipalStore = new Ext.data.JsonStore({
            idProperty: 'value',
            fields: ['value', 'target']
        });

        var userHostPrincipalStore = new Ext.data.JsonStore({
            idProperty: 'value',
            fields: ['value', 'target']
        });

        var userStore = new Ext.data.JsonStore({
            root: 'user',
            idProperty: 'id',
            url: 'none',
            fields: ['id', 'name', 'firstname', {
                name: 'disabled',
                type: 'boolean'
            }, 'lastname', 'modified', 'created', 'email', 'authsrc', 'authkey', 'roles', 'principals'],
            newUser: function () {
                Ext.iterate(this.fields.keys, function (key) {
                    var field = Ext.getCmp("form_user_" + key);
                    if (!field) {
                        return;
                    }
                    field.setValue("");
                }, this);
                Ext.getCmp("form_user_id").setValue('new');
                userRoleStore.removeAll();
                credentialView.selectValues([]);
                hostgroupPrincipalsView.selectValues([]);
                servicegroupPrincipalsView.selectValues([]);
                customVariableView.selectValues([]);
                userRestrictionFlagsView.selectValues([]);
                servicePrincipalsView.selectValues([]);
                hostPrincipalsView.selectValues([]);
            },
            listeners: {
                load: function (store, records, options) {
                    var record = records[0];
                    if (!record) {
                        return;
                    }

                    Ext.iterate(record.fields.keys, function (key) {
                        var field = Ext.getCmp("form_user_" + key);
                        if (!field) {
                            return;
                        }
                        field.setValue(record.get(key));
                    }, this);

                    userRoleStore.loadData(record.get('roles'));
                    var principals = record.get('principals');
                    credentialView.selectValues(principals);
                    hostgroupPrincipalsView.selectValues(principals);
                    servicegroupPrincipalsView.selectValues(principals);
                    customVariableView.selectValues(principals);
                    userRestrictionFlagsView.selectValues(principals);
                    servicePrincipalsView.selectValues(principals);
                    hostPrincipalsView.selectValues(principals);
                },
                scope: this
            }

        });

        var hostgroupPrincipalsView = new AppKit.Admin.Components.GroupRestrictionView({
            target: 'hostgroup',
            store: userHostgroupPrincipalStore
        });

        var servicegroupPrincipalsView = new AppKit.Admin.Components.GroupRestrictionView({
            target: 'servicegroup',
            store: userServicegroupPrincipalStore
        });

        var customVariableView = new AppKit.Admin.Components.CVGridPanel({
            target: 'servicegroup',
            store: userCustomvarPrincipalStore
        });

        var servicePrincipalsView = new AppKit.Admin.Components.ObjectRestrictionView({
            store: userServicePrincipalStore,
            type: 'user',
            target: 'service'
        });

        var hostPrincipalsView = new AppKit.Admin.Components.ObjectRestrictionView({
            store: userHostPrincipalStore,
            type: 'user',
            target: 'host'
        });

        var credentialView = new AppKit.Admin.Components.CredentialGrid({
            store: userCredentialStore,
            type: 'user'
        });

        var roleView = new AppKit.Admin.Components.UserListingGrid({
            store: userRoleStore,
            roleProviderURI: cfg.roleProviderURI
        });

        var userRestrictionFlagsView = new AppKit.Admin.Components.RestrictionFlagsView({
            type: 'user'
        });

        var clearPassword = function() {
            var passfield = Ext.getCmp('form_user_password');
            var passfield_confirm = Ext.getCmp('form_user_password_confirmed');
            passfield.setValue("");
            passfield_confirm.setValue("");
        }

        AppKit.Admin.UserEditForm.bindUser = function (id, url) {
            clearPassword();
            if (id !== 'new') {
                userStore.proxy.setUrl(url + "/id=" + id);
                userStore.load();
            } else {
                userStore.newUser();
            }
        };

        AppKit.Admin.UserEditForm.saveUser = function (url, success, fail) {
            userStore.proxy.setUrl(url + "/create");
            var params = {};

            var i = 0;
            userRoleStore.each(function (role) {
                params["userroles[" + (i++) + "]"] = role.get("id");
            });

            i = 0;

            userServicePrincipalStore.each(function (p) {
                params["principal_target[" + i + "][name][]"] = "IcingaService";
                params["principal_value[" + i + "][value][]"] = p.get("value");
                params["principal_target[" + i + "][set][]"] = 1;
                i++;
            });

            userHostPrincipalStore.each(function (p) {
                params["principal_target[" + i + "][name][]"] = "IcingaHost";
                params["principal_value[" + i + "][value][]"] = p.get("value");
                params["principal_target[" + i + "][set][]"] = 1;
                i++;
            });

            userHostgroupPrincipalStore.each(function (p) {
                params["principal_target[" + i + "][name][]"] = "IcingaHostgroup";
                params["principal_value[" + i + "][hostgroup][]"] = p.get("hostgroup");
                params["principal_target[" + i + "][set][]"] = 1;
                i++;
            });

            userServicegroupPrincipalStore.each(function (p) {
                params["principal_target[" + i + "][name][]"] = "IcingaServicegroup";
                params["principal_value[" + i + "][servicegroup][]"] = p.get("servicegroup");
                params["principal_target[" + i + "][set][]"] = 1;
                i++;
            });

            userCustomvarPrincipalStore.each(function (p) {
                if (p.get("target") === "host") {
                    params["principal_target[" + i + "][name][]"] = "IcingaHostCustomVariablePair";
                } else if (p.get("target") === "service") {
                    params["principal_target[" + i + "][name][]"] = "IcingaServiceCustomVariablePair";
                } else {
                    return;
                }
                params["principal_target[" + i + "][set][]"] = 1;
                params["principal_value[" + i + "][cv_name][]"] = p.get("name");
                params["principal_value[" + i + "][cv_value][]"] = p.get("value");
                i++;
            });

            Ext.iterate(userCredentialStore.selectedValues, function (p) {
                params["principal_target[" + i + "][set][]"] = 1;
                params["principal_target[" + i + "][name][]"] = p.get("target_name");
                i++;
            });

            Ext.iterate(userRestrictionFlagsView.roleFlags, function (flag) {
                if (!Ext.getCmp(flag.id).getValue()) {
                    return true;
                }
                params["principal_target[" + i + "][set][]"] = 1;
                params["principal_target[" + i + "][name][]"] = flag.principal;
                i++;
            });

            var paramMap = {
                id: 'form_user_id',
                user_name: 'form_user_name',
                user_firstname: 'form_user_firstname',
                user_lastname: 'form_user_lastname',
                user_email: 'form_user_email',
                user_disabled: 'form_user_disabled',
                user_authsrc: 'form_user_authsrc',
                user_authkey: 'form_user_authkey',
                password: 'form_user_password',
                password_validate: 'form_user_password_confirmed'
            };
            for (var id in paramMap) {
                if (id) {
                    var cmp = Ext.getCmp(paramMap[id]);
                    if (cmp.isValid()) {
                        if (cmp.getValue()) { // don't write empty fields 
                            params[id] = cmp.getValue();

                        } else {
                            continue;
                        }
                    } else {
                        return fail(arguments);
                    }
                }
            }
            userStore.on("load", function () {
                success(arguments);
            }, this, {
                single: true
            });
            userStore.on("exception", function () {
                fail(arguments);
            }, this, {
                single: true
            });
            if (params.user_disabled) {
                params.user_disabled = 1;
            }
            userStore.load({
                params: params
            });
        };

        return [{
            xtype: 'hidden',
            name: 'user_id',
            id: 'form_user_id'
        }, {
            xtype: 'fieldset',
            title: _('General information'),
            defaults: {
                allowBlank: false
            },
            items: [{
                xtype: 'textfield',
                fieldLabel: _('User name'),
                name: 'user_name',
                id: 'form_user_name',
                anchor: '95%',
                minLength: 3,
                maxLength: 127
            }, {
                xtype: 'container',
                layout: 'column',
                anchor: '100%',
                items: [{
                    xtype: 'container',
                    layout: 'form',

                    items: {
                        fieldLabel: _('Name'),
                        name: 'user_firstname',
                        id: 'form_user_firstname',
                        xtype: 'textfield',
                        anchor: '95%',
                        allowBlank: false,
                        minLength: 3,
                        maxLength: 40
                    },
                    columnWidth: 0.5
                }, {
                    xtype: 'container',
                    layout: 'form',
                    labelWidth: 65,
                    items: {
                        xtype: 'textfield',
                        name: 'user_lastname',
                        id: 'form_user_lastname',
                        fieldLabel: _('Surname'),
                        anchor: '90%',
                        allowBlank: false,
                        minLength: 3,
                        maxLength: 40
                    },
                    columnWidth: 0.5
                }]
            }, {
                xtype: 'textfield',
                fieldLabel: _('Email'),
                name: 'user_email',
                id: 'form_user_email',
                anchor: '75%',
                vtype: 'email',
                maxLength: 254
            }, {
                xtype: 'checkbox',
                name: 'user_disabled',
                id: 'form_user_disabled',
                fieldLabel: _('Disabled')
            }, {
                xtype: 'combo',
                fieldLabel: _('Auth via'),
                typeAhead: true,
                name: 'user_authsrc',
                id: 'form_user_authsrc',
                triggerAction: 'all',
                mode: 'local',
                store: new Ext.data.ArrayStore({
                    id: 0,
                    fields: ['user_authkey'],
                    data: authTypes
                }),
                listeners: {
                    change: function (cmp) {
                        var authMethod = cmp.getValue();
                        if (authMethod === 'internal' || authMethod === 'auth_key') {
                            setInternalFieldsEnabled(true);
                            return true;
                        }
                        setInternalFieldsEnabled(false);
                    }
                },
                valueField: 'user_authkey',
                displayField: 'user_authkey'
            }]
        }, {
            xtype: 'spacer',
            height: 25
        }, {
            xtype: 'fieldset',
            title: _('Change Password'),
            id: 'password_fieldset',
            items: [{
                xtype: 'textfield',
                fieldLabel: _('Password'),
                id: 'form_user_password',
                name: 'user_password',
                validator: function (value) {
                    var auth = Ext.getCmp('form_user_authsrc');
                    if (auth !== 'internal' && auth !== 'auth_key') {
                        return true;
                    }

                    if (Ext.getCmp('form_user_id').getValue() === 'new' && !value) {
                        return _("Please provide a password for this user");
                    }
                    return true;
                },
                inputType: 'password',
                minLength: 6,
                maxLength: 20,
                width: '200'
            }, {
                xtype: 'textfield',
                fieldLabel: _('Confirm password'),
                name: 'user_password_confirmed',
                id: 'form_user_password_confirmed',
                inputType: 'password',
                validator: function (value) {
                    var cmp_value = Ext.getCmp('form_user_password').getValue();
                    if (value !== cmp_value && cmp_value !== "") {
                        return _("The confirmed password doesn't match");
                    }
                    return true;
                },
                width: '200'
            }, {
                xtype: 'compositefield',
                items: [{
                    fieldLabel: _('Authkey for Api (optional)'),
                    id: 'form_user_authkey',
                    name: 'user_authkey',
                    readOnly: false,
                    minLength: 8,
                    maxLength: 40,
                    text: getApiKey(),
                    xtype: 'textfield',
                    width: 175,
                    regex: /[A-Za-z0-9]*/
                }, {
                    xtype: 'button',
                    iconCls: 'icinga-icon-arrow-refresh',
                    qtip: 'Create new api key',

                    handler: function () {
                        Ext.getCmp('form_user_authkey').setValue(getApiKey());
                    }
                }]
            }]
        }, {
            xtype: 'fieldset',
            title: _('Meta information'),
            items: [{
                xtype: 'displayfield',
                fieldLabel: _('Created'),
                name: 'user_created',
                id: 'form_user_created',
                preventMark: true,
                allowBlank: true,
                anchor: '95%'
            }, {
                xtype: 'displayfield',
                fieldLabel: _('Modified'),
                name: 'user_modified',
                id: 'form_user_modified',
                preventMark: true,
                allowBlank: true,
                anchor: '95%'
            }]
        }, {
            xtype: 'tabpanel',
            activeTab: 0,
            height: 400,
            enableTabScroll: true,
            items: [
                credentialView,
                roleView,
                hostgroupPrincipalsView,
                servicegroupPrincipalsView,
                customVariableView,
                servicePrincipalsView,
                hostPrincipalsView,
                userRestrictionFlagsView
            ],
            listeners: {
                tabchange: function (_this, panel) {
                    if (panel.updateView) {
                        panel.updateView();
                    }
                }
            },
            minHeight: 200,
            autScroll: true
        }];

    };
})();