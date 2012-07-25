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

        /**
         * @cfg {Boolean} customCronkCredential
         * Credential for creating or modifying custom cronks (SaveAs ...)
         */
        customCronkCredential: false,

        /**
         * Constructor
         * @param {Object} config
         */
        constructor: function (config) {
            Icinga.Cronks.System.CronkPortal
                .superclass.constructor.call(this, config);
        },
        
        /**
         * Control our mask if we loading components
         * @param {Boolean} show Display or hide
         */
        loadingMask: function(show) {
            if (show===true) {
                if (Ext.isEmpty(this.loadingMaskElement)) {
                    this.loadingMaskElement = Ext.DomHelper.insertFirst(Ext.getBody(), {
                        id: "icinga-portal-loading-mask",
                        tag: "div",
                        children: [{
                            id: "icinga-portal-loading",
                            tag: "div"
                        }, {
                            id: "icinga-portal-loading-text",
                            tag: "div",
                            html: "loading ... 0%"
                        }]
                    });
                    
                    Ext.get(this.loadingMaskElement).on("click", function() {
                        this.loadingMask(false);
                    }, this);
                }
            } else if (show === false && !Ext.isEmpty(this.loadingMaskElement)) {
                Ext.get(this.loadingMaskElement).fadeOut({
                    endOpacity: 0,
                    easing: 'easeOut',
                    duration: 0.5,
                    remove: true,
                    useDisplay: true
                });
            }
        },
        
        /**
         * Updates the status text on the loading mask
         * @param {Number/String} percent
         */
        updateLoadingText: function(percent) {
            if (Ext.isNumber(percent)) {
                if (percent > 100) {
                    percent=100.00;
                }
                percent = Ext.util.Format.number(percent, '0.00') + "%";
            }
            
            var text=_("loading ... {0}");
            var ele = Ext.get("icinga-portal-loading-text");
            
            if (ele) {
                ele.update(String.format(text, percent));
            }
        },
        
        /**
         * Watchdog tracking requests and assume when
         * initial start sequence is over
         */
        loadingWatchDog: function() {
            var tr = AppKit.getTr();      // Task runner
            var rc = 0;                   // Round counter
            var reqc = 0;                 // Current request counter
            var reqs = 0;                 // Summary request counter
            var maxrequests=18;           // Average requests per start (const)
            var interval = 50;            // Interval to check (const)
            var usec = function() {      // Shortcut to retrieve micro seconds
                var dt = new Date();
                return dt.getTime();
            };
            var tstamp = usec();           // Current timestamp 
            var watchdogTask = {};         // Pre declared task
            
            var fi=function() {           // request increase function
                reqc++;
                reqs++;
            };
            
            var fd=function() {           // request decrease function
                reqc--;
            };
            
            Ext.Ajax.on("requestexception", fd);
            Ext.Ajax.on("requestcomplete", fd);
            Ext.Ajax.on("beforerequest", fi);
            
            watchdogTask = {
                    interval: interval,
                    scope: this,
                    run: function() {
                        rc++;
                        
                        var percent = 
                            this.updateLoadingText((100/maxrequests)*reqs);
                        
                        // Compensate more requests
                        if (percent>90) {
                            maxrequests+= 10;
                        }
                        
                        // Compensate less requests
                        if (percent < 60 && rc%10===0) {
                            reqs += 10;
                        }
                        
                        if (Ext.Ajax.isLoading() === true) {
                            tstamp = usec();
                        }
                        
                        /*
                         * 1. Check if all pending requests done within 
                         *    our timerange
                         * 
                         * 2. Check if the whole process does not need more
                         *    then 6 seconds 
                         */
                        if ((reqc<=0 && (usec()-tstamp)>300) ||
                                (rc*interval)>=6000) {
                            
                            this.updateLoadingText(100);
                            this.loadingMask(false);
                            
                            // Unregister
                            tr.stop(watchdogTask);
                            
                            Ext.Ajax.un("beforerequest", fi);
                            Ext.Ajax.un("requestcomplete", fd);
                            Ext.Ajax.un("requestexception", fd);
                            
                            AppKit.log("Portal/Requests", reqs);
                        }
                    }
            };
            
            AppKit.log("Portal/Starting");
            AppKit.log("Portal/Starting", "Click on the mask to remove");
            
            this.loadingMask(true);
            tr.start(watchdogTask);
        },

        /**
         * Building our layout of cronk components
         */
        initComponent: function () {
            Icinga.Cronks.System.CronkPortal.superclass.initComponent.call(this);
            
            this.loadingWatchDog();
            
//            if (this.loadingMask) {
//                AppKit.pageLoadingMask(this.loadingMask);
//            }
            

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