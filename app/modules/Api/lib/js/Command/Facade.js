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