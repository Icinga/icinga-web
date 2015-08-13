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

/*global Ext: false, Icinga: false, AppKit: false, _: false, Cronk: false */

Ext.ns('Icinga.Cronks.util');

(function() {

    "use strict";

    /*
     * The form to create a new catecory
     */
    Icinga.Cronks.util.CategoryEditorForm = Ext.extend(Ext.Window, {

        layout: 'fit',
        width: 350,
        height: 180,
        resizable: false,
        closeable: false,
        title: _('Add new category'),
        iconCls: 'icinga-icon-category',
        modal: true,


        constructor: function (config) {

            this.addEvents({
                submitForm: true
            });

            Icinga.Cronks.util.CategoryEditorForm.superclass.constructor.call(this, config);
        },

        initComponent: function () {
            Icinga.Cronks.util.CategoryEditorForm.superclass.initComponent.call(this);

            this.addButton({
                iconCls: 'icinga-action-icon-ok',
                text: _('OK'),
                handler: function (b, e) {
                    this.doSubmit();
                },
                scope: this
            })

            this.addButton({
                iconCls: 'icinga-action-icon-cancel',
                text: _('Cancel'),
                handler: function (b, e) {
                    this.close();
                },
                scope: this
            });

            this.form = this.add({
                border: false,
                xtype: 'form',
                padding: 10,
                defaults: {
                    msgTarget: 'side',
                    labelWidth: 75,
                    allowBlank: false,
                    width: 200
                },
                items: [{
                    xtype: 'textfield',
                    name: 'catid',
                    fieldLabel: _('Category ID'),
                    vtype: 'alphanum'
                }, {
                    xtype: 'textfield',
                    name: 'title',
                    fieldLabel: _('Title')
                }, {
                    xtype: 'radiogroup',
                    fieldLabel: _('Visibility'),
                    items: [{
                        xtype: 'radio',
                        name: 'visible',
                        boxLabel: _('false'),
                        value: 0
                    }, {
                        xtype: 'radio',
                        name: 'visible',
                        boxLabel: _('true'),
                        value: 1,
                        checked: true
                    }]
                }, {
                    xtype: 'textfield',
                    name: 'position',
                    fieldLabel: _('Position'),
                    maskRe: new RegExp(/[0-9]+/),
                    value: 0
                }]
            });

            this.doLayout();
        },

        doSubmit: function () {
            var form = this.form.getForm();
            if (form.isValid()) {
                var values = form.getValues();
                if (this.fireEvent('submitForm', this, values, form) === true) {
                    this.close();
                }
            }
        }
    });

})();
