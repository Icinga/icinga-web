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

/*global Ext: false, Icinga: false, _: false */
Ext.ns('Icinga.Cronks.Tackle.Information');

(function () {
    "use strict";

     Icinga.Cronks.Tackle.Information.State = Ext.extend(Ext.grid.PropertyGrid, {
        title: _('State information'),
        clicksToEdit: 3,
        
        constructor: function (config) {
            Icinga.Cronks.Tackle.Information.State.superclass.constructor.call(this, config);
        },

        initComponent: function () {
            Icinga.Cronks.Tackle.Information.State.superclass.initComponent.call(this);
        },

        setSource: function (source) {
            source = this.translateNames(source);
            
            this.customEditors = this.createSimpleEditors(source);
            this.rewriteValues(source);

            Icinga.Cronks.Tackle.Information.State.superclass.setSource.call(this, source);
        },

        translateNames: function (source) {
            var newSource = {};
            Ext.iterate(source, function (key, val) {
                newSource[Icinga.Cronks.Tackle.Translation.get(key)] = val;
            }, this);
            return newSource;
        },
        
        createSimpleEditors : function(source) {
            var field = new Ext.grid.GridEditor(new Ext.form.TextField({
                selectOnFocus:false,
                readOnly : true
            }));
            
            var editors = {};
            
            Ext.iterate(source, function(key, val) {
                editors[key] = field;
            }, this);
            
            return editors;
        },
        
        rewriteValues : function(source) {
            Ext.iterate(source, function(key, val) {
                if (!val) {
                    source[key] = '(null)';
                }
               
            }, this);
        }
    });

})();