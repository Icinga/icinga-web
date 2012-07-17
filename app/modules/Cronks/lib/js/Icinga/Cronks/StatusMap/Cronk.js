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
/*global Ext: false, Icinga: false, AppKit: false, _: false, Cronk: false, $jit: false */

Ext.ns('Icinga.Cronks.StatusMap');

(function () {

    "use strict";

    Icinga.Cronks.StatusMap.Cronk = Ext.extend(Ext.Panel, {

        url: null,
        rgraph: null,
        refreshTime: 300,
        connection: 'icinga',


        constructor: function (config) {
            Icinga.Cronks.StatusMap.Cronk.superclass.constructor.call(this, config);

        },

        initComponent: function () {
            this.tbar = [{
                xtype: 'button',
                iconCls: 'icinga-icon-application-edit',
                text: _('Settings'),
                menu: [{
                    text: _('Autorefresh'),
                    xtype: 'menucheckitem',
                    checkHandler: function (item, state) {
                        var tr = AppKit.getTr();
                        if (state === true) {
                            tr.start(this.refreshTask);
                        } else if (state === false) {
                            tr.stop(this.refreshTask);
                        }
                    },
                    scope: this
                }]
            }];

            Icinga.Cronks.StatusMap.Cronk.superclass.initComponent.call(this);

            this.rgraph = new Icinga.Cronks.StatusMap.RGraph({
                url: this.url,
                parentId: this.getId()
            });

            this.refreshTask = {
                run: this.rgraph.reloadTree.createDelegate(this.rgraph),
                interval: (this.refreshTime * 1000)
            };
        },

        getRGraph: function () {
            return this.rgraph;
        }
    });

})();