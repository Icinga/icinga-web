// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2014 Icinga Developer Team.
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
/*global Ext: false, Icinga: false, AppKit: false, _: false, Cronk: false */ (function () {

    "use strict";

    /*
     * By Jake Knerr - Copyright 2010 - supersonicecho@gmail.com
     * 
     * Version 1.0
     * 
     * LICENSE
     * GPL v3
     * 
     */

    Ext.ns('Ext.ux');

    Ext.ux.SlidingTabPanel = Ext.extend(Ext.TabPanel, {

        initTab: function (item, index) {
            Ext.ux.SlidingTabPanel.superclass.initTab.call(this, item, index);
            var p = this.getTemplateArgs(item);
            if (!this.slidingTabsID) {
                this.slidingTabsID = Ext.id(); // Create a unique ID for this tabpanel
            }
            new Ext.ux.DDSlidingTab(p, this.slidingTabsID, {
                tabpanel: this // Pass a reference to the tabpanel for each dragObject
            });
        }

    });

    Ext.ux.DDSlidingTab = Ext.extend(Ext.dd.DDProxy, {

        // Constructor
        constructor: function () {
            Ext.ux.DDSlidingTab.superclass.constructor.apply(this, arguments);
            this.setYConstraint(0, 0, 0); // Lock the proxy to its initial Y coordinate

            // Create a convenient reference to the tab's tabpanel
            this.tabpanel = this.config.tabpanel;

            // Set the slide duration
            this.slideDuration = this.tabpanel.slideDuration;
            if (!this.slideDuration) {
                this.slideDuration = 0.1;
            }
        }

        // Pseudo Private Methods
        ,
        handleMouseDown: function (e, oDD) {
            if (this.primaryButtonOnly && e.button != 0) return;
            if (this.isLocked()) return;
            this.DDM.refreshCache(this.groups);
            var pt = new Ext.lib.Point(Ext.lib.Event.getPageX(e), Ext.lib.Event.getPageY(e));
            if (!this.hasOuterHandles && !this.DDM.isOverTarget(pt, this)) {} else {
                if (this.clickValidator(e)) {
                    this.setStartPosition(); // Set the initial element position
                    this.b4MouseDown(e);
                    this.onMouseDown(e);
                    this.DDM.handleMouseDown(e, this);
                    // this.DDM.stopEvent(e); // Must remove this event swallower for the tabpanel to work
                }
            }
        },
        startDrag: function (x, y) {
            Ext.dd.DDM.useCache = false; // Disable caching of element location
            Ext.dd.DDM.mode = 1; // Point mode

            this.proxyWrapper = Ext.get(this.getDragEl()); // Grab a reference to the proxy element we are creating
            this.proxyWrapper.update(); // Clear out the proxy's nodes
            this.proxyWrapper.applyStyles('z-index:1001;border:0 none;');
            this.proxyWrapper.addClass('tab-proxy');

            // Use 2 nested divs to mimic the default tab styling
            // You may need to customize the proxy to get it to look like your custom tabpanel if you use a bunch of custom css classes and styles
            this.stripWrap = this.proxyWrapper.insertHtml('afterBegin', '<div class="x-tab-strip x-tab-strip-top"></div>', true);
            this.dragEl = this.stripWrap.insertHtml('afterBegin', '<div></div>', true);

            this.tab = Ext.get(this.getEl()); // Grab a reference to the tab being dragged
            this.tab.applyStyles('visibility:hidden;'); // Hide the tab being dragged

            // Insert the html and css classes for the dragged tab into the proxy
            this.dragEl.insertHtml('afterBegin', this.tab.dom.innerHTML, false);
            this.dragEl.dom.className = this.tab.dom.className;

            // Constrain the proxy drag in the X coordinate to the tabpanel
            var panelWidth = this.tabpanel.el.getWidth();
            var panelX = this.tabpanel.el.getX();
            var tabX = this.tab.getX();
            var tabWidth = this.tab.getWidth();
            var left = tabX - panelX;
            var right = panelX + panelWidth - tabX - tabWidth;
            this.resetConstraints();
            this.setXConstraint(left, right);
        },
        onDragOver: function (e, targetArr) {
            e.stopEvent();

            // Grab the tab you have dragged the proxy over
            var target = Ext.get(targetArr[0].id);
            var targetWidth = target.getWidth();
            var targetX = target.getX();
            var targetMiddle = targetX + (targetWidth / 2);
            var elX = this.tab.getX();
            var dragX = this.proxyWrapper.getX();
            var dragW = this.proxyWrapper.getWidth();
            if (dragX < targetX && ((dragX + dragW) > targetMiddle)) {
                if (target.next() != this.tab) {
                    target.applyStyles('visibility:hidden;');
                    this.tab.insertAfter(target);
                    this.targetProxy = this.createSliderProxy(targetX, target);
                    if (!this.targetProxy.hasActiveFx()) this.animateSliderProxy(target, this.targetProxy, elX);
                }
            }
            if (dragX > targetX && (dragX < targetMiddle)) {
                if (this.tab.next() != target) {
                    target.applyStyles('visibility:hidden;');
                    this.tab.insertBefore(target);
                    this.targetProxy = this.createSliderProxy(targetX, target);
                    if (!this.targetProxy.hasActiveFx()) this.animateSliderProxy(target, this.targetProxy, elX);
                }
            }
        },
        animateSliderProxy: function (target, targetProxy, elX) {
            targetProxy.shift({
                x: elX,
                easing: 'easeOut',
                duration: this.slideDuration,
                callback: function () {
                    targetProxy.remove();
                    target.applyStyles('visibility:visible;');
                },
                scope: this
            });
        },
        createSliderProxy: function (targetX, target) {
            var sliderWrapperEl = Ext.getBody().insertHtml('afterBegin', '<div class="tab-proxy" style="position:absolute;visibility:visible;z-index:999;left:' + targetX + 'px;"></div>', true);
            sliderWrapperEl.stripWrapper = sliderWrapperEl.insertHtml('afterBegin', '<div class="x-tab-strip x-tab-strip-top"></div>', true);
            sliderWrapperEl.dragEl = sliderWrapperEl.stripWrapper.insertHtml('afterBegin', '<div></div>', true);
            sliderWrapperEl.dragEl.update(target.dom.innerHTML);
            sliderWrapperEl.dragEl.dom.className = target.dom.className;
            var h = parseInt(target.getTop(false));
            sliderWrapperEl.setTop(h)
            return sliderWrapperEl;
        },
        onDragDrop: function (e, targetId) {
            e.stopEvent();
        },
        endDrag: function (e) {
            var elX = this.tab.getX();
            this.proxyWrapper.applyStyles('visibility:visible;');

            // Animate the dragProxy to the proper position
            this.proxyWrapper.shift({
                x: elX,
                easing: 'easeOut',
                duration: this.slideDuration,
                callback: function () {
                    this.proxyWrapper.applyStyles('visibility:hidden;');
                    this.tab.applyStyles('visibility:visible;');

                    // Cleanup
                    this.stripWrap.remove();
                    this.dragEl.remove();
                    if (!this.targetProxy) return;
                    this.targetProxy.stripWrapper.remove();
                    this.targetProxy.dragEl.remove();
                },
                scope: this
            });

            Ext.dd.DDM.useCache = true;
        }
    });

})();