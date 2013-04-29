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

    Cronk.util.StaticContentUtil = {

        convertToLink: function (ele, image_class) {
            ele.addClass([image_class, 'icinga-image-link']);
        },

        drilldownLink: function (c) {

            var to = Ext.getCmp(c.cmpid);

            if (Ext.get(c.jsid) && to) {
                var link = Ext.get(c.jsid);
                Cronk.util.StaticContentUtil.convertToLink(link, 'icinga-icon-drilldown');

                var updater = to.getUpdater();
                link.on('click', function (e) {
                    var u = updater.defaultUrl.split('?', 2);

                    var oUrl = {};

                    try {
                        oUrl = Ext.urlDecode(u[1]);
                    } catch (e) {
                        // PASS
                    }

                    var ary = [];
                    if (Ext.isEmpty(oUrl['p[filter_appendix]'])) {
                        oUrl['p[filter_appendix]'] = "";
                    } else {
                        ary = oUrl['p[filter_appendix]'].split('|');
                    }

                    var x = new Ext.XTemplate(c.filter_value);
                    var filter_value = x.apply(c.filter_object);

                    ary.push(String.format('{0},{1}', c.filter_field, filter_value.toUpperCase()));

                    oUrl['p[filter_appendix]'] = ary.join('|');

                    oUrl['p[chain]'] = c.chainid += 1;

                    updater.defaultUrl = Ext.urlAppend(u[0], Ext.urlEncode(oUrl));

                    updater.refresh();
                });
            }
        },

        drillupLink: function (c) {
            var to = Ext.getCmp(c.cmpid);

            if (Ext.get(c.jsid) && to) {
                var link = Ext.get(c.jsid);

                Cronk.util.StaticContentUtil.convertToLink(link, 'icinga-icon-drillup');

                var updater = to.getUpdater();
                link.on('click', function (e) {
                    var u = updater.defaultUrl.split('?', 2);

                    var oUrl = {};
                    try {
                        oUrl = Ext.urlDecode(u[1]);
                    } catch (e) {
                        // PASS
                    }

                    var ary = [];
                    if (Ext.isEmpty(oUrl['p[filter_appendix]'])) {
                        oUrl['p[filter_appendix]'] = "";
                    } else {
                        ary = oUrl['p[filter_appendix]'].split('|');
                    }

                    if (ary.length === 0) {
                        throw "drillupLink: need filter_appendix set!";
                    }

                    ary.pop();

                    oUrl['p[filter_appendix]'] = ary.join('|');

                    oUrl['p[chain]'] = c.chainid -= 1;

                    updater.defaultUrl = Ext.urlAppend(u[0], Ext.urlEncode(oUrl));

                    updater.refresh();

                });
            }
        }
    };
})();