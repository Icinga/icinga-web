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

/*global Ext: false, Icinga: false, AppKit: false, _: false */
Ext.ns('Icinga.Cronks.System');

(function () {
    "use strict";

    Icinga.Cronks.System.CronkPortal = Ext.extend(Ext.Panel, {

        layout: 'border',
        border: false,
        id: 'view-container',

        defaults: {
            border: false,
            layout: 'fit'
        },
        style: {
            padding: '0px 5px 5px 5px'
        },

        loadingMask: null,

        // Credential for creating or modifying custom cronks (SaveAs ...)
        customCronkCredential: false,

        constructor: function (config) {
            Icinga.Cronks.System.CronkPortal.superclass.constructor.call(this, config);
        },

        initComponent: function () {
            Icinga.Cronks.System.CronkPortal.superclass.initComponent.call(this);

            if (this.loadingMask) {
                AppKit.pageLoadingMask(this.loadingMask);
            }

            this.add([{
                region: 'north',
                id: 'north-frame',

                layout: 'hbox',
                layoutConfig: {
                    align: 'stretch',
                    pack: 'start'
                },

                padding: 10,
                height: 72,

                defaults: {
                    border: false
                },

                items: [{
                    xtype: 'cronk',
                    crname: 'icingaOverallStatus',
                    width: 800
                }, {
                    xtype: 'cronk',
                    crname: 'icingaMonitorPerformance',
                    width: 350
                }]

            }, {
                region: 'center',
                id: 'center-frame',
                layout: 'fit',
                items: {
                    xtype: 'cronk-control-tabs',
                    id: 'cronk-tabs',
                    border: false,
                    stateful: true,
                    stateId: 'cronk-tab-panel',
                    customCronkCredential: this.customCronkCredential
                },
                border: true,
                margins: '0 0'
            }, {
                region: 'west',
                id: 'west-frame',
                layout: 'fit',
                autoScroll: true,
                split: true,
                minSize: 200,
                maxSize: 400,
                width: 280,
                collapsible: true,
                stateful: true,
                border: true,
                stateId: 'west-frame',

                items: {
                    xtype: 'cronk',
                    crname: 'crlist',
                    border: false
                }
            }]);
        }

    });

})();