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

Ext.ns('AppKit.search');

(function() {
    "use strict";
    
    AppKit.search.Searchbox = Ext.extend(Ext.form.TextField, {
        enableKeyEvents : true,
        id : 'global_search',
        name : 'global_search',
        cls: 'icinga-global-search-box',
        width : 170,
        active: false,
        inactiveLabel: _('Press CTRL+ALT+F ...'),
        inactiveStyle: {
            color: '#c0c0c0'
        },
        activeLabel: '',
        activeStyle: {
            color: '#000'
        },
        
        margins: {
            top: '2px'
        },
        
        constructor: function(c) {
            AppKit.search.Searchbox.superclass.constructor.call(this, c);
        },
        
        initComponent: function() {
            AppKit.search.Searchbox.superclass.initComponent.call(this);
            
            this.keymap = new Ext.KeyMap(Ext.getDoc(), {
                key : Ext.EventObject.F,
                ctrl : true,
                alt : true,
                fn : (function() {
                    this.focus();
                }).createDelegate(this)
            });
            
            this.on('keyup', this.processSearch, this);
            this.on('keydown', this.submitSearch, this);
            this.on('blur', this.resetSearch, this);
            this.on('focus', this.prepareSearch, this);
            
            AppKit.search.SearchHandler.on('deactivate', this.forceResetSearch, this);
            
            AppKit.search.SearchHandler.on('activate', function() {
                this.active = true;
            }, this);
            
            AppKit.search.SearchHandler.on('deactivate', function() {
                this.active = false;
            }, this);
            
            this.on('afterrender', function() {
                this.resetSearch();
            }, this);
        },
        
        resetSearch: function(field, event) {
            if (!this.active) {
                this.forceResetSearch(field, event);
            }
        },
        
        forceResetSearch: function(field, event) {
            this.setValue(this.inactiveLabel);
            this.getEl().setStyle(this.inactiveStyle);
        },
        
        prepareSearch: function(field, event) {
            if (!this.active) {
                this.setValue(this.activeLabel);
                this.getEl().setStyle(this.activeStyle);
            }
        },
        
        processSearch: function(field, event) {
            AppKit.search.SearchHandler.doSearch(field.getValue());
        },
        
        submitSearch: function(field, event) {
            if (event.getCharCode() === Ext.EventObject.ENTER) {
                AppKit.search.SearchHandler.doSearch(field.getValue(), 'submit');
            }
        },
        
        stopSearch: function(field, event) {
            AppKit.search.SearchHandler.deactivate();
            
        }
        
    });
})();