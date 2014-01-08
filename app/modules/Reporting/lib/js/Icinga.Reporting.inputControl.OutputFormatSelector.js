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

Ext.ns('Icinga.Reporting.inputControl');

Icinga.Reporting.inputControl.OutputFormatSelector = Ext.extend(Ext.form.ComboBox, {
    
    constructor : function(config) {
        config = Ext.apply(config || {}, {
            typeAhead : 'true',
            mode : 'local',
            triggerAction : 'all',
            valueField : 'id',
            displayField : 'label',
            store : new Ext.data.ArrayStore({
                autoDestroy : true,
                fields: ['id', 'label'],
                data : [
                    ['pdf', _('PDF')],
                    ['csv', _('Comma seperated spreadsheet')],
                    ['xls', _('Microsoft Excel')],
                    ['rtf', _('Rich text format')],
                    ['html', _('HTML')],
                    ['xml', _('XML')]
                ]
            })
        });
        
        config.hiddenName = config.name
        
        Icinga.Reporting.inputControl.OutputFormatSelector.superclass.constructor.call(this, config);
    },
    
    initComponent : function() {
        Icinga.Reporting.inputControl.OutputFormatSelector.superclass.initComponent.call(this);
        
        this.setValue('pdf');
    }
    
});
