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
/*global Ext: false, Icinga: false, AppKit: false, _: false, Cronk: false */

Ext.ns('Cronk.util');

(function () {

    "use strict";

    /**
     * @class Cronk.util.Tabpanel
     * @extends Ext.ux.panel.DDTabPanel
     * <p>Tabpanel which holds cronks in, stateful
     * @param {Object} config The config object
     * @xtype cronk-control-tabs
     */
    Cronk.util.Tabpanel = function (config) {

        this.stateEvents = ['add', 'remove', 'tabchange', 'titlechange'];

        this.addEvents({
            /**
             * @event cronksloaded
             * Fires if all cronks from state are loadede and the
             * panel is ready initialized
             * @param {Cronk.util.Tabpanel}
             */
            cronksloaded: true
        });

        Cronk.util.Tabpanel.superclass.constructor.call(this, config);
    };

    Ext.extend(Cronk.util.Tabpanel, Ext.ux.panel.DDTabPanel, {

        URLTabData: false,
        minTabWidth: 125,
        tabWidth: 175,
        enableTabScroll: true,
        resizeTabs: true,

        /**
         * Array of component id's to
         * recreate right tab order.
         * @type {Array}
         */
        tabOrder: [],

        /**
         * Flag for editing and creating custom cronks
         * @type {Boolean}
         */
        customCronkCredential: false,

        /**
         * Number of maximum tabs allows in this panel
         * @type {Number}
         */
        maxTabs: 30,


        initComponent: function () {

            this.plugins = [
                new Cronk.util.CronkTabHelper(),

                new Ext.ux.TabScrollerMenu({
                    maxText: 15,
                    pageSize: 5
            })];

            Cronk.util.Tabpanel.superclass.initComponent.call(this);

            // This is missed globally
            this.on('beforeadd', function (tabPanel, component, index) {

                // Check before if we can add the tab
                if (this.items.getCount() >= this.maxTabs) {
                    AppKit.notifyMessage(_('Error'), String.format(_('Please close other tabs first (max = {0})'), this.maxTabs));
                    return false;
                }

                if (!Ext.isDefined(component.tabTip) && Ext.isDefined(component.title)) {
                    component.tabTip = component.title;
                }
            }, this);

            // Add handler to control specific removing
            // of components (especially for this tab
            // just added)
            this.on('beforeadd', function (tabPanel, component, index) {
                component.on('removed', this.handleTabRemove, this, {
                    single: true
                });
            }, this);

            // Fix tab order just before remove
            this.on('beforeremove', function(tabPanel, component) {
                this.fillTabOrder(null, component);
                return true;
            }, this);

            this.on('tabchange', this.fillTabOrder, this);

            // Check for cronks we should open
            // by urls
            this.opener = new Cronk.util.CronkOpener({
                panel: this,
                autoExecute: true
            });
        },

        fillTabOrder: function (tabs, changed) {
            this.tabOrder.push(changed.getId());

            // Sort of GV
            var lastItem = null;
            Ext.each(this.tabOrder, function (item, number) {

                // Item does not exist anymore
                if (this.items.get(item) === false) {
                    this.tabOrder.splice(number, 1);
                    return false;
                }

                // Doubled entry
                if (lastItem === item) {
                    this.tabOrder.splice(number -1, 1);
                }

                lastItem = item;
            }, this);
        },

        handleTabRemove: function (removec, ownerCt) {
            var index = 0;
            var sid = this.tabOrder.pop();

            while (index >= 0) {
                index = this.tabOrder.indexOf(removec.getId());
                if (index >= 0) {
                    this.tabOrder.splice(index, 1);
                }
            }

            if (sid === this.getActiveTab().getId()) {
                sid = this.tabOrder.pop();
            } else {
                return;
            }

            this.items.each(function (item, index, len) {
                if (item.getId() === sid) {
                    this.setActiveTab(item);
                    return false;
                }
            }, this);
        },

        setURLTab: function (params) {
            this.URLTabData = params;
        },

        getTabIndex: function (tab) {
            var i = -1;
            this.items.each(function (item, index, a) {
                i++;
                if (item === tab) {
                    return false;
                }
            });
            return i;
        },

        getActiveTabIndex: function () {
            return this.getTabIndex(this.getActiveTab());
        },

        getState: function () {

            var cout = {};

            this.items.each(function (item, index, l) {
                if (Cronk.Registry.get(item.getId())) {

                    // Copy reference
                    cout[item.getId()] = Ext.apply({}, Cronk.Registry.get(item.getId()));

                    // Local space is not for serializing
                    if (Ext.isDefined(cout[item.getId()].local)) {
                        delete(cout[item.getId()].local);
                    }

                    if (Ext.isDefined(item.iconCls)) {
                        cout[item.getId()].iconCls = item.iconCls;
                    }
                }
            });
            // AppKit.log("STATE", cout);
            var t = this.getActiveTab();
            return {
                cronks: cout,
                items: this.items.getCount(),
                active: ((t) ? t.getId() : null),
                tabOrder: this.tabOrder
            };
        },

        applyState: function (state) {
            (function () {
                if (state.cronks) {
                    // Adding all cronks
                    Ext.iterate(state.cronks, function (index, item, o) {
                        this.add(item);
                    }, this);

                    if (this.URLTabData) {

                        var tabPlugin = this.plugins;
                        if (Ext.isArray(this.plugins)) {
                            tabPlugin = null;
                            for (var i = 0; i < this.plugins.length; i++) {
                                if (this.plugins[i].createURLCronk) {
                                    tabPlugin = this.plugins[i];
                                    break;
                                }
                            }
                        }
                        if (tabPlugin) {
                            var index = this.add(tabPlugin.createURLCronk(this.URLTabData));
                            this.setActiveTab(index);
                        }
                    } else {
                        this.setActiveTab(state.active || 0);
                    }

                    if (Ext.isArray(state.tabOrder)) {
                        this.tabOrder = state.tabOrder;
                    }

                    this.getActiveTab().doLayout();
                }


                // Notify that all cronks are initialized
                this.fireEvent('cronksloaded', this);
            }).defer(5, this);

            return true;
        },

        listeners: {
            tabchange: function (tab) {
                var aTab = tab.getActiveTab();
                document.title = String.format('{0} - {1}', AppKit.util.Config.get('core.app_name'), aTab.title);
            }
        }
    });

    Ext.reg('cronk-control-tabs', Cronk.util.Tabpanel);
})();
