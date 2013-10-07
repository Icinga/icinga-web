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

    /**
     * Util singleton class for use with other renderers
     */
    Cronk.grid.ColumnRendererUtil = function () {
        var pub = {
            metaDataObject: function (o) {
                var meta = {};
                var attributes = Cronk.util.StructUtil.extractParts(o, ['attr', 'cellAttr']);
                meta.attr = Cronk.util.StructUtil.attributeString(attributes.attr || {});
                meta.cellAttr = Cronk.util.StructUtil.attributeString(attributes.cellAttr || {});
                Ext.applyIf(meta, o);

                return meta;
            },

            applyXTemplate: function (grid, index, string) {
                var data = grid.getStore().getAt(index).data;
                var tpl = new Ext.XTemplate(string);
                return tpl.apply(data);
            },

            applyXTemplateOnMetaData: function (metaData, store, rowIndex) {
                Ext.iterate(metaData, function (i, v) {
                    if (Ext.isString(metaData[i])) {
                        metaData[i] = Cronk.grid.ColumnRendererUtil.applyXTemplate({
                            getStore: function () {
                                return store;
                            }
                        }, rowIndex, metaData[i]);
                    }
                });
            },

            testBooleanCondition: function (field, record) {

                if (Ext.isEmpty(record.data[field]) === false) {

                    return Boolean(record.data[field]);
                }

                return false;
            }
        };

        return pub;

    }();

    /**
     * Default column renderes
     */
    Cronk.grid.ColumnRenderer = {

        customColumnPerfdataSanitized: function (cfg) {
            return function (value, metaData, record, rowIndex, colIndex, store) {
                if (!value) {
                    return _('no value');
                }

                if (value.match(/check_multi/)) {
                    var output = '';
                    var expression = /(check_multi.*?)<br \/>/;
                    expression.exec(value);

                    output += RegExp.$1;

                    metaData.attr = 'ext:qtip="' + output + '"';

                    return output;
                }

                metaData.attr = 'ext:qtip="' + Ext.util.Format.htmlEncode(value) + '"';

                return value;
            };
        },

        nullDisplay: function (cfg) {
            return function (value, metaData, record, rowIndex, colIndex, store) {

                if (value === undefined) {
                    metaData.css += ' x-icinga-grid-data-null';
                    return '(null)';
                }

                return value;
            };
        },

        truncateText: function (cfg) {
            var defaultLength = AppKit.getPrefVal('org.icinga.grid.outputLength') || 70;
            cfg = cfg || {};
            return function (value, metaData, record, rowIndex, colIndex, store) {
                if (!value) {
                    return "";
                }
                // skip truncate if html is located at the ouput
                if (value.match(/<.*?>(.*?)<\/.*?>/g)) {
                    return value;
                }

                var out = Ext.util.Format.ellipsis(value, (Ext.isEmpty(cfg.length)) ? defaultLength : cfg.length);
                if (out.indexOf('...', (out.length - 3)) !== -1) {
                    metaData.attr = value.replace(/"/g, "'");
                }

                var id = Ext.id();
                (function() {
                    var ttip = new Ext.ToolTip({
                        target: Ext.get(id),
                        autoHide: false,
                        html: value,
                        width: value.length > 200 ? 500 : 200
                    });
                    ttip.on('show', function(el) {
                        var overlap = (el.x + el.getWidth()) - Ext.getBody().getWidth();
                        if (overlap > 0) {
                            el.setPagePosition(Ext.getBody().getWidth() - (el.getWidth() + 50), el.y);
                        }
                    }, this, {delay: 200});
                }).defer(200)
                return '<div id="' + id + '">' + out + '</div>';
            };
        },

        columnElement: function (cfg) {
            return function (value, metaData, record, rowIndex, colIndex, store) {
                var my = cfg; // local reference
                Ext.apply(metaData, Cronk.grid.ColumnRendererUtil.metaDataObject(my));
                Cronk.grid.ColumnRendererUtil.applyXTemplateOnMetaData(metaData, store, rowIndex);
                if (("value" in my)) {
                    return my.value;
                } else if (!("noValue" in my) && my.noValue !== true) {
                    return value;
                } else {
                    return "";
                }
            };
        },

        columnMetaData: function (cfg) {
            return function (value, metaData, record, rowIndex, colIndex, store) {
                Ext.apply(metaData, Cronk.grid.ColumnRendererUtil.metaDataObject(cfg));
                Cronk.grid.ColumnRendererUtil.applyXTemplateOnMetaData(metaData, store, rowIndex);
                return String.format('{0}', value);
            };
        },

        columnImage: function (cfg) {
            return function (value, metaData, record, rowIndex, colIndex, store) {
                var my = cfg; // local reference
                if (Ext.isEmpty(my.booleanConditionField) === false) {
                    if (!Cronk.grid.ColumnRendererUtil.testBooleanCondition(my.booleanConditionField, record)) {
                        return '';
                    }
                }

                Ext.apply(metaData, Cronk.grid.ColumnRendererUtil.metaDataObject(my));
                Cronk.grid.ColumnRendererUtil.applyXTemplateOnMetaData(metaData, store, rowIndex);
                var flat_attr = metaData.attr;

                delete metaData.attr;

                if (Ext.isEmpty(my.image)) {
                    return ''; //[no image defined (attr=image)]';
                } else {
                    // AppKit.log(my.booleanConditionField, my.image, record.data[my.booleanConditionField]);
                    var imgName = new Ext.XTemplate(my.image).apply(record.data);
                    // Old version
                    // return String.format('<img src="{0}/{1}"{2} />', AppKit.c.path, imgName, (flat_attr && " " + flat_attr + " "));
                    imgName = imgName.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
                    metaData.style += String.format("background: transparent url('{0}/{1}') center center no-repeat; background-size: 16px 16px;",
                        AppKit.c.path, imgName);

                    return "<div style='width:24px;height:24px' " + (flat_attr && " " + flat_attr + " ") + "></div>";
                }
            };
        },

        booleanImage: function (cfg) {
            return function (value, metaData, record, rowIndex, colIndex, store) {
                var iconCls = "icon-centered";
                var bVal = Boolean(Ext.decode(value));

                var qtip = "";
                if (!Ext.isEmpty(cfg.qtip)) {
                    qtip += cfg.qtip;
                }

                if (bVal === true) {
                    if (!Ext.isEmpty(cfg.TrueImageClass)) {
                        iconCls += " " + cfg.TrueImageClass;
                    }

                    if (!Ext.isEmpty(cfg.TrueQtipText)) {
                        qtip += cfg.TrueQtipText;
                    }
                } else if (bVal === false) {
                    if (!Ext.isEmpty(cfg.FalseImageClass)) {
                        iconCls += " " + cfg.FalseImageClass;
                    }

                    if (!Ext.isEmpty(cfg.FalseQtipText)) {
                        qtip += cfg.FalseQtipText;
                    }
                }

                if (qtip) {
                    metaData.attr = "ext:qtip='" + qtip + "'";
                }

                metaData.css += iconCls;

                Cronk.grid.ColumnRendererUtil.applyXTemplateOnMetaData(metaData, store, rowIndex);

                return "";
            };
        },

        columnImageFromValue: function (cfg) {
            return function (value, metaData, record, rowIndex, colIndex, store) {
                metaData.style += String.format("background: transparent url('{0}') center center no-repeat;", AppKit.util.Dom.imageUrl(value));
                Cronk.grid.ColumnRendererUtil.applyXTemplateOnMetaData(metaData, store, rowIndex);
                return "<div style=\"width: 24px; height: 24px\"></div>";
            };
        },

        regExpReplace: function (cfg) {
            return function (value, metaData, record, rowIndex, colIndex, store) {
                var exp = new RegExp(cfg.expression);
                return value.replace(exp, cfg.replacement);

            };
        },

        serviceStatus: function (cfg) {
            return function (value, metaData, record, rowIndex, colIndex, store) {

                if (Ext.isDefined(record.json.service_is_pending)) {
                    if (record.json.service_is_pending === 1 || record.json.service_is_pending === "1") {
                        value = 99;
                    }
                } else if (Ext.isDefined(record.json.service_has_been_checked)) {
                    if (record.json.service_has_been_checked === 0 || record.json.service_has_been_checked === "0") {
                        value = 99;
                    }
                }
                if (!Ext.isDefined(value)) {
                    return "";
                }
                return Icinga.StatusData.wrapElement('service', value);
            };
        },

        hostStatus: function (cfg) {
            return function (value, metaData, record, rowIndex, colIndex, store) {
                if (Ext.isDefined(record.json.host_is_pending)) {
                    if (record.json.host_is_pending === 1 || record.json.host_is_pending === "1") {
                        value = 99;
                    }
                } else if (Ext.isDefined(record.json.host_has_been_checked)) {
                    if (record.json.host_has_been_checked === 0 || record.json.host_has_been_checked === "0") {
                        value = 99;
                    }
                }
                if (!Ext.isDefined(value)) {
                    return "";
                }
                return Icinga.StatusData.wrapElement('host', value);
            };
        },

        stateType: function (cfg) {
            return function (value, metaData, record, rowIndex, colIndex, store) {
                if (!Ext.isDefined(value)) {
                    return "";
                }
                return Icinga.StatusData.wrapElement('statetype', value);
            };
        },

        switchStatus: function (cfg) {
            return function (value, metaData, record, rowIndex, colIndex, store) {
                var my = cfg;
                var type = "host";
                if ('serviceField' in my && record.data[my.serviceField]) {
                    type = "service";
                }
                return Icinga.StatusData.wrapElement(type, value);
            };
        },

        selectableColumn: function (cfg) {
            return function (value, metaData, record, rowIndex, colIndex, store) {
                metaData.css += ' x-icinga-grid-cell-selectable';
                return value;
            };
        },

        durationField: function (cfg) {
            return function (value, metaData, record, rowIndex, colIndex, store) {
                return AppKit.util.Date.toDurationString(
                    record.json.DURATION_START);
            };
        }
    };

})();
