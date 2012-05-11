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

Ext.ns('Ext.ux');
Ext.ux.AutoComboBox = Ext.extend(Ext.form.ComboBox, {
    
    minChars : 0,
    
    height : 30,
    
    pageSize : 20,
    
    
    constructor : function(cfg) {
        cfg = cfg || {};
        cfg.storeCfg = cfg.storeCfg || {};
            
        Ext.applyIf(cfg, {
            triggerAction : 'all',
            
            listEmptyText : _('No results...'),
            editable : true
            //tpl : '<tpl for="."><div ext:qtip="{{0}}" class="x-combo-list-item">{{0}}</div></tpl>'.format(cfg.name), 
        });
    
        
    
        
        Ext.form.ComboBox.prototype.constructor.call(this, cfg);
        
        this.store.on({
            beforeload : function(store, options) {
                var value = options.params[this.valueField] || store.baseParams[this.valueField];
                
                if(value) {
                    if(value.charAt(0) != '%') {
                        value = '%' + value;
                    }
                    if(value.charAt(value.length-1) != '%') {
                        value += '%';
                    }
                    store.setBaseParam(this.valueField, value);
                } else {
                    store.setBaseParam(this.valueField, '%');
                }
            },
            scope : this
        });
    },
    
    onRender : function() {
        Ext.ux.AutoComboBox.superclass.onRender.apply(this, arguments);
        
        this.el.on({
            click : function() {
                this.selectText();
                this.getStore().reload();
            },
            scope : this
        });
    }
    
});

Ext.reg('autocombo', Ext.ux.AutoComboBox);

