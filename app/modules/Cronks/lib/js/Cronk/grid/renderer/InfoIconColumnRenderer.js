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

Ext.ns('Cronk.grid');

(function () {

    "use strict";

    Cronk.grid.InfoIconColumnRenderer = new (function () {

        var buildIcon = function (iconCls, title) {
                return Ext.DomHelper.createDom({
                    tag: 'div',
                    cls: 'x-icinga-info-icon ' + iconCls,
                    'ext:qtip': title
                });
            };

        var buildIconFrame = function (data, element) {
                // element.removeClass('icinga-icon-throbber');
                if (data.check_type === 'passive') {
                    element.appendChild(buildIcon('icinga-icon-info-passive', _('Accepting passive only')));
                } else if (data.check_type === 'disabled') {
                    element.appendChild(buildIcon('icinga-icon-info-disabled', _('Check is disabled')));
                }

                if (data.in_downtime === true) {
                    element.appendChild(buildIcon('icinga-icon-info-downtime', _('Object in downtime')));
                }

                if (data.is_flapping === true) {
                    element.appendChild(buildIcon('icinga-icon-info-flapping', _('Object is flapping')));
                }

                if (data.notification_enabled === false) {
                    element.appendChild(buildIcon('icinga-icon-info-notifications-disabled', _('Notifications for this object are disabled')));
                }

                if (data.problem_acknowledged === true) {
                    element.appendChild(buildIcon('icinga-icon-info-problem-acknowledged', _('Problem has been acknowledged')));
                }

            };

        var updateContent = function (data, type, columns) {
                if (data.success === true) {
                    Ext.iterate(data.rows, function (oid, obj, arry) {
                        if (Ext.isArray(columns[oid])) {
                            for (var i = 0; i < columns[oid].length; i++) {
                                var element = columns[oid][i];
                                buildIconFrame(obj, element);
                            }
                        }
                    }, this);
                }
            };

        var loadInfoData = function (type) {

                var columns = this.getEl().select("div.object-info-icon-cell");

                if (columns.getCount()) {
                    var oids = [];
                    var re = new RegExp(/^.*object-info-icon-(\d+)-(\d+)$/);
                    var test = [];
                    var resultColumns = {};
                    columns.each(function (el, c, idx) {
                        if (!el.getAttribute("infoIconType")) {
                            return true;
                        }

                        var elType = el.getAttribute("infoIconType");
                        if (type !== 1 && type !== 2) {
                            type = elType;
                        }
                        if (elType !== type) {
                            return true;
                        }
                        var oid = el.getAttribute("infoIconObjectId");
                        oids.push(oid);
                        if (!Ext.isArray(resultColumns[oid])) {
                            resultColumns[oid] = [];
                            resultColumns[oid].push(Ext.get(el.dom));
                        }
                        return true;
                    }, this);

                    Ext.Ajax.request({
                        cancelOn: {
                            component: this.getStore(),
                            event: 'beforeload'
                        },
                        url: AppKit.util.Config.get('path') + '/modules/appkit/dispatch',
                        params: {
                            module: 'Cronks',
                            action: 'Provider.ObjectInfoIcons',
                            params: Ext.encode({
                                type: type,
                                oids: oids.join(','),
                                connection: this.selectedConnection
                            })
                        },
                        success: function (response, opts) {
                            try {
                                var data = Ext.decode(response.responseText);
                                updateContent(data, type, resultColumns);
                            } catch (e) {
                                //AppKit.log('Could not decode object info data ' + e);
                            }

                        },
                        scope: this
                    });
                }
            };

        this.init = function (grid, c) {
            grid.getStore().on('load', Ext.createDelegate(loadInfoData, grid, [c.column_type]));
        };

        this.infoColumn = function (cfg) {
            var id = Ext.id();
            return function (value, metaData, record, rowIndex, colIndex, store) {
                if (!store.infoId) {
                    store.infoId = id;
                }
                if (value !== null) {
                    return Ext.DomHelper.markup({
                        tag: 'div',
                        cls: 'object-info-icon-cell ' + cfg.type, // icinga-icon-throbber icon-16
                        infoIconType: cfg.type,
                        infoIconObjectId: value
                    });
                }
            };
        };

    })();


})();