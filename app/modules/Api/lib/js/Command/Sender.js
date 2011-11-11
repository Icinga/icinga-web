/*global Ext: false, Icinga: false, AppKit: false, _: false */

Ext.ns('Icinga.Api.Command');

(function () {
    "use strict";
    Icinga.Api.Command.Sender = Ext.extend(Ext.util.Observable, {

        apiCommandUrl: null,
        targets: [],
        data: {},
        command: null,
        autoReset: true,
        
        recordTargetMap : {
        	HOST_NAME : 'host',
        	SERVICE_NAME : 'service',
        	INSTANCE_NAME : 'instance'
        },

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
                this.apiCommandUrl = String.format('{0}/web/api/cmd/json', AppKit.util.Config.get('path'));
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
        
        /**
         * If you added records to the targets this method
         * converts record data based on a map to "real"
         * target values
         */
        prepareTargets : function(targets) {
        	var outTargets = [];
        	
        	Ext.each(targets, function(o, index) {
        		// Hack to detect a record
        		if (Ext.isObject(o) && Ext.isObject(o.data)) {
        			var tmp = {};
        			Ext.iterate(this.recordTargetMap, function(k, v) {
        				if (Ext.isEmpty(o.data[k]) === false) {
        					tmp[v] = o.data[k];
        				}
        			}, this);
        			outTargets.push(tmp);
        		} else {
        			outTargets.push(o);
        		}
        	}, this);
        	
        	return outTargets;
        },
        
        send: function () {
        	
        	var targets = this.prepareTargets(this.targets);
        	
            var data = {
                command: this.command,
                target: targets,
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
                successProperty: 'success',
                scope: this
            });

            return this.fireEvent('sent', data, this);
        },

        processResponse: function (options, success, response) {
            var jsonResponse = Ext.decode(response.responseText);
            if (success === true && jsonResponse.success === true) {

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