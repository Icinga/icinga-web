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

Ext.ns('AppKit.search', 'AppKit.lib');

AppKit.search.SearchHandler = (new (Ext.extend(Ext.util.Observable, {
    
    query : "",
    
    handlers : [],
    
    searchBox : null,
    
    constructor : function(config) {
        
        config = config || {};
        
        this.addEvents({
            'activate' : true,
            'deactivate' : true,
            'process' : true,
            'submit' : true
        })
        
        this.listeners = config.listeners;
        
        Ext.util.Observable.prototype.constructor.call(this, config);
    },
    
    setSearchbox : function(cmp) {
        this.searchBox = cmp;
    },
    
    getSearchbox : function() {
        return this.searchBox;
    },
    
    getTargetElement : function() {
        return this.searchBox.getEl();
    },
    
    getQuery : function() {
        return this.query;
    },
    
    doSearch : function(query, event) {
        
        if (Ext.isEmpty(event)) {
            event = 'process';
        }
        
        if (query !== this.query || event == 'submit') {
            if (this.fireEvent(event, this, query) !== false) {
                this.query = query;
            }
        }
    },
    
    activate : function(wnd, field) {
        return this.fireEvent('activate', this, wnd, field);
    },
    
    deactivate : function() {
        if (this.fireEvent('deactivate', this) !== false) {
            this.query = "";
        }
    },
    
    registerHandler : function(fn, scope) {
        Ext.util.Observable.capture(this, fn, scope || this);
        this.handlers.push([fn, scope]);
    },
    
    isReady : function() {
        return (this.handlers.length || this.hasListener('process'));
    }
    
})));