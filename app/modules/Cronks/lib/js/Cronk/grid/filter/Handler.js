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
/*global Ext: false, Icinga: false, AppKit: false, _: false, Cronk: false */

Ext.ns('Cronk.grid.filter');

(function () {

    "use strict";

    /**
     * Handler to create components for modifying data in
     * grids
     */
    Cronk.grid.filter.Handler = Ext.extend(Ext.util.Observable, {
        
        /**
         * @property {Object} oFilterOp Mapping for filters to default operators
         * @private
         */
        oFilterOp: {
            'appkit.ext.filter.text': 'text',
            'appkit.ext.filter.number': 'number',
            'appkit.ext.filter.servicestatus': 'number',
            'appkit.ext.filter.hoststatus': 'number',
            'appkit.ext.filter.downtime_type_fixed': 'number',
            'appkit.ext.filter.bool': 'bool'
        },
        
        /**
         * @property {Object} oOpList Operator choices for operator_type
         * @private
         */
        oOpList: {
            text: [
                [60, _('contain')],
                [61, _('does not contain')],
                [50, _('is')],
                [51, _('is not')]
            ],

            number: [
                [50, _('is')],
                [51, _('is not')],
                [70, _('less than')],
                [71, _('greater than')]
            ],

            bool: [
                [50, _('is')]
            ]
        },
        
        /**
         * @property {Object} oOpDefault Default operators
         * @private
         */
        oOpDefault: {
            number: 50,
            text: 60,
            bool: 50
        },
        
        /**
         * @property {Object} meta Grid meta data
         * @private
         */
        meta: {},
        
        /**
         * @property {Object} config Object configuration
         * @private
         */
        config: {},
        
        /**
         * @property {Object} cList A list of all created components
         * @private
         */
        cList: {},
        
        /**
         * Create a new handler
         * 
         * @param {Object} config
         */
        constructor: function (config) {

            Ext.apply(this.config, config);

            if (this.config.meta) {
                this.setMeta(this.config.meta);
            }

            this.listener = {};

            this.addEvents({
                'aftercompremove': true,
                'compremove': true,
                'aftercompadd': true,
                'compcreate': true,
                'aftercompcreate': true,
                'metaload': true
            });

            Cronk.grid.filter.Handler.superclass.constructor.call();
        },
        
        /**
         * Setter for grid meta information
         * @param {Object} meta
         * @return {Boolean} Always true
         */
        setMeta: function (meta) {
            if (this.fireEvent('metaload', this, meta) !== false) {
                this.meta = meta;
            }

            return true;
        },
        
        /**
         * Creates a button to remove a filter
         * @param {Object} meta
         * @return {Ext.Button}
         */
        getRemoveComponent: function (meta) {
            var button = new Ext.Button({
                xtype: 'button',
                iconCls: 'icinga-icon-cross',
                handler: function (b, e) {
                    this.removeComponent(meta);
                },
                anchor: '-5',
                scope: this,
                width: 25
            });

            return button;
        },
        
        /**
         * Remove all existing components
         */
        removeAllComponents: function () {
            Ext.iterate(this.cList, function (k, v) {
                this.removeComponent(v);
            }, this);
        },
        
        /**
         * Remove a single component from panel
         * @param {Object} meta
         * @return {Boolean} Always true
         */
        removeComponent: function (meta) {

            var cid = 'fco' + meta.id;

            // Retrieve the comp_id
            var p = Ext.getCmp('fco' + meta.id);

            // Removing the panel construct
            if (this.fireEvent('compremove', this, p, meta) !== false) {

                var form = p.findParentByType('form');

                if (form) {
                    form.remove(p, true).destroy();
                    delete this.cList[cid];
                }
            }

            this.fireEvent('aftercompremove', this, p, meta);

            return true;
        },
        
        /**
         * Return a object structur for Ext.create to build
         * a label component for the filter
         * @param {Object} meta
         * @return {Object} Xtype structure
         */
        getLabelComponent: function (meta) {
            return {
                xtype: 'label',
                html: meta.label,
                border: false,
                width: "100px",
                columWidth: "100px"
            };
        },

        /**
         * Creates a operator component used by the filter
         * @param {Object} meta
         * @return {Ext.form.ComboBox}
         */
        getOperatorComponent: function (meta) {
            var type = null;

            // Disable the operator
            if (meta.no_operator && meta.no_operator === true) {
                return new Ext.Panel({
                    border: false
                });
            }

            if (meta.operator_type) {
                type = meta.operator_type;
            }

            if (!type) {
                type = this.oFilterOp[meta.subtype];
            }
            
            // this is our combo field
            var oCombo = new Ext.form.ComboBox({

                store: new Ext.data.ArrayStore({
                    idIndex: 0,
                    fields: ['id', 'label'],
                    data: this.oOpList[type] || []
                }),

                mode: 'local',

                typeAhead: true,
                triggerAction: 'all',
                forceSelection: true,

                fieldLabel: "Operator",

                valueField: 'id',
                displayField: 'label',

                hiddenName: meta.id + '-operator',
                hiddenId: meta.id + '-operator',

                'name': '___LABEL' + meta.id + '-operator',
                id: '___LABEL' + meta.id + '-operator',
                columnWidth: 0.3


            });

            // Set the default value after rendering
            oCombo.on('render', function (c) {
                if(!c.getValue())
                    c.setValue(this.oOpDefault[type]);
            }, this);

            // Pack all together in a container
            // var p = new Ext.Panel({border: false});
            // p.add(oCombo);
            // return p;

            return oCombo;
        },

        /**
         * Dispatcher for creatung combos based on data
         * @param {Array} data
         * @param {Object} meta
         * @return {Ext.form.ComboBox}
         */
        getComboComponent: function (data, meta) {
            var def = {
                store: new Ext.data.ArrayStore({
                    idIndex: 0,
                    fields: ['fId', 'fStatus', 'fLabel'],
                    data: data
                }),

                'name': '__status_name_' + meta.name,
                'id': '__status_name_' + meta.name,
                // 'name': meta.name + '-value',

                mode: 'local',
                typeAhead: true,
                triggerAction: 'all',
                forceSelection: true,


                fieldLabel: 'Status',

                valueField: 'fStatus',
                displayField: 'fLabel',

                hiddenName: meta.name + '-value',
                hiddenId: meta.name + '-value',
                columnWidth: 0.6
            };

            return new Ext.form.ComboBox(def);

        },

        /**
         * Return a api combo box for IcingaWeb Api data
         * @param {Object} meta
         * @return {Cronk.grid.filter.ApiComboBox}
         */
        getApiCombo: function (meta) {
            return new Cronk.grid.filter.ApiComboBox({
                typeAhead: false,
                triggerAction: 'all',
                forceSelection: false,
                'name': meta.name + '-field',
                'id': meta.name + '-field',
                hiddenName: meta.name + '-value',
                hiddenId: meta.name + '-value',
                columnWidth: 0.6
            }, meta);
        },

        /**
         * Dispatcher creating a filter component based on meta data
         * @param {Object} meta
         * @return {Ext.form.Field}
         */
        getFilterComponent: function (meta) {
            var oDef = {
                'name': meta.name + '-value',
                id: meta.name + '-value',
                columnWidth: 0.6,
                bodyStyle: 'padding:0 18px 0 0'
            };

            switch (meta.subtype) {

            case 'appkit.ext.filter.servicestatus':
                return this.getComboComponent([
                    ['1', '0', 'OK'],
                    ['2', '1', 'Warning'],
                    ['3', '2', 'Critical'],
                    ['4', '3', 'Unknown']
                ], meta);

            case 'appkit.ext.filter.bool':
                return this.getComboComponent([
                    ['1', '1', _('Yes')],
                    ['2', '0', _('No')]
                ], meta);

            case 'appkit.ext.filter.hoststatus':
                return this.getComboComponent([
                    ['1', '0', 'UP'],
                    ['2', '1', 'Down'],
                    ['3', '2', 'Unreachable']
                ], meta);

            case 'appkit.ext.filter.downtime_type_fixed':
                return this.getComboComponent([
                    ['1', '0', 'Flexible'],
                    ['2', '1', 'Fixed']
                ], meta);

            case 'appkit.ext.filter.api':
                return this.getApiCombo(meta);

            default:
                return new Ext.form.TextField(oDef);

            }


        },

        /**
         * Compat version of componentDispatch
         * @param {Object} meta
         * @return {Ext.Panel}
         */
        createComponent: function (meta) {
            return this.componentDispatch(meta);
        },

        /**
         * Create a filter stack (label, operator, filter, removeButton)
         * @param {Object} meta
         * @return {Ext.Panel}
         */
        componentDispatch: function (meta) {

            var cid = 'fco' + meta.id;

            var panel = new Ext.Panel({
                id: cid,
                border: false,
                layout: 'column',
                padding: 4,
                defaults: {
                    border: false,
                    anchor: '100%'
                }
            });

            // Before adding stage
            if (this.fireEvent('compcreate', this, panel, meta) !== false) {

                this.cList[cid] = meta;

                // Adding the label
                panel.add([
                this.getLabelComponent(meta),
                this.getOperatorComponent(meta),
                this.getFilterComponent(meta),
                this.getRemoveComponent(meta)]);

            }

            // All panels there
            this.fireEvent('aftercompcreate', this, panel, meta);

            return panel;

        }

    });

})();
