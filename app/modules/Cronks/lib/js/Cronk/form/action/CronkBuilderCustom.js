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
/*global Ext: false, Icinga: false, AppKit: false, _: false, Cronk: false */

Ext.ns('Cronk.form.action');

(function() {
    
    "use strict";
    
    Cronk.form.action.CronkBuilderCustom = Ext.extend(Ext.form.Action.Submit, {
        
        constructor : function(form, options, propertyGrid) {
            Cronk.form.action.CronkBuilderCustom.superclass.constructor.call(this, form, options);
            
            this.propertyGrid = propertyGrid;
        },
        
        getParams : function() {
            var buff = Cronk.form.action.CronkBuilderCustom.superclass.getParams.call(this);
            
            var np = {};
            
            var data = this.propertyGrid.getSource();
            
            Ext.iterate(data, function(k, v) {
                np['p[' + k + ']='] = v;
            });
            
            if (!Ext.isEmpty(buff)) {
                buff += '&';
            }
            
            buff += Ext.urlEncode(np);
            
            return buff;
        }
    });
    
})();