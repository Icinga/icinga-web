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

Ext.ns('Icinga.Api.Command.Type');

(function () {
    "use strict";

    Icinga.Api.Command.Type.Generic = Ext.extend(Icinga.Api.Command.Type.Abstract, {

        buildForm: function (o) {
            Ext.iterate(o.parameters, function (key, value) {
                if (this.isSourceField(key) === false) {
                    var field = this.getFieldByName(key, value);
                    this.add(field);
                }
            }, this);

            Icinga.Api.Command.Type.Generic.superclass.buildForm.call(this, o);
        },

        getFieldByName: function (fieldName, fieldParams) {

            var oDef = this.getExtFieldDefinition(fieldParams);

            this.changeFieldAttributes(oDef, fieldName, fieldParams);

            var field = Ext.ComponentMgr.create(oDef, fieldName);

            return field;
        },

        changeFieldAttributes: function (oDef, fieldName, fieldParams) {
            if (fieldName === 'COMMAND_AUTHOR') {
                oDef.value = AppKit.getPreferences().author_name;
            }

            if (fieldParams.type === 'ro') {
                oDef.readOnly = true;
            }

            return oDef;
        }
    });
})();
