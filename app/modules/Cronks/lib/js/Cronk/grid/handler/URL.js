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

Ext.ns("Cronk.grid.handler");

(function () {
    
    "use strict";
    
    /**
     * @static
     * Handlers for working with URLs
     */
    Cronk.grid.handler.URL = {
        
        /**
         * Open a url in a inline iframe cronk
         * @static
         * @cfg {String} url URL to open, allow {} for data wrappers
         * @cfg {String} cronkTitle title of the open tab, allow {}
         * @cfg {Boolean} activateOnClick Jump into after activate
         */
        open: function() {
            var url = this.getHandlerArgTemplated("url");
            var cronkIcon = this.getHandlerArg("cronkIcon", "icinga-cronk-icon-database");
            var title = this.getHandlerArgTemplated("cronkTitle", "untitled");
            
            var tabPanel = Ext.getCmp("cronk-tabs");
            var cmp = tabPanel.add({
                xtype: "cronk",
                title: title,
                crname: "genericIFrame",
                iconCls: cronkIcon,
                params: {
                    url: encodeURI(url),
                    connection: this.getGrid().selectedConnection
                },
                closable: true
            });
            
            tabPanel.doLayout();
            
            if (this.getHandlerArg("activateOnClick", false)) {
                tabPanel.setActiveTab(cmp);
            }
        },
        
        /**
         * Open a new window displaying the url
         * @cfg {String} url Templated string of the url ({})
         */
        openExternal: function() {
            var url = this.getHandlerArgTemplated("url");

            //Cronk.grid.ColumnRendererUtil()
            window.open(url);
        },
        
        /**
         * @private
         * @static
         * Helper function to create content panels for later, concrete use
         * @param {Object} windowConfig
         * @param {Ext.Element} targetEl
         * @param {Object} args
         * @return {Ext.Window}
         */
        contentHelperPanel: function(windowConfig, targetEl, args) {
            windowConfig = Ext.apply(windowConfig || {}, {
                closeAction: "close",
                closable: true,
                renderTo: Ext.getBody(),
                iconCls: (args.iconCls) ? args.iconCls : "icinga-icon-image",
                hidden: true,
                audoScroll: true,
                bbar: ["->", {
                        text: _("Close"),
                        iconCls: "icinga-action-icon-cancel",
                        handler: function(button, event) {
                            var win = button.findParentByType("window");
                            win.close();
                        }
                }]
            });
            
            if (args.width) {
                windowConfig.width = parseInt(args.width, 10) + 14;
            }
            
            if (args.height) {
                windowConfig.height = parseInt(args.height, 10) + 14;
            }
            
            var win = new Ext.Window(windowConfig);
            
            win.render();
            
            if (args.title) {
                win.setTitle(args.title);
            }
            
            win.alignTo(targetEl, "tl-t?");
            
            return win;
        },
        
        /**
         * Opens a image panel with the image as content
         * 
         * All other handler arguments will be added to the img tag, 
         * e.g. name, style, class, and so on.
         * 
         * @cfg {String} src Image source, templated string
         * @cfg {String} title Window and image title, templated
         * @cfg {String} iconCls Css class of the window icon
         * @cfg {Number} height Height of window and image
         * @cfg {Number} width Width of window and image
         * @static
         */
        imagePanel: function() {
            var args = this.getHandlerArgsTemplated();
            
            var ignoreTags = ["src"];
            var src = args.src;
            var tags = [];
            
            Ext.iterate(args, function(key, val) {    
                if (ignoreTags.indexOf(key) < 0) {
                    tags.push(key + "=" + "\"" + val + "\"");
                }
            }, this);
            
            var win = Cronk.grid.handler.URL.contentHelperPanel({
                tpl: new Ext.XTemplate([
                    "<img src=\"{src}\"{tags} />"
                ].join(""))
            }, this.getEl(), args);

            win.update({
                src: src,
                tags: (tags.length>0) ? " " + tags.join(" ") : ""
            });
            
            win.show();
        },
        
        /**
         * This could be used for including small HTML snippets (inline, this
         * is no iframe). You can display foreign content or information
         * related on that object
         * @cfg {String} title Window, templated
         * @cfg {String} iconCls Css class of the window icon
         * @cfg {String} url request url
         * @cfg {Object} params object to post as parameters
         * @cfg {String} method GET or POST
         * @cfg {Number} timeout Timeout in milliseconds
         * @cfg {Object} headers Custom headers to send
         * @cfg {String} XML document to post to
         * @cfg {String} Json document to post (could be also an object)
         * @cfg {Boolean} disableCaching Like the name said
         * 
         */
        htmlContentPanel: function() {
            var args = this.getHandlerArgsTemplated();
            
            var win = Cronk.grid.handler.URL.contentHelperPanel({
                tpl: new Ext.XTemplate([
                    "<div style=\"overflow:auto;\" class=\"icinga-url-handler-inline-html\">",
                    "{content}",
                    "</div>"
                ].join(""))
            }, this.getEl(), args);
            
            var requestConfig = Ext.copyTo({}, args, [
                "url", "params", "method", "timeout", "headers", "xmlData",
                "jsonData", "disableCaching"
            ]);
            
            win.on("beforeshow", function() {
                
                var loadMask = win.getEl().mask(_("Loading ressource ..."));
                
                Ext.Ajax.request(Ext.apply(requestConfig, {
                    callback: function(options, success, response) {
                        
                        var content = "";
                        
                        if (success === true) {
                            content = response.responseText;
                        } else {
                            content = "<h1>ERROR</h1>" +
                            String.format(
                                "<code>Status code: {0}</code><br />" +
                                "<code>Status message: {1}</code>",
                                response.status,
                                response.statusText
                            );
                        }
                        
                        win.update({
                            content: content
                        });
                        
                        win.getEl().unmask();
                    }
                }));                
            }, this, {single:true});
            
            win.show();
        }
        
    };
    
})();