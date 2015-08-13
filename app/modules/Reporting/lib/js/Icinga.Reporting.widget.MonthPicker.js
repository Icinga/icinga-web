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

Ext.ns('Icinga.Reporting.widget');

Icinga.Reporting.widget.MonthPicker = Ext.extend(Ext.ux.form.MultiSelect, {
    
    constructor : function(config) {
        
        this.store = [
            [1, _('Jan')],
            [2, _('Feb')],
            [3, _('Mar')],
            [4, _('Apr')],
            [5, _('May')],
            [6, _('Jun')],
            [7, _('Jul')],
            [8, _('Aug')],
            [9, _('Sep')],
            [10, _('Oct')],
            [11, _('Nov')],
            [12, _('Dec')]
        ];
        
        Icinga.Reporting.widget.MonthPicker.superclass.constructor.call(this, config);
    },
    
    initComponent : function() {
        Icinga.Reporting.widget.MonthPicker.superclass.initComponent.call(this);
    }
    
});
