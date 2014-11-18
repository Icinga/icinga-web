// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2014 Icinga Developer Team.
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