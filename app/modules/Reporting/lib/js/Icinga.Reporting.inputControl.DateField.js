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

Ext.ns('Icinga.Reporting.inputControl');

Icinga.Reporting.inputControl.DateField = Ext.extend(Ext.form.DateField, {

    setToNow: false,

    constructor : function(config) {
        Icinga.Reporting.inputControl.DateField.superclass.constructor.call(this, config);
    },

    initComponent : function() {
        Icinga.Reporting.inputControl.DateField.superclass.initComponent.call(this);

        if (!Ext.isEmpty(this.setToNow) && this.setToNow === true) {
            var now = new Date();
            this.setValue(now);
        }
    }

});
