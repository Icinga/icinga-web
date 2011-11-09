/*global Ext: false, Icinga: false, AppKit: false, _: false */

Ext.ns('Icinga.Api.Command');

(function () {
    "use strict";
    Icinga.Api.Command.FormAction = Ext.extend(Ext.form.Action, {

        clientValidation: true,

        targets: [],

        command: {},

        constructor: function (form, options) {

            Ext.applyIf(options, {
                clientValidation: true,
                waitMsg: _('Sending command . . .')
            });

            Icinga.Api.Command.FormAction.superclass.constructor.call(this, form, options);

            if (Ext.isEmpty(options.targets) === false) {
                this.setTargets(options.targets);
            }

            if (Ext.isEmpty(options.command) === false) {
                this.setCommand(options.command);
            }

            this.commandSender = new Icinga.Api.Command.Sender({
                autoReset: true
            });
           
        },

        setTargets: function (targets) {
            this.targets = targets;
        },

        setCommand: function (command) {
            this.command = command;
        },

        run: function () {
            this.commandSender.on('success', this.success,this,{single:true});
            this.commandSender.on('error', this.failure,this,{single:true});

            var o = this.options;
            if (o.clientValidation === false || this.form.isValid()) {
                var data = this.form.getFieldValues();

                this.commandSender.setCommand(this.command.definition);
                this.commandSender.setData(data);
                this.commandSender.addTarget(this.targets);

                this.commandSender.send();

            } else if (o.clientValidation !== false) { // client validation failed
                this.failureType = Ext.form.Action.CLIENT_INVALID;
                this.form.afterAction(this, false);
            }
        },

        success: function () {
            this.form.clearInvalid();
            this.form.afterAction(this, true);
        },

        failure: function () {
            this.failureType = Ext.form.Action.SERVER_INVALID;
            this.form.afterAction(this, false);
        }
    });
})();