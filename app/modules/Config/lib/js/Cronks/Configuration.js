// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-present Icinga Developer Team.
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

Ext.ns('Icinga.Configuration.Cronks');

/*global Ext: false, Icinga: false, AppKit: false, _: false, Cronk: false */
Ext.ns('Icinga.Cronks.System');

(function () {

    "use strict";

    /*
     * Cronk Component which holds implementation of
     * a grid, showing agavi configuration settings
     */
    Icinga.Configuration.Cronks.Viewer = Ext.extend(Ext.Panel, {
        providerUrl: null,
        layout: 'fit',
        initComponent: function () {

            this.store = new Ext.data.JsonStore({
                autoDestroy: true,
                url: this.providerUrl
            });

            this.grid = new Ext.grid.GridPanel({
                store: this.store,

                colModel: new Ext.grid.ColumnModel({
                    defaults: {
                        sortable: true
                    },

                    columns: [{
                        header: 'Key',
                        dataIndex: 'key',
                        width: 50
                    }, {
                        header: 'Type',
                        dataIndex: 'key',
                        width: 18,
                        renderer: {
                            fn: this.typeColumnRenderer,
                            scope: this
                        }
                    }, {
                        header: 'Value',
                        dataIndex: 'value',
                        xtype: 'templatecolumn',
                        tpl: '<div style="-moz-user-select: all; '
                        + '-khtml-user-select: text; white-space: pre;">'
                        + '<code>{value}</code></div>'
                    }]
                }),

                viewConfig: {
                    forceFit: true
                }
            });

            this.items = this.grid;

            this.tbar = this.buildSearchBar();
            this.bbar = this.buildCounterBar();

            this.store.load();

            Icinga.Configuration.Cronks.Viewer
                .superclass.initComponent.call(this);
        },

        doSearch: function (val) {
            val = val || this.textField.getValue();

            if (val) {
                val = new RegExp('^' + val);
                this.store.filter('key', val);
            } else {
                this.store.clearFilter();
            }
        },

        buildCounterBar: function () {
            this.counterItem = new Ext.Toolbar.TextItem({
                tpl: '{0} items'
            });

            this.store.on('datachanged', function () {
                this.counterItem.update([this.store.data.items.length]);
            }, this);

            return [this.counterItem];
        },

        buildSearchBar: function () {
            this.textField = new Ext.form.TextField({
                name: 'config-query',
                enableKeyEvents: true
            });

            this.textField.on('keyup', this.doSearch.createDelegate(this, [], false));

            this.searchButton = new Ext.Button({
                iconCls: 'icinga-action-icon-search',
                width: 20,
                handler: this.doSearch.createDelegate(this, [], false)
            });

            this.reloadButton = new Ext.Button({
                iconCls: 'icinga-action-refresh',
                toolTip: 'Press to reload remote data',
                handler: function () {
                    this.store.reload();
                    this.textField.setValue("");
                },
                scope: this
            });

            this.clearButton = new Ext.Button({
                iconCls: 'icinga-action-icon-cancel',
                toolTip: 'Remove filters and clear text field',
                handler: function () {
                    this.store.clearFilter();
                    this.textField.setValue("");
                },
                scope: this
            });

            this.helpButton = new Ext.Button({
                iconCls: 'icinga-action-icon-help',
                toolTip: 'Press for ...',
                handler: function () {
                    Ext.MessageBox.show({
                        buttons: Ext.MessageBox.OK,
                        closable: true,
                        icon: Ext.MessageBox.INFO,
                        modal: true,
                        title: 'Icinga-web settings cronk',
                        msg: 'This cronk is only for viewing configured'
                        + '<br />values. Never was intended to change'
                        + ' settings at runtime.<br /><br />'
                        + 'The searchbox supports JS regular expressions'
                        + 'to<br />search but always starts at beginning,'
                        + ' e.g.:<br />".*context" or ".*api"'
                    });
                }
            });

            return [
                this.clearButton, 
                this.textField, 
                this.searchButton, 
                this.reloadButton, 
                this.helpButton
            ];
        },

        typeColumnRenderer: function (value, meta) {
            var type = 'misc';
            var bgcolor = '#f1f1f1';
            var fgcolor = '#333';

            if (value.match(/^core/)) {
                type = 'agavi/core';
                bgcolor = '#cc0000';
                fgcolor = '#fff';
            } else if (value.match(/^agavi/)) {
                type = 'agavi/lib';
            } else if (value.match(/^modules/)) {
                type = 'module';
                bgcolor = '#ffff00';
            } else if (value.match(/^org\.icinga/)) {
                type = 'icinga';
                bgcolor = '#00cc00';
            } else if (value.match(/^action/)) {
                type = 'agavi/actions';
            }

            meta.attr = String.format(
                'style="color: {0}; background-color: {1};"', 
                fgcolor,
                bgcolor
            );

            return type;
        }
    });

})();