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
/*jshint browser:true, curly:false */

(function() {
"use strict";


Ext.ns('Icinga.Cronks.util.FilterTypes').Api = function(filterCfg,defaults) {
    var provider = Icinga.Cronks.util.FilterTypes.FieldProvider;
    return {
        xtype:'form',
        height:200,
        padding:5,
        width:300,
        items: [{
            border: false,
            html: '<div style="font-size:12px;">'+_("Add a new filter here. You can change the filter afterwards or move it in the filter tree")+
            '</div>'
        },{
            xtype: 'spacer',
            height: 25
        },{
            xtype: 'hidden',
            name: 'field',
            value: filterCfg.name
        },{
            xtype: 'hidden',
            name: 'label',
            value: filterCfg.label
        },{
            fieldLabel: _('Filter target'),
            xtype: 'textfield',
            emptyText: filterCfg.label,
            disabled: true,
            allowEmpty:false,
            anchor: '90%'
        },
            provider.getLocalComboConfig(provider.TEXT_FIELDS,"operator",defaults.operator),
            provider.getApiCombo(filterCfg,defaults.value)
        ]
    };
};

    Ext.ns('Icinga.Cronks.util.FilterTypes').Customvar = function(filterCfg,defaults) {
        var provider = Icinga.Cronks.util.FilterTypes.FieldProvider;
        return {
            xtype:'form',
            height:200,
            padding:5,
            width:300,
            items: [{
                border: false,
                html: '<div style="font-size:12px;">'+_("Add a new "+filterCfg.label+" filter here. You can change the filter afterwards or move it in the filter tree")+
                    '</div>'
            },{
                xtype: 'spacer',
                height: 25
            },{
                xtype: 'hidden',
                name: 'field',
                value: "Customvariable"

            },provider.getApiCombo({
                boxLabel: 'Customvariable',
                api_keyfield: "CUSTOMVARIABLE_NAME",
                api_target: "customvariable",
                api_valuefield: "CUSTOMVARIABLE_NAME",
                name: 'CUSTOMVARIABLE_NAME'
            }),
                provider.getLocalComboConfig(provider.TEXT_FIELDS,"operator",defaults.operator),
                provider.getApiCombo({
                    boxLabel: 'Customvariable',
                    hidden: true,
                    api_keyfield: "CUSTOMVARIABLE_NAME",
                    api_target: "customvariable",
                    api_valuefield: "CUSTOMVARIABLE_NAME",
                    name: 'CUSTOMVARIABLE_NAME'
                }),
            ]
        }
    }

})();
