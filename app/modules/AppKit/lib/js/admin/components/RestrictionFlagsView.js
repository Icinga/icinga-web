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
    AppKit.Admin.Components.RestrictionFlagsView = Ext.extend(Ext.Panel, {
        roleFlags: [{
            icon: 'icinga-icon-role-delete',
            principal: 'IcingaCommandRo',
            id: 'flag-command-only',
            text: _('Disallow sending of commands')
        }, {
            icon: 'icinga-icon-group',
            principal: 'IcingaContactgroup',
            id: 'flag-contacts-only',
            text: _('Only show items that contain a contact with this name' + ' in their contactgroup definitions')
        }, {
            icon: 'icinga-icon-group',
            principal: 'IcingaCommandRestrictions',
            id: 'flag-commands-restricted',
            text: _('Don\'t allow critical commands (like disabling host checks)')
        }],
        constructor: function (cfg) {
            var items = [];
            for (var i = 0; i < this.roleFlags.length; i++) {
                var flag = this.roleFlags[i];
                items.push(new Ext.form.Checkbox({
                    xtype: 'checkbox',
                    boxLabel: flag.text,
                    id: flag.id,
                    name: flag.principal
                }));
            }
            cfg.items = {
                xtype: 'container',
                layout: 'form',

                border: false,
                items: items
            };
            cfg.tbar = new Ext.Toolbar({
                items: [{
                    xtype: 'tbtext',
                    text: _('You can define additional restrictions for this ') + _(cfg.type) + _(' here')
                }]
            });

            Ext.Panel.prototype.constructor.call(this, cfg);
        },
        title: _('Other restrictions'),
        iconCls: 'icinga-icon-lock',
        padding: 10,
        layout: 'fit',
        selectedValue: [],
        selectValues: function (principals) {
            var checkboxes = this.findByType('checkbox');
            Ext.iterate(checkboxes, function (checkbox) {
                checkbox.reset();
                Ext.iterate(principals, function (p) {
                    if (p.target.target_name === checkbox.getName()) {
                        checkbox.setValue(true);
                    }
                });
            }, this);
        }

    });

})();