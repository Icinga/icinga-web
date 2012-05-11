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

Ext.ns('Icinga.Reporting.abstract');

Icinga.Reporting.abstract.ApplicationWindow = Ext.extend(Ext.Panel, {
    
    constructor : function(config) {
        Icinga.Reporting.abstract.ApplicationWindow.superclass.constructor.call(this, config);
    },
    
    initComponent : function() {
        if (Ext.isEmpty(this.plugins)) {
            this.plugins = [];
        }
        
        this.plugins.push(new Ext.ux.plugins.ContainerMask ({
            msg : Ext.isEmpty(this.mask_text) ? _('Please be patient') : this.mask_text,
            masked : Ext.isEmpty(this.mask_show) ? false :  this.mask_show
        }));
        
        Icinga.Reporting.abstract.ApplicationWindow.superclass.initComponent.call(this);
    },
    
    setToolbarEnabled : function(bool, pos) {
        if (Ext.isEmpty(bool)) {
            bool = true;
        }
        var i = 0;
        this.getTopToolbar().items.eachKey(function(key, item) {
            i++;
            if (!Ext.isEmpty(pos)) {
                if (Ext.isArray(pos)) {
                    if (pos.indexOf(i) >= 0) {
                        item.setDisabled(!bool);
                    }
                }
                else {
                    if (pos == i) {
                        item.setDisabled(!bool);
                    }
                }
            }
            else {
                item.setDisabled(!bool);
            }
        });
    }
    
});