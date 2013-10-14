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

Ext.ns('Icinga.Reporting.util');

/**
 * Class to create input controls based
 * on report parameters
 *
 * @class
 */
Icinga.Reporting.util.InputControlBuilder = Ext.extend(Object, {

    /**
     * @cfg {Boolean}
     * Drop all items first before add new ones
     */
    removeAll: false,

    /**
     * @type {Ext.util.MixedCollection}
     * @property items
     *
     * Collection of input elements
     */

    /**
     * @type {Object}
     * @property controlsStruct
     *
     * Collection of input element definitions
     */

    /**
     * Constructor
     * @param {Object} config
     */
    constructor: function (config) {
        Icinga.Reporting.util.InputControlBuilder.superclass.constructor.call(this);

        this.initialConfig = config;
        Ext.apply(this, config);

        this.items = new Ext.util.MixedCollection();

        //console.log(this.controlStruct);
    },

    /**
     * Set the target container we work on
     * @param {Ext.Container} target
     */
    setTarget: function (target) {
        this.target = target;
    },

    setControlStruct: function (controlStruct) {
        this.controlStruct = controlStruct;
    },

    buildFormItems: function () {
        this.items.clear();

        var namePrefix = this.namePrefix || '';

        Ext.iterate(this.controlStruct, function (k, v) {
            var inputConfig = {};

            Ext.apply(v.jsControl, {
                hidden: v.PROP_INPUTCONTROL_IS_VISIBLE == "false" ? true : false,
                readonly: v.PROP_INPUTCONTROL_IS_READONLY == "true" ? true : false,
                name: namePrefix + v.name,
                fieldLabel: v['label'],
                listeners: {
                    afterrender: function(component) {
                        // Set width to 80% of maximum space (component - labelWidth)
                        // refs #3922
                        var width = parseInt((component.ownerCt.getWidth() - component.label.getWidth()) * 0.8, 10);
                        component.setWidth(width);
                    }
                }
            });

            Ext.applyIf(v.jsControl, Icinga.Reporting.DEFAULT_JSCONTROL);

            inputConfig = v.jsControl;

            if (!Ext.isEmpty(v.jsData)) {
                inputConfig.jsData = v.jsData;
            } else {
                inputConfig.jsData = false;
            }

            if (Ext.isEmpty(inputConfig.allowBlank)) {
                inputConfig.allowBlank = false;
            }

            if (!Ext.isEmpty(inputConfig.className)) {
                var inputClass = eval('window.' + inputConfig.className);
                var inputControl = new inputClass(inputConfig);
                this.items.add(inputConfig.name, inputControl);
            }

        }, this);

        return this.items;
    },

    /**
     * Create form elements and add to target
     * @param {Ext.Component} target
     */
    applyToTarget: function (target) {
        target = target || this.target;

        if (this.items.getCount() < 1) {
            this.buildFormItems();
        }

        if (this.removeAll == true) {
            target.removeAll(true);
        }

        this.items.each(function (item, index, len) {
            target.add(item);
        }, this);

        target.doLayout();
    },

    /**
     * Tests if we have controls there
     * @returns {Boolean}
     */
    hasControls: function () {
        return !!(Object.keys(this.controlStruct).length > 0);
    }
});
