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
    
    /**
     * @class
     * @augments Ext.util.Observable
     * 
     * Cronk which dispatches MetaGrid components after fetching meta data
     * from xml provider
     */
    Icinga.Cronks.System.TemplateGrid = Ext.extend(Ext.util.Observable, {
        
        /**
         * @static
         * @property {Ext.util.MixedCollection} metaCache
         * @type Ext.util.MixedCollection
         * 
         * Caching templates to avoid requesting through HTTP/GET operation
         */
        metaCache: new Ext.util.MixedCollection(),
        
        /**
         * @constructor
         * Creating the object
         * @param {Object} config
         */
        constructor: function(config) {
            config = config || {};
            
            this.name = config.name;
            
            this.addEvents({
                /**
                 * @event beforemeta
                 * @param {Icinga.Cronks.System.TemplateGrid} o Creator instance
                 * @param {String} url
                 */
                beforemeta: true,
                
                /**
                 * @event meta
                 * @param {Icinga.Cronks.System.TemplateGrid} o Creator instance
                 * @param {Object} meta Metadata
                 */
                meta: true,
                
                /**
                 * @event beforecreation
                 * @param {Object} meta Metadata
                 * @param {Error} error Error object if one
                 */
                beforecreation: true,
                
                /**
                 * @event creation
                 * @param {Cronk.grid.MetaGridPanel} grid The created component
                 * @param {Error} error Error if one
                 * 
                 */
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
        
        /**
         * Adds settings to configuration which creates the object
         * @param {Object} o
         */
        addComponentConfig: function(o) {
            Ext.apply(this.componentConfig, o);
        },
        
        /**
         * Getter for component config. You can use defaults here
         * if you're not sure that all required settings are configured
         * @param {Object} defaults
         */
        getComponentConfig: function(defaults) {
            return Ext.apply({}, this.componentConfig, defaults || {});
        },
        
        /**
         * Set the template name
         * @param {String} template
         */
        setTemplate: function(template) {
            this.template = template;
        },
        
        /**
         * Getter for template name
         * @return {String}
         */
        getTemplate: function() {
            return this.template;
        },
        
        /**
         * Setter for meta url (place where the JSON meta provider lives)
         * @param {String} metaurl
         */
        setMetaUrl: function(metaurl) {
            this.metaurl = metaurl;
        },
        
        /**
         * Setter for data url (where the data provider lives)
         * @param {String} dataurl
         */
        setDataUrl: function(dataurl) {
            this.dataurl = dataurl;
        },
        
        /**
         * Url processor. Applies the template name to needed url "{0}"
         * @param {String} base
         * @returns {String}
         * @private
         */
        getUrl: function(base) {
            return String.format(base, this.getTemplate());
        },
        
        /**
         * Getter for meta url
         * @return {String}
         */
        getMetaUrl: function() {
            return this.getUrl(this.metaurl);
        },
        
        /**
         * Getter for data url
         * @return {String}
         */
        getDataUrl: function() {
            return this.getUrl(this.dataurl);
        },
        
        /**
         * Setter for the component
         * @param {Cronk.grid.MetaGridPanel} c
         */
        setComponent: function(c) {
            this.component = c;
        },
        
        /**
         * Getter for the component
         * @return {Cronk.grid.MetaGridPanel}
         */
        getComponent: function() {
            return this.component;
        },
        
        /**
         * Transit container for cronk interface parameters
         * @param {Object} parameter
         */
        setParameter: function(parameter) {
            this.parameter = parameter;
        },
        
        /**
         * Getter for transit parameters
         * @return {Object}
         */
        getParameter: function() {
            return this.parameter;
        },
        
        /**
         * Async method to fetch JSON meta data and execute
         * callback after request succeed / failed
         * @param {Function} callbackFn
         */
        getJson: function(callbackFn) {
            var url = this.getMetaUrl();
            if (this.fireEvent("beforemeta", this, url) === true) {
                
                // Try to create grid from cached meta data
                if (this.metaCache.containsKey(this.getTemplate())) {
                    var meta = this.metaCache.get(this.getTemplate());
                    if (this.fireEvent("meta", this, meta) === true) {
                        callbackFn.call(this, meta);
                    }
                } else {
                
                    Ext.Ajax.request({
                        url: this.getMetaUrl(),
                        callback: function(options, success, response) {
                            //try {
                                if (success===true) {
                                    var object = Ext.decode(response.responseText);
                                    this.metaCache.add(this.getTemplate(), object);
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