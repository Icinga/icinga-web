/*global Ext: false, Icinga: false, AppKit: false, _: false */

Ext.ns('Icinga.Api.Command');

(function () {
    "use strict";
    
    Icinga.Api.Command.Facade = (new(Ext.extend(Object, {

        commandSender: null,

        commandInfo: null,

        constructor: function () {
            Object.prototype.constructor.call(this);

            this.commandSender = new Icinga.Api.Command.Sender({
                autoReset: true
            });

            this.commandInfo = new Icinga.Api.Command.Info({
                autoLoad: false
            });

            this.commandInfo.loadCommandDefinitions.defer(300, this.commandInfo);
        },

        /**
         * Interface method to send commands quickly
         * <pre><code>
         * Icinga.Api.Command.Facade.sendCommand({
         *     command : 'ADD_HOST_COMMENT',
         *     data : {author: 'test_author', comment: 'test_comment', persistent : 1},
         *     targets : [ {instance: 'default', host: 'test_host1'} ]
         * });
         * </code></pre>
         */
        sendCommand: function (o) {
            Ext.copyTo(this.commandSender, o, ['command', 'targets', 'data']);

            this.commandSender.send();
        },

        getCommand: function (commandNameOrEmpty) {
            return this.commandInfo.get(commandNameOrEmpty);
        }

    }))());
})();