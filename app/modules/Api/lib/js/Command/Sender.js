/*global Ext: false, Icinga: false, AppKit: false, _: false */

Ext.ns('Icinga.Api.Command');

(function () {
    "use strict";
    Icinga.Api.Command.Sender = Ext.extend(Ext.util.Observable, {

        apiCommandUrl: null,
        targets: [],
        data: {},
        command: null,
        autoReset: false,

        constructor: function (config) {
            config = config || {};

            this.name = config.name;

            this.addEvents({
                beforeSend: true,
                sent: true,
                error: true,
                success: true
            });

            this.listeners = config.listeners;

            Ext.apply(this, config);

            if (Ext.isEmpty(this.apiCommandUrl)) {
                this.apiCommandUrl = String.format('{0}/web/api/cmd/', AppKit.util.Config.get('path'));
            }

            Icinga.Api.Command.Sender.superclass.constructor.call(this, config);
        },

        addTarget: function (mixed) {
            if (Ext.isObject(mixed)) {
                this.targets.push(mixed);
            } else if (Ext.isArray(mixed)) {
                Ext.each(mixed, function (item) {
                    this.targets.push(item);
                }, this);
            }
        },

        setData: function (o) {
            return Ext.apply(this.data, o);
        },

        setCommand: function (cname) {
            this.command = cname;
        },

        reset: function () {
            this.command = null;
            this.target = [];
            this.data = {};
        },

        send: function () {
            var data = {
                command: this.command,
                target: this.targets,
                data: this.data
            };

            if (this.fireEvent('beforeSend', data, this) === false) {
                return false;
            }

            Ext.iterate(data, function (k, v, o) {
                if (Ext.isPrimitive(v) === false) {
                    o[k] = Ext.encode(v);
                }
            });

            Ext.Ajax.request({
                url: this.apiCommandUrl,
                params: data,
                method: 'POST',
                callback: this.processResponse,
                scope: this
            });

            return this.fireEvent('sent', data, this);
        },

        processResponse: function (options, success, response) {
            if (success === true) {

                if (this.autoReset === true) {
                    this.reset();
                }

                return this.fireEvent('success', response, this);
            } else {
                return this.fireEvent('error', response, this);
            }
        }

    });


})();