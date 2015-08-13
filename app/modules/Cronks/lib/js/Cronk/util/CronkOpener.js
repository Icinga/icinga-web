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

Ext.ns('Cronk.util');

(function () {
    "use strict";

    /**
     * @class Cronk.util.CronkOpener
     * @extends Ext.util.Observable
     * <p>This object gets a tabpanel as parameter and parses the url. If
     * matched for a cronk, create a new cronk and add them to the panel
     * @param {Object} c configuration object
     */
    Cronk.util.CronkOpener = Ext.extend(Ext.util.Observable, {

        /**
         * @cfg {Boolean} autoExecute
         * Start the chain to check if we can add the cronk from url
         * If this is false you have to call
         * {@link Cronk.util.CronkOpener#canApply} and
         * {@link Cronk.util.CronkOpener#execute} manually
         *
         */
        autoExecute: false,

        /**
         * @cfg {Cronk.util.Tabpanel} panel
         * The panel to work on. Must support the cronksloaded event
         */
        panel: null,


        /**
         * @cfg {String} cronkuid
         * Cronk UID to open
         */
        cronkuid: null,

        /**
         * @cfg {Boolean} reset
         * Reset the tabs before we open a new cronk
         */
        reset: false,

        constructor: function (c) {
            this.addEvents({
                beforeopen: true,
                open: true
            });

            Cronk.util.CronkOpener.superclass.constructor.call(this, c);

            Ext.apply(this, c);

            if (Ext.isEmpty(this.panel)) {
                throw ("panel property is empty!");
            }

            this.parseUrl();

            // Start the flow if wanted (autoExecute)
            if (this.autoExecute === true) {
                if (this.canApply()) {
                    this.execute();
                }
            }
        },

        // PRIVATE
        parseUrl: function () {
            if (location.href.match(/modules\/cronks\/open\/([\w\d_\.\-\s]+)(\?(.+))?$/)) {
                if (RegExp.$1) {
                    this.setCronkUid(RegExp.$1);
                }

                var params = Ext.urlDecode(RegExp.$3);

                if (!Ext.isEmpty(params.reset) && params.reset === 'true') {
                    this.setReset(true);
                }
            }
        },

        /**
         * Setter for cronkuid
         * @param {String} cronkuid
         */
        setCronkUid: function (cronkuid) {
            this.cronkuid = cronkuid;
        },

        /**
         * Getter for cronkuid
         *
         * @return {String}
         */
        getCronkUid: function () {
            return this.cronkuid;
        },

        /**
         * Setter for reset flag. If the method
         * is called without argument, the flag will be set
         * to false
         * @param {Boolean} flag
         */
        setReset: function (flag) {
            flag = Boolean(flag) || false;
            this.reset = flag;
        },

        /**
         * Getter for reset
         * @return {Boolean}
         */
        getReset: function () {
            return this.reset;
        },

        /**
         * Check if we're ready to add a cronk
         * @return {Boolean}
         */
        canApply: function () {
            if (this.getCronkUid() !== null && this.panel) {
                return true;
            }

            return false;
        },

        /**
         * Add the cronk to the panel
         */
        execute: function () {

            var execFunction = function () {
                    if (Cronk.Inventory.getCount()) {
                        AppKit.getTr().stop(this.task);


                        var cronkConfig = Cronk.Inventory.get(this.getCronkUid());

                        var cronk = {
                            xtype: 'cronk',
                            iconCls: Cronk.getIconClass(cronkConfig.image),
                            title: cronkConfig.name,
                            crname: cronkConfig.cronkid,
                            closable: true,
                            params: Ext.apply({}, cronkConfig['ae:parameter'], {
                                module: cronkConfig.module,
                                action: cronkConfig.action
                            })
                        };

                        var re = Cronk.util.InterGridUtil.gridFilterLink(cronk, {});

                        this.panel.resumeEvents();

                        this.fireEvent('open', re);

                    }
                };

            this.task = {
                run: execFunction,
                interval: 300,
                scope: this
            };

            this.panel.on('cronksloaded', function () {

                this.panel.suspendEvents();

                if (this.getReset() === true) {
                    // Remove all and suppress events
                    this.panel.removeAll(true);
                }

                if (this.fireEvent('beforeopen') === true) {
                    AppKit.getTr().start(this.task);
                }
            }, this, {
                single: true
            });
        }
    });

})();
