// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2013 Icinga Developer Team.
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
/*global Ext: false, Icinga: false, AppKit: false, _: false, Cronk: false, hex_hmac_rmd160: false */

Ext.ns('Cronk.grid');

(function () {

    "use strict";

    Cronk.grid.CommandHandler = function (meta) {
        this.toolbaritem = undefined;

        this.meta = undefined;

        this.command_options = {};

        this.url_info = undefined;
        this.url_send = undefined;

        this.grid = undefined;
        this.constructor.call(this, meta);
    };

    Cronk.grid.CommandHandler.prototype = {

        constructor: function (meta) {
            this.meta = meta;

            this.command_options = this.meta.template.option.commands;
        },

        setToolbarEntry: function (tb) {
            this.toolbaritem = tb;
        },

        setInfoUrl: function (url) {
            this.url_info = url;
        },

        setSendUrl: function (url) {
            this.url_send = url;
        },

        setGrid: function (grid) {
            this.grid = grid;
        },

        enhanceToolbar: function () {

            Ext.iterate(this.command_options.items, function (k, v) {
                var b = this.toolbaritem.menu.add({
                    text: v.title,
                    iconCls: v.icon_class || 'icinga-icon-bricks'
                });

                if (v.seperator && v.seperator === true) {
                    this.toolbaritem.menu.add('-');
                }

                b.on('click', function (b, e) {
                    this.showCommandWindow(k, v.title);
                }, this);

            }, this);
            if (Ext.isEmpty(this.command_options.items)) {
                this.toolbaritem.menu.add({
                    xtype: 'tbtext',
                    text: _("No commands are available for your user")

                });
            }

        },

        validSelection: function () {
            return this.grid.getSelectionModel().hasSelection();
        },

        getArrayComboField: function (o, oDef, data) {
            var orgName = oDef.name;
            delete oDef.name;

            Ext.apply(oDef, {
                store: new Ext.data.ArrayStore({
                    idIndex: 0,
                    fields: ['fId', 'fStatus', 'fLabel'],
                    data: data,
                    autoDestroy: true
                }),

                'name': '__return_value_combo',

                mode: 'local',
                typeAhead: true,
                triggerAction: 'all',
                forceSelection: true,


                fieldLabel: o.fieldLabel,

                valueField: 'fStatus',
                displayField: 'fLabel',

                hiddenName: o.fieldName
            });

            return new Ext.form.ComboBox(oDef);
        },

        getField: function (o) {

            var oDef = {
                fieldLabel: o.fieldLabel,
                name: o.fieldName,
                value: o.fieldValue,
                width: 200,
                allowBlank: !o.fieldRequired
            };

            var data = [];

            var form = o.form;

            switch (o.fieldType) {

            case 'notification_options':
                data = [
                    ['1', '0', _('(default) no option')],
                    ['2', '1', _('Broadcast')],
                    ['3', '2', _('Forced')],
                    ['4', '4', _('Increment current notification')]
                ];

                return this.getArrayComboField(o, oDef, data);


            case 'return_code_service':
                data = [
                    ['1', '0', _('OK')],
                    ['2', '1', _('Warning')],
                    ['3', '2', _('Critical')],
                    ['4', '3', _('Unknown')],
                    ['5', '255', _('Return code out of bounds')]
                ];

                return this.getArrayComboField(o, oDef, data);


            case 'return_code_host':
                data = [
                    ['1', '0', _('UP')],
                    ['2', '1', _('Down')],
                    ['3', '2', _('Unreachable')],
                    ['5', '255', _('Return code out of bounds')]
                ];

                return this.getArrayComboField(o, oDef, data);


            case 'hidden':
                return new Ext.form.Hidden(oDef);

            case 'date':
                oDef.format = 'Y-m-d H:i:s';

                if (!oDef.value) {
                    oDef.value = new Date();
                } else if (oDef.value.match(/^now[ \+\-]\d+$/)) {
                    oDef.value = new Date(new Date().getTime() + 1000 * Number(oDef.value.substr(3)));
                }
                return new Ext.form.DateField(oDef);


            case 'ro':
                oDef.readOnly = true;
                return new Ext.form.Field(oDef);


            case 'checkbox':
                Ext.apply(oDef, {
                    name: o.fieldName + '-group',
                    layout: 'column',
                    items: [{
                        xtype: 'radio',
                        boxLabel: _('Yes'),
                        inputValue: 1,
                        columnWidth: 0.5,
                        name: o.fieldName,
                        checked: o.fieldValue !== "true"
                    }, {
                        xtype: 'radio',
                        boxLabel: _('No'),
                        inputValue: 0,
                        name: o.fieldName,
                        columnWidth: 0.5,
                        checked: o.fieldValue === "true"
                    }]
                });
                if (o.fieldName === "fixed") {
                    AppKit.log(oDef);
                    var affectedForms = ['duration', 'duration-minute', 'duration-hour'];
                    for(var i=0;i<oDef.items.length;i++) {
                        oDef.items[i].listeners = {
                            check: function (checkedBox,val) {
                                AppKit.log("?");
                                for (var i = 0; i < affectedForms.length; i++) {
                                    var m = form.getForm().findField(affectedForms[i]);

                                    if (m) {
                                        m.setReadOnly((checkedBox.initialConfig.boxLabel === _('No')) ? !val : val);
                                        m.container.setVisible((checkedBox.initialConfig.boxLabel === _('No')) ? val : !val);
                                    }
                                }
                            }
                        }
                    };
                }
                return new Ext.Container(oDef);


            case 'duration':

                var dlistener = function (field, newValue, oldValue) {
                        var m = form.getForm().findField('duration-minute').getValue();
                        var h = form.getForm().findField('duration-hour').getValue();
                        form.getForm().findField('duration').setValue((m * 60) + (h * 3600));
                    };

                return new Ext.Container({
                    fieldLabel: o.fieldLabel,
                    defaults: {
                        style: {
                            padding: '0 5px 0 5px'
                        }
                    },
                    items: [{
                        xtype: 'numberfield',
                        name: o.fieldName + '-hour',
                        width: 30,
                        value: 2,
                        submitValue: false,
                        listeners: {
                            change: dlistener
                        }
                    }, {
                        xtype: 'label',
                        text: _('hours')
                    }, {
                        xtype: 'numberfield',
                        name: o.fieldName + '-minute',
                        width: 30,
                        value: 0,
                        submitValue: false,
                        listeners: {
                            change: dlistener
                        }
                    }, {
                        xtype: 'label',
                        text: _('minutes')
                    }, {
                        xtype: 'numberfield',
                        name: o.fieldName,
                        value: (3600 * 2),
                        minValue: 1,
                        width: 50,
                        readOnly: true,
                        style: {
                            background: '#00cc00'
                        }
                    }, {
                        xtype: 'label',
                        text: _('seconds')
                    }]
                });


            case 'textarea':

                Ext.apply(oDef, {
                    height: 120,
                    enableKeyEvents: true,
                    listeners: {

                    }
                });
                return new Ext.form.TextArea(oDef);


            case 'text':
                return new Ext.form.TextField(oDef);


            default:
                oDef.value = '(' + String.format(_('Unknown field type: {0}'), o.fieldType) + ')';
                return new Ext.form.DisplayField(oDef);
            }
        },

        getSelection: function () {

            var r = [];

            Ext.each(this.grid.getSelectionModel().getSelections(), function (item, index, arry) {
                var td = {};

                for (var skey in this.command_options.source) {

                    if (item.data[this.command_options.source[skey]]) {
                        td[skey] = item.data[this.command_options.source[skey]];
                    }

                }

                r.push(td);

            }, this);

            return r;
        },

        // ** MAYBE LATER **    
        //  modifyForm : function(command, form) {
        //      var p = form.getForm();
        //      
        //      if (command.match(/SCHEDULE.+DOWNTIME/)) {
        //          
        //          var f = p.findField('fixed-group');
        //          
        //          var d = p.findField('duration');
        //          
        //          if (f && d) {
        //          
        //              f.on('change', function(group, radio) {
        //                  if (radio.name=='fixed' && radio.inputValue==0) {
        //                      d.allowBlank = false;
        //                  }
        //                  else if (radio.name=='fixed' && radio.inputValue==1) {
        //                      d.allowBlank = true;
        //                  }
        //                  p.clearInvalid();
        //              });
        //          
        //          }
        //          
        //      }
        //  },
        showCommandWindow: function (command, title) {

            if (this.validSelection() !== true) {
                AppKit.notifyMessage(_('Command'), _('Selection is missing'));
                return;
            }

            Ext.Ajax.request({
                url: String.format(this.url_info, command),
                scope: this,

                success: function (response, opts) {

                    var o = Ext.decode(response.responseText);

                    var oWin = new Ext.Window({
                        title: String.format(_('{0} ({1} items)'), title, this.grid.getSelectionModel().getCount()),
                        width: 380,
                        autoDestroy: true,
                        autoHeight: true,
                        closable: true,
                        modal: true,
                        defaultType: 'field',
                        defaults: {
                            padding: 5
                        },
                        buttons: [{
                            text: _('OK'),
                            iconCls: 'icinga-icon-accept',
                            handler: function (b, e) {
                                oForm.getForm().doAction(oFormAction);
                            }
                        }, {
                            text: _('Cancel'),
                            iconCls: 'icinga-icon-cross',
                            handler: function (b, e) {
                                oWin.close();
                            }
                        }]
                    });

                    // This fixes the webkit (safari, chrome) width issue ...
                    oWin.on('afterrender', function () {
                        this.syncSize();
                        this.syncShadow();
                    }, oWin, {
                        delay: 40,
                        single: true
                    });

                    var oForm = new Ext.form.FormPanel({
                        border: false,
                        bodyStyle: 'padding: 5px 5px 5px 5px',

                        defaults: {
                            border: false,
                            msgTarget: 'side'
                        }
                    });

                    oForm.getForm().on('beforeaction', function (f, a) {
                        if (!f.isValid()) {
                            return false;
                        }
                        var selection = Ext.util.JSON.encode(this.getSelection());

                        // Auth for the command, key is the timekey given
                        // by the json request (Timekey is valid for 5 minutes)
                        // The complete key is valid for the command and the selection
                        var h_data = command + '-' + selection;
                        h_data += '-' + Ext.util.JSON.encode(f.getValues(false));
                        // simplify string


                        var h_key = o.tk;
                        var h_auth = hex_hmac_rmd160(h_key,command);

                        a.options.params.auth = h_auth;
                        a.options.params.selection = selection;

                        oWin.disable();

                        return true;

                    }, this);

                    var oFormAction = new Ext.form.Action.JSONSubmit(oForm.getForm(), {
                        clientValidation: true,

                        url: String.format(this.url_send, command),

                        // The name of the json store
                        json_namespace: 'data',

                        params: {},

                        failure: function (f, a) {
                            if (a.failureType !== Ext.form.Action.CLIENT_INVALID) {
                                var e = Ext.util.JSON.decode(a.response.responseText);
                                var error = e.errors['default'];

                                oWin.close();

                                AppKit.notifyMessage(_('Error sending command'), _('Could not send the command, please examine the logs!'));

                                Ext.Msg.show({
                                    title: _('Error sending command'),
                                    msg: error,
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
                        },
                        success: function (f, a) {
                            oWin.close();
                            AppKit.notifyMessage(_('Command sent'), String.format(_('{0} command was sent successfully!'), command));
                        }
                    });

                    var bAdd = false;

                    Ext.each(o.fields, function (item, index, arry) {

                        if (this.command_options.source[item]) {
                            return;
                        }

                        if (item === "author") {
                            this.command_options.predefined[item] = AppKit.getPreferences().author_name;
                        }
                        var f = this.getField({
                            fieldLabel: item,
                            fieldName: item,
                            fieldType: o.types[item].type,
                            fieldValue: this.command_options.predefined[item] || o.types[item].defaultValue || "",
                            fieldRequired: o.types[item].required == "true",
                            form: oForm
                        });

                        if (f) {
                            bAdd = true;
                            oForm.add(f);
                        }

                    }, this);

                    if (bAdd === false) {
                        oForm.add({
                            xtype: 'label',
                            text: _('This command will be sent to all selected items')
                        });
                    }

                    // this.modifyForm(command, oForm);
                    oWin.add(oForm);

                    oWin.render(Ext.getBody());

                    if (this.command_options.predefined.fixed === 1) {
                        var fdur = oForm.getForm().findField('duration');
                        if (fdur) {
                            fdur.setReadOnly(true);
                            fdur.container.hide();
                        }
                    }

                    oWin.show();

                    oWin.setWidth(oWin.getWidth() + 50);
                    oWin.setHeight(oWin.getHeight() + 5);

                    oWin.doLayout();
                }
            });
        }

    };

})();
