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

var AppKit, _ = function () {
        return Array.prototype.join.call(arguments, ' ');
    };

(function () {

    var _APPKIT;

    AppKit = new(_APPKIT = Ext.extend(Ext.util.Observable, function () {

        // - Private
        var stateInitialData = null,

            stateProvider = null,

            taskRunner = null,

            growlStackElement = null,

            growlTemplate = new Ext.Template([
                '<div class="growl-msg-message floatbox">', 
                '<div class="head-frame clearfix">', 
                '<div class="icon {icon}"></div>', 
                '<div class="head">{header}</div>', 
                '</div>', 
                '<div class="body">{message}</div>', '</div>'
            ]),

            userPreferences = {},

            initEnvironment = function () {
                var me = AppKit;

                Ext.BLANK_IMAGE_URL = me.c.path + '/images/ajax/s.gif';
                Ext.chart.Chart.CHART_URL = null; // force URL to be null
                                                  // ExtJS loads a web url by default
                Ext.QuickTips.init();
                growlStack();

                stateProvider = new Ext.ux.state.HttpProvider({
                    url: String.format(me.c.path + '/modules/appkit/ext/applicationState'),
                    id: 1,
                    readBaseParams: {
                        cmd: 'read'
                    },
                    saveBaseParams: {
                        cmd: 'write'
                    }
                });

                Ext.state.Manager.setProvider(stateProvider);

                if (stateInitialData) {
                    stateProvider.initState(stateInitialData);
                }

                AppKit.util.loginWatchdog();

                me.ready = true;
                me.fireEvent('appkit-ready');

                return true;
            },

            growlStack = function () {
                if (!growlStackElement) {
                    growlStackElement = Ext.DomHelper.insertFirst(Ext.getBody(), {
                        id: 'growl-msg-stack'
                    }, true);
                }

                growlStackElement.alignTo(Ext.getDoc(), 'tr-tr', [-18, 10]);

                return growlStackElement;
            };

        // - Public
        return {

            setPreferences: function (o) {
                if (!Ext.isEmpty(o)) {
                    userPreferences = Ext.apply({}, o);
                    return true;
                }

                return null;
            },

            getPreferences: function () {
                return userPreferences;
            },

            getPrefVal: function (key, def) {
                if (key in userPreferences) {
                    return userPreferences[key];
                }

                if (!Ext.isEmpty(def)) {
                    return def;
                }

                return null;
            },

            constructor: function () {
                this.events = {};
                this.listeners = {};

                this.addListener('appkit-statedata', this.onStateData, this, {
                    single: true
                });

                this.addEvents({
                    'appkit-statedata': true,
                    'appkit-ready': true
                });

                _APPKIT.superclass.constructor.call(this);

                this.c = {};

                this.ready = false;

            },

            onStateData: function (d) {
                stateInitialData = d;
                initEnvironment();
                return true;
            },

            /**
             * Set the initial application state
             * before init!
             */
            setInitialState: function (s) {
                var me = this;
                return me.fireEvent('appkit-statedata', s);
            },

            /**
             * General log implementation
             */
            log: function () {
                try {
                    if (!Ext.isIE && console) {
                        if (typeof (console.log) === 'function') console.log[console.firebug ? 'apply' : 'call'](console, Array.prototype.slice.call(arguments));
                    }
                } catch (e) { /*.No logging.*/
                }
            },
            /**
             * Sets the window location
             */
            changeLocation: function (sUrl) {
                // Just simple ;-)
                window.location.href = sUrl;
                return true;
            },


            pageLoadingMask: function (time, remove) {
                remove = (remove || false);
                time = (time || 2000);

                var ids = ['icinga-portal-loading-mask', 'icinga-portal-loading'];

                if (remove) {
                    Ext.iterate(ids, function (v) {
                        Ext.get(v).fadeOut({
                            remove: true
                        });
                    });
                } else {
                    Ext.iterate(ids, function (v) {
                        Ext.DomHelper.append(Ext.getBody(), {
                            tag: 'div',
                            id: v
                        });
                    });

                    if (time > 0) {
                        // setTimeout(AppKit.pageLoadingMask.createDelegate(this, [0, true]), time);
                        var task = new Ext.util.DelayedTask(this.pageLoadingMask.createCallback(0, true), AppKit);
                        task.delay(time);
                    }
                }
            },

            growlPopupBox: function (message, title, icon) {

                icon = icon || "info";

                if (icon === "info") {
                    icon = "icinga-icon-information";
                }

                var box = growlTemplate.append(growlStack(), {
                    header: title,
                    message: message,
                    icon: icon
                }, true);
                return box.boxWrap('x-icinga-growlbox');
            },

            notifyMessage: function (title, msg) {
                var la = Ext.toArray(arguments);
                var title = la.shift();

                var c = {
                    waitTime: 3
                };

                if (Ext.isObject(la[la.length - 1])) {
                    Ext.apply(c, la.pop());
                };

                var nm = String.format.apply(this, la);

                var ele = this.growlPopupBox(nm, title);

                ele.slideIn('t').pause(c.waitTime).ghost('t', {
                    remove: true
                });
            },

            getTr: function () {
                if (!taskRunner) {
                    taskRunner = new Ext.util.TaskRunner();
                };
                return taskRunner;
            },

            onReady: function (fn, scope) {
                var me = this;
                if (Ext.isFunction(fn)) {
                    if (this.ready == true) {
                        fn.call(scope || fn);
                    } else {
                        this.on('appkit-ready', fn, scope || fn, {
                            single: true
                        });
                    }
                }
            }
        }

    }()));
})();

Ext.ns('AppKit.lib', 'AppKit.util');
