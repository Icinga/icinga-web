// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
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

Ext.ns('Icinga.Reporting.util');

 Icinga.Reporting.util.InputControlBuilder = Ext.extend(Object, {
    
    removeAll : false,
    
    constructor : function(config) {
        Icinga.Reporting.util.InputControlBuilder.superclass.constructor.call(this);
        
        this.initialConfig = config;
        Ext.apply(this, config);
        
        this.items = new Ext.util.MixedCollection();
    },
    
    setTarget : function(target) {
        this.target = target;
    },
    
    setControlStruct : function(controlStruct) {
        this.controlStruct = controlStruct;
    },
    
    buildFormItems : function() {
        this.items.clear();
        
        var namePrefix = this.namePrefix || '';
        
        Ext.iterate(this.controlStruct, function(k,v) {
            var inputConfig = {};
            
            Ext.apply(v.jsControl, {
                hidden : v.PROP_INPUTCONTROL_IS_VISIBLE=="false" ? true : false,
                readonly : v.PROP_INPUTCONTROL_IS_READONLY=="true" ? true : false,
                name : namePrefix + v.name,
                width: 250,
                fieldLabel : v['label'],
                allowBlank : false
            });
            
            Ext.applyIf(v.jsControl, Icinga.Reporting.DEFAULT_JSCONTROL);
            
            inputConfig = v.jsControl;
            
            if (!Ext.isEmpty(inputConfig.className)) {
                var inputClass = eval('window.' + inputConfig.className);
                var inputControl = new inputClass(inputConfig);
                this.items.add(inputConfig.name, inputControl);
            }
            
        }, this);
        
        return this.items;
    },
    
    applyToTarget : function(target) {
        target = target || this.target;
        
        if (this.items.getCount() < 1) {
            this.buildFormItems();
        }
        
        if (this.removeAll == true) {
            target.removeAll(true);
        }
        
        this.items.each(function(item, index, len) {
            target.add(item);
        }, this);
        
        target.doLayout();
    }
    
});