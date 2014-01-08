// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-present Icinga Developer Team.
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

Ext.ns('Icinga.Reporting.inputControl');

Icinga.Reporting.inputControl.ApiSelectionField = Ext.extend(Ext.form.ComboBox, {
    constructor : function(config) {
        
        Ext.apply(config, {
            typeAhead : true,
            triggerAction : 'all',
            mode : 'remote'
        });
        
        if (config.tpl) {
            config.tpl = new Ext.XTemplate(
                '<tpl for=".">',
                '<div class="x-combo-list-item">',
                config.tpl,
                '</div>',
                '</tpl>'
            );
        }
        
        config.hiddenName = config.name;
        
        var store = this.createStoreFromConfig({
            target : config.target,
            valueField : config.valueField,
            displayField : config.displayField,
            order: config.order
        }, config);
        
        config.store = store;

        Icinga.Reporting.inputControl.ApiSelectionField.superclass.constructor.call(this, config);
    },
    
    createStoreFromConfig : function(config, origin) {
        
        var displayField = config.displayField;
        var valueField = config.valueField;
        
        var url = AppKit.util.Config.getBaseUrl() + String.format('/modules/web/api/{0}/json', config.target.toLowerCase());

        var order_col = config.displayField;
        var order_dir = 'asc';

        if (Ext.isObject(config.order)) {
            order_col = config.order.field;
            order_dir = String(config.order.order).toLowerCase();
        }

        var baseParams = {
            order_col: order_col,
            order_dir: order_dir
        };
        
        var fields = [];
        
        if (!Ext.isEmpty(origin.columns) && Ext.isArray(origin.columns)) {
            Ext.apply(baseParams, {
                columns : origin.columns.join("|")
            });
            fields = origin.columns;
        } else {
            fields = [displayField, valueField];
        }

        var store = new Ext.data.JsonStore({
            url : url,
            autoDestroy : true,
            root : 'result',
            idProperty : valueField,
            fields : fields,
            baseParams : baseParams,

            listeners : {
                beforeload : function(store, options) {
                    if (!Ext.isEmpty(store.baseParams.query)) {
                        var targetJson = [];
                        Ext.iterate(fields, function(item, key) {
                            targetJson.push({
                                type : 'atom',
                                field : [item],
                                method : ['like'],
                                value : [String.format('*{0}*', store.baseParams.query)]
                            });
                        });
                        
                        store.baseParams.filters_json = Ext.util.JSON.encode({
                            type : 'OR',
                            field : targetJson
                        });
                    }
                }
            }
        });
        
        store.load();
        
        return store;
    },
    
    initComponent : function() {
        Icinga.Reporting.inputControl.ApiSelectionField.superclass.initComponent.call(this);
        
        this.on('beforequery', function(queryEvent) {
            if (Ext.isEmpty(queryEvent.query)) {
                delete(this.store.baseParams.filters_json);
                this.store.reload();
            }
        }, this);
    }
});
