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

/*global Ext: false, Icinga: false, AppKit: false, _: false */

Ext.ns('Icinga.Api.Command');

(function () {
    "use strict";
    Icinga.Api.Command.Info = Ext.extend(Object, {

        infoUrl: null,
        loaded: false,
        data: {},
        autoLoad: false,

        constructor: function (config) {
            Icinga.Api.Command.Info.superclass.constructor.call(this);

            Ext.apply(this, config);

            this.infoUrl = String.format('{0}/web/api/cmdInfo/json', AppKit.util.Config.get("path"));
            
            if (this.autoLoad === true) {
                this.loadCommandDefinitions();
            }
        },

        loadCommandDefinitions: function () {
            if (this.loaded === true) {
                return true;
            }
            
            
            // Not logged in, abort loading of info
            if (!AppKit.getPrefVal("author_name")) {
                return;
            }

            var abort = false;

            Ext.Ajax.request({
                url: this.infoUrl,
                callback: function (options, success, response) {

                    if (success === false) {
                        this.data = {
                            success: false
                        };
                        this.loaded = false;
                        return false;
                    }
                    try {
                        var data = Ext.decode(response.responseText);
                        this.data = data.results;
                        this.loaded = data.success;
                    } catch (e) {
                        this.loaded = false;
                    }
                },
                scope: this
            });

            return true;
        },

        get: function (commandName) {
            if (Ext.isEmpty(commandName)) {
                return this.data;
            } else {
                return this.data[commandName];
            }
        }
    });
})();