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

/*global Ext: false, Icinga: false, AppKit: false, _: false */

Ext.ns('Icinga.Api.Command');

(function () {
    "use strict";

    Icinga.Api.Command.FormBuilder = Ext.extend(Object, {
        constructor: function () {
            Icinga.Api.Command.FormBuilder.superclass.constructor.call(this);
        },

        build: function (command, options) {
            if (Ext.isString(command)) {
                command = Icinga.Api.Command.Facade.getCommand(command);
            }

            if (Ext.isObject(command) === false) {
                throw ("Got no command object");
            }

            var subClass = this.getSubClassName(command.definition);

            if (!(subClass in Icinga.Api.Command.Type)) {
                subClass = "Generic";
            }

            var DynamicClass = Icinga.Api.Command.Type[subClass];

            options = options || {};
            options.command = command;

            var cObject = new DynamicClass(options);

            return cObject;
        },

        getSubClassName: function (commandName) {
            return commandName.split('_').map(function (v) {
                return v.charAt(0).toUpperCase() + v.substr(1).toLowerCase();
            }).join('');
        }
    });
})();
