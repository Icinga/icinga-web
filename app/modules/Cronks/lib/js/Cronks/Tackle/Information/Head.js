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

/*global Ext: false, Icinga: false, _: false */
Ext.ns('Icinga.Cronks.Tackle.Information');

(function () {
    "use strict";

    Icinga.Cronks.Tackle.Information.Head = Ext.extend(Ext.Panel, {

        columns_host: ['HOST_ID', 'HOST_OBJECT_ID', 'HOST_INSTANCE_ID', 'HOST_NAME', 'HOST_ALIAS', 'HOST_DISPLAY_NAME', 'HOST_ADDRESS', 'HOST_ADDRESS6', 'HOST_ACTIVE_CHECKS_ENABLED', 'HOST_CONFIG_TYPE', 'HOST_FLAP_DETECTION_ENABLED', 'HOST_PROCESS_PERFORMANCE_DATA', 'HOST_FRESHNESS_CHECKS_ENABLED', 'HOST_FRESHNESS_THRESHOLD', 'HOST_PASSIVE_CHECKS_ENABLED', 'HOST_EVENT_HANDLER_ENABLED', 'HOST_ACTIVE_CHECKS_ENABLED', 'HOST_RETAIN_STATUS_INFORMATION', 'HOST_RETAIN_NONSTATUS_INFORMATION', 'HOST_NOTIFICATIONS_ENABLED', 'HOST_OBSESS_OVER_HOST', 'HOST_FAILURE_PREDICTION_ENABLED', 'HOST_NOTES', 'HOST_NOTES_URL', 'HOST_ACTION_URL', 'HOST_ICON_IMAGE', 'HOST_ICON_IMAGE_ALT', 'HOST_IS_ACTIVE', 'HOST_OUTPUT', 'HOST_LONG_OUTPUT', 'HOST_PERFDATA', 'HOST_CURRENT_STATE', 'HOST_CURRENT_CHECK_ATTEMPT', 'HOST_MAX_CHECK_ATTEMPTS', 'HOST_LAST_CHECK', 'HOST_LAST_STATE_CHANGE', 'HOST_CHECK_TYPE', 'HOST_LATENCY', 'HOST_EXECUTION_TIME', 'HOST_NEXT_CHECK', 'HOST_HAS_BEEN_CHECKED', 'HOST_LAST_HARD_STATE_CHANGE', 'HOST_LAST_NOTIFICATION', 'HOST_PROCESS_PERFORMANCE_DATA', 'HOST_STATE_TYPE', 'HOST_IS_FLAPPING', 'HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED', 'HOST_SCHEDULED_DOWNTIME_DEPTH', 'HOST_SHOULD_BE_SCHEDULED', 'HOST_STATUS_UPDATE_TIME'],

        columns_service: ['SERVICE_ID', 'SERVICE_INSTANCE_ID', 'SERVICE_CONFIG_TYPE', 'SERVICE_IS_ACTIVE', 'SERVICE_OBJECT_ID', 'SERVICE_NAME', 'SERVICE_DISPLAY_NAME', 'SERVICE_NOTIFICATIONS_ENABLED', 'SERVICE_FLAP_DETECTION_ENABLED', 'SERVICE_PASSIVE_CHECKS_ENABLED', 'SERVICE_EVENT_HANDLER_ENABLED', 'SERVICE_ACTIVE_CHECKS_ENABLED', 'SERVICE_RETAIN_STATUS_INFORMATION', 'SERVICE_RETAIN_NONSTATUS_INFORMATION', 'SERVICE_OBSESS_OVER_SERVICE', 'SERVICE_FAILURE_PREDICTION_ENABLED', 'SERVICE_NOTES', 'SERVICE_NOTES_URL', 'SERVICE_ACTION_URL', 'SERVICE_ICON_IMAGE', 'SERVICE_ICON_IMAGE_ALT', 'SERVICE_OUTPUT', 'SERVICE_LONG_OUTPUT', 'SERVICE_PERFDATA', 'SERVICE_PROCESS_PERFORMANCE_DATA', 'SERVICE_CURRENT_STATE', 'SERVICE_CURRENT_CHECK_ATTEMPT', 'SERVICE_MAX_CHECK_ATTEMPTS', 'SERVICE_LAST_CHECK', 'SERVICE_LAST_STATE_CHANGE', 'SERVICE_CHECK_TYPE', 'SERVICE_LATENCY', 'SERVICE_EXECUTION_TIME', 'SERVICE_NEXT_CHECK', 'SERVICE_HAS_BEEN_CHECKED', 'SERVICE_LAST_HARD_STATE', 'SERVICE_LAST_HARD_STATE_CHANGE', 'SERVICE_LAST_NOTIFICATION', 'SERVICE_STATE_TYPE', 'SERVICE_IS_FLAPPING', 'SERVICE_PROBLEM_HAS_BEEN_ACKNOWLEDGED', 'SERVICE_SCHEDULED_DOWNTIME_DEPTH', 'SERVICE_SHOULD_BE_SCHEDULED', 'SERVICE_STATUS_UPDATE_TIME'],

        layout: 'hbox',
        layoutConfig: {
            align: 'stretch',
            pack: 'start'
        },
        fields: [],
        title: _('Default'),
        iconCls: 'icinga-icon-information',
        store: null,

        constructor: function (config) {
            if (Ext.isEmpty(config.type)) {
                throw ("config.type is needed: host or service!");
            }
            this.targetType = config.type;
            this.connection = config.connection || "icinga";
            Icinga.Cronks.Tackle.Information.Head.superclass.constructor.call(this, config);
        },

        buildFieldsObject: function (type) {
            
            var sourceArray = this['columns_' + type];

            if (Ext.isArray(sourceArray) === false) {
                throw ("Could not build fields from type: " + type);
            }

            var fields = [];

            Ext.iterate(sourceArray, function (key, val) {
                var sourceKey = key.toLowerCase().replace(type, "object");
                fields.push({
                    name: sourceKey,
                    mapping: key
                });
            }, this);

            return fields;
        },

        initComponent: function () {
            Icinga.Cronks.Tackle.Information.Head.superclass.initComponent.call(this);

            this.fields = this.buildFieldsObject(this.type);

            this.store = new Icinga.Api.RESTStore({
                autoDestroy: true,
                idIndex: 0,
                target: this.targetType,
                columns: this.fields,
                connection: this.connection
            });

            this.store.on('load', this.updateSubComponents, this);

            // DEBUG
            // this.loadDataForObjectId(3);

            this.stateInfo = new Icinga.Cronks.Tackle.Information.State({
                type: this.type,
                flex: 1,
                layout : 'fit'
            });

            this.pluginOutputInfo = new Icinga.Cronks.Tackle.Information.PluginOutput({
                // height : 40,
                type: this.type,
                flex: 1,
                layout: 'fit'
            });
            this.pluginLongOutputInfo = new Icinga.Cronks.Tackle.Information.LongPluginOutput({
                // height : 40,
                type: this.type,
                flex: 1,
                layout: 'fit'
            });
            this.pluginPerfdataInfo = new Icinga.Cronks.Tackle.Information.Perfdata({
                // height: 40,
                type: this.type,
                flex: 1,
                layout: 'fit'
            });

            this.add(
                this.stateInfo, {
                xtype: 'panel',
                border: false,
                layout: 'vbox',
                flex: 1,
                
                layoutConfig: {
                    align: 'stretch',
                    pack: 'start'
                },
                
                items: [this.pluginOutputInfo, this.pluginLongOutputInfo, this.pluginPerfdataInfo]
            });

            this.doLayout();
        },

        updateSubComponents: function (store, records, options) {

            var record = store.getAt(0);

            this.stateInfo.setSource(record.data);
            this.pluginOutputInfo.update(record.data);
            this.pluginLongOutputInfo.update(record.data);
            this.pluginPerfdataInfo.update(record.data);
        },

        loadDataForObjectId: function (oid,connection) {
            var field = String.format('{0}_OBJECT_ID', this.type.toUpperCase());
            this.store.connection = connection;
            this.store.setFilter({
                type: 'AND',
                field: [{
                    type: 'atom',
                    field: [field],
                    method: ['='],
                    value: [oid]
                }]
            });

            this.store.load();
        },
        
        getStore: function() {
            return this.store;
        }
    });

    Ext.reg('cronks-tackle-information-head', Icinga.Cronks.Tackle.Information.Head);

})();
