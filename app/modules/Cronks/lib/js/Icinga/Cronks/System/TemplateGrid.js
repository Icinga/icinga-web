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

/*global Ext: false, Icinga: false, AppKit: false, _: false, Cronk: false */

Ext.ns("Icinga.Cronks.System");

(function() {
    "use strict";
    
    Icinga.Cronks.System.TemplateGrid = Ext.extend(Ext.util.Observable, {
        
        constructor: function(config) {
            config = config || {};
            
            this.name = config.name;
            
            this.addEvents({
                beforemeta: true,
                meta: true,
                beforecreation: true,
                creation: true
            });
            
            this.listeners = config.listeners;
            
            this.setDataUrl(AppKit.util.Config.get("baseurl") +
                "/modules/cronks/viewproc/{0}/json");
            
            this.setMetaUrl(AppKit.util.Config.get("baseurl") +
                "/modules/cronks/viewproc/{0}/json/inf");
            
            this.componentConfig = {};
            
            Icinga.Cronks.System.TemplateGrid.superclass.constructor.call(this, config);
        },
        
        addComponentConfig: function(o) {
            Ext.apply(this.componentConfig, o);
        },
        
        getComponentConfig: function(defaults) {
            return Ext.apply({}, this.componentConfig, defaults || {});
        },
        
        setTemplate: function(template) {
            this.template = template;
        },
        
        getTemplate: function() {
            return this.template;
        },
        
        setMetaUrl: function(metaurl) {
            this.metaurl = metaurl;
        },
        
        setDataUrl: function(dataurl) {
            this.dataurl = dataurl;
        },
        
        /**
         * Url processor
         * @param {String} base
         * @returns {String}
         * @private
         */
        getUrl: function(base) {
            return String.format(base, this.getTemplate());
        },
        
        getMetaUrl: function() {
            return this.getUrl(this.metaurl);
        },
        
        getDataUrl: function() {
            return this.getUrl(this.dataurl);
        },
        
        setComponent: function(c) {
            this.component = c;
        },
        
        getComponent: function() {
            return this.component;
        },
        
        setParameter: function(parameter) {
            this.parameter = parameter;
        },
        
        getParameter: function() {
            return this.parameter;
        },
        
        getJson: function(callbackFn) {
            var url = this.getMetaUrl();
            if (this.fireEvent("beforemeta", this, url) === true) {
                Ext.Ajax.request({
                    url: this.getMetaUrl(),
                    callback: function(options, success, response) {
                        //try {
                            if (success===true) {
                                var object = Ext.decode(response.responseText);
                                if (this.fireEvent("meta", this, object) === true) {
                                    callbackFn.call(this, object);
                                }
                            } else {
                                throw new Error("HTTP/" + response.status + 
                                    " " + response.statusText);
                            }
                        //} catch(e) {
                        //    callbackFn.call(this, {}, new Error("Object/" + e.message));
                        //}
                    },
                    scope: this
                });
            }
        },
        
        /**
         * Configures the cronk and return it
         * @returns {Cronk.grid.MetaGridPanel}
         */
        buildComponent: function() {
            this.getJson(function(object, err) {
                if (this.fireEvent("beforecreation", object, err) === true) {
                    if (typeof err !== "undefined") {
                        var panel = new Ext.Panel({
                            html: "<h1 style=\"margin: 10px;\">" +
                                "Could not open grid: " + err.message +
                                "</h1><br /><pre style=\"margin: 10px\">" + 
                                err.stack + "</pre>"
                        });
                        
                        this.setComponent(panel);
                    } else {
                        var grid = new Cronk.grid.MetaGridPanel(this.getComponentConfig({
                            meta: object,
                            url: this.getDataUrl(),
                            parameters: this.getParameter()
                        }));
                        
                        this.setComponent(grid);
                    }
                    
                    this.fireEvent("creation", this.getComponent(), err);
                }
            });
        }
        
    });
    
})();