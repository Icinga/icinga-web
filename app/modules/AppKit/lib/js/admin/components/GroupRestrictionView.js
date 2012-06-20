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
    AppKit.Admin.Components.GroupRestrictionView = Ext.extend(Ext.Panel, {
        layout: 'fit',
        autoScroll: true,
        constructor: function (cfg) {
            cfg.iconCls = 'icinga-icon-' + cfg.target;
            this.store = cfg.store;
            this.fieldTarget = cfg.target;
            cfg.title = Ext.util.Format.capitalize(cfg.target);
            cfg.items = [{
                xtype: 'editorgrid',
                autoScroll: true,
                store: cfg.store,
                sm: new Ext.grid.RowSelectionModel({
                    singleSelect: false
                }),
                emptyText: _('No ') + _(cfg.target) + (' restrictions set for this ') + _(cfg.type),
                columns: [{
                    header: _('Only show members of the following ') + _(cfg.target + 's') + ':',
                    dataIndex: cfg.target,
                    editor: (function () {
                        switch (cfg.target) {
                        case 'servicegroup':
                            return Icinga.Api.ServicegroupsComboBox;
                        case 'hostgroup':
                            return Icinga.Api.HostgroupsComboBox;
                        }
                    })()
                }],
                viewConfig: {
                    forceFit: true
                }
            }];
            Ext.Panel.prototype.constructor.call(this, cfg);
        },

        tbar: [{
            text: _('Add restriction'),
            iconCls: 'icinga-icon-add',
            handler: function (c) {
                var panel = c.ownerCt.ownerCt;

                var EmptyRecord = Ext.data.Record.create([{
                    'name': panel.fieldTarget
                }]);
                var rObj = {};

                rObj[panel.fieldTarget] = 'new restriction';
                panel.store.add(new EmptyRecord(rObj), true);
            }

        }, {
            text: _('Remove selected'),
            iconCls: 'icinga-icon-cancel',
            handler: function (c) {
                var panel = c.ownerCt.ownerCt;
                var list = panel.findByType('editorgrid')[0];
                panel.store.remove(list.getSelectionModel().getSelections());
            },
            scope: this
        }],

        selectValues: function (principals) {
            var Record = Ext.data.Record.create([{
                name: this.fieldTarget
            }]);
            this.store.removeAll();
            Ext.iterate(principals, function (p) {
                if (p.target.target_name === 'Icinga' + Ext.util.Format.capitalize(this.fieldTarget)) {
                    Ext.iterate(p.values, function (v) {
                        var rObj = {};
                        rObj[this.fieldTarget] = v.tv_val;
                        this.store.add(new Record(rObj));
                    }, this);
                }
            }, this);
        }
    });
})();