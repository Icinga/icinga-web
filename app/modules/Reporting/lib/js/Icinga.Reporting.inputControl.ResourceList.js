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

Ext.ns('Icinga.Reporting.inputControl');

Icinga.Reporting.inputControl.ResourceList = Ext.extend(Ext.form.ComboBox, {
    
    triggerAction: 'all',
    displayField: 'label',
    valueField: 'id',
    mode: 'local',
    typeAhead: true,
    
    constructor : function(config) {
        config.hiddenName = config.name;
        Icinga.Reporting.inputControl.ResourceList.superclass.constructor.call(this, config);
    },
    
    initComponent : function() {
        if (this.jsData) {
            var arrayData = [];
            Ext.iterate(this.jsData, function(k, v) {
                arrayData.push([k, v]);
            }, this);
            
            this.store = new Ext.data.ArrayStore({
                autoDestroy: true,
                fields: ['id', 'label'],
                data: arrayData
            });
        }
        
        Icinga.Reporting.inputControl.ResourceList.superclass.initComponent.call(this);
    }
    
});
