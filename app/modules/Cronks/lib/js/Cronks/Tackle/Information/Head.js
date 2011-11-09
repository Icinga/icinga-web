/*global Ext: false, Icinga: false, _: false */
Ext.ns('Icinga.Cronks.Tackle.Information');

(function () {
    "use strict";

    Icinga.Cronks.Tackle.Information.Head = Ext.extend(Ext.Panel, {

        columns_host: ['HOST_ID', 'HOST_OBJECT_ID', 'HOST_INSTANCE_ID', 'HOST_NAME', 'HOST_ALIAS', 'HOST_DISPLAY_NAME', 'HOST_ADDRESS', 'HOST_ADDRESS6', 'HOST_ACTIVE_CHECKS_ENABLED', 'HOST_CONFIG_TYPE', 'HOST_FLAP_DETECTION_ENABLED', 'HOST_PROCESS_PERFORMANCE_DATA', 'HOST_FRESHNESS_CHECKS_ENABLED', 'HOST_FRESHNESS_THRESHOLD', 'HOST_PASSIVE_CHECKS_ENABLED', 'HOST_EVENT_HANDLER_ENABLED', 'HOST_ACTIVE_CHECKS_ENABLED', 'HOST_RETAIN_STATUS_INFORMATION', 'HOST_RETAIN_NONSTATUS_INFORMATION', 'HOST_NOTIFICATIONS_ENABLED', 'HOST_OBSESS_OVER_HOST', 'HOST_FAILURE_PREDICTION_ENABLED', 'HOST_NOTES', 'HOST_NOTES_URL', 'HOST_ACTION_URL', 'HOST_ICON_IMAGE', 'HOST_ICON_IMAGE_ALT', 'HOST_IS_ACTIVE', 'HOST_OUTPUT', 'HOST_LONG_OUTPUT', 'HOST_PERFDATA', 'HOST_CURRENT_STATE', 'HOST_CURRENT_CHECK_ATTEMPT', 'HOST_MAX_CHECK_ATTEMPTS', 'HOST_LAST_CHECK', 'HOST_LAST_STATE_CHANGE', 'HOST_CHECK_TYPE', 'HOST_LATENCY', 'HOST_EXECUTION_TIME', 'HOST_NEXT_CHECK', 'HOST_HAS_BEEN_CHECKED', 'HOST_LAST_HARD_STATE_CHANGE', 'HOST_LAST_NOTIFICATION', 'HOST_PROCESS_PERFORMANCE_DATA', 'HOST_STATE_TYPE', 'HOST_IS_FLAPPING', 'HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED', 'HOST_SCHEDULED_DOWNTIME_DEPTH', 'HOST_SHOULD_BE_SCHEDULED', 'HOST_STATUS_UPDATE_TIME', 'HOST_EXECUTION_TIME_MIN', 'HOST_EXECUTION_TIME_AVG', 'HOST_EXECUTION_TIME_MAX', 'HOST_LATENCY_MIN', 'HOST_LATENCY_AVG', 'HOST_LATENCY_MAX'],

        columns_service: ['SERVICE_ID', 'SERVICE_INSTANCE_ID', 'SERVICE_CONFIG_TYPE', 'SERVICE_IS_ACTIVE', 'SERVICE_OBJECT_ID', 'SERVICE_NAME', 'SERVICE_DISPLAY_NAME', 'SERVICE_NOTIFICATIONS_ENABLED', 'SERVICE_FLAP_DETECTION_ENABLED', 'SERVICE_PASSIVE_CHECKS_ENABLED', 'SERVICE_EVENT_HANDLER_ENABLED', 'SERVICE_ACTIVE_CHECKS_ENABLED', 'SERVICE_RETAIN_STATUS_INFORMATION', 'SERVICE_RETAIN_NONSTATUS_INFORMATION', 'SERVICE_OBSESS_OVER_SERVICE', 'SERVICE_FAILURE_PREDICTION_ENABLED', 'SERVICE_NOTES', 'SERVICE_NOTES_URL', 'SERVICE_ACTION_URL', 'SERVICE_ICON_IMAGE', 'SERVICE_ICON_IMAGE_ALT', 'SERVICE_OUTPUT', 'SERVICE_LONG_OUTPUT', 'SERVICE_PERFDATA', 'SERVICE_PROCESS_PERFORMANCE_DATA', 'SERVICE_CURRENT_STATE', 'SERVICE_CURRENT_CHECK_ATTEMPT', 'SERVICE_MAX_CHECK_ATTEMPTS', 'SERVICE_LAST_CHECK', 'SERVICE_LAST_STATE_CHANGE', 'SERVICE_CHECK_TYPE', 'SERVICE_LATENCY', 'SERVICE_EXECUTION_TIME', 'SERVICE_NEXT_CHECK', 'SERVICE_HAS_BEEN_CHECKED', 'SERVICE_LAST_HARD_STATE', 'SERVICE_LAST_HARD_STATE_CHANGE', 'SERVICE_LAST_NOTIFICATION', 'SERVICE_STATE_TYPE', 'SERVICE_IS_FLAPPING', 'SERVICE_PROBLEM_HAS_BEEN_ACKNOWLEDGED', 'SERVICE_SCHEDULED_DOWNTIME_DEPTH', 'SERVICE_SHOULD_BE_SCHEDULED', 'SERVICE_STATUS_UPDATE_TIME', 'SERVICE_EXECUTION_TIME_MIN', 'SERVICE_EXECUTION_TIME_AVG', 'SERVICE_EXECUTION_TIME_MAX', 'SERVICE_LATENCY_MIN', 'SERVICE_LATENCY_AVG', 'SERVICE_LATENCY_MAX'],

        layout: 'hbox',
        layoutConfig: {
            align: 'stretch',
            pack: 'start'
        },
        style: 'height: 100%;',
        fields: [],
        title: _('Default'),
        iconCls: 'icinga-icon-information',
        store: null,

        constructor: function (config) {
            if (Ext.isEmpty(config.type)) {
                throw ("config.type is needed: host or service!");
            }

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
                target: 'host',
                columns: this.fields
            });

            this.store.on('load', this.updateSubComponents, this);

            // DEBUG
            // this.loadDataForObjectId(3);

            this.stateInfo = new Icinga.Cronks.Tackle.Information.State({
                width: 400,
                type: this.type
            });

            this.pluginOutputInfo = new Icinga.Cronks.Tackle.Information.PluginOutput({
                width: 400,
                height: 100,
                type: this.type,
                layout: 'fit'
            });

            this.pluginPerfdataInfo = new Icinga.Cronks.Tackle.Information.Perfdata({
                width: 400,
                height: 100,
                type: this.type,
                layout: 'fit'
            });

            var dummyPanel = new Ext.Panel({
                title: 'TEST PANEL',
                html: 'Stupid test panel without meaning!',
                layout: 'fit'
            });

            this.add(
                this.stateInfo, {
                xtype: 'panel',
                border: false,
                layout: 'vbox',
                
                layoutConfig: {
                    align: 'stretchmax',
                    pack: 'start'
                },
                items: [this.pluginOutputInfo, this.pluginPerfdataInfo]
            });

            this.doLayout();
        },

        updateSubComponents: function (store, records, options) {
            var record = store.getAt(0);

            this.stateInfo.setSource(record.data);
            this.pluginOutputInfo.update(record.data);
            this.pluginPerfdataInfo.update(record.data);
        },

        loadDataForObjectId: function (oid) {
            var field = String.format('{0}_OBJECT_ID', this.type.toUpperCase());

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
        }
    });

    Ext.reg('cronks-tackle-information-head', Icinga.Cronks.Tackle.Information.Head);

})();