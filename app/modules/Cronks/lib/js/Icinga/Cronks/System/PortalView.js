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

Ext.ns('Icinga.Cronks.System');
(function() {
    "use strict";
    
    Icinga.Cronks.System.PortalView = Ext.extend(Ext.ux.Portal, {
        defaultColumns : 1,
        layout: 'column',
        autoScroll: true,
        border: false,
        stateEvents: ['add', 'remove', 'titlechange', 'resize'],
        
        constructor: function(config) {
            Icinga.Cronks.System.PortalView.superclass.constructor.call(this, config);
        },
        
        initItemsConfig: function() {
            var columnWidth = Math.floor(100 / this.defaultColumns) / 100;
            var items_config = [];
            for (var i=0; i<this.defaultColumns; i++) {
                items_config[i] = {
                    columnWidth: columnWidth,
                    style: 'padding: 3px;'
                };
            }
            return items_config;
        },
        
        /**
         * Create the defautl tool set which
         * is used for every cronk created within
         */
        initTools : function() {
            this.defaultTools = [{
                id: 'gear',
                handler: function(e, target, panel) {
                    var msg = Ext.Msg.prompt(_("Enter title"), _("Change title for this portlet"), function(btn, text) {
                        if (btn == 'ok' && text) {
                            panel.setTitle(text);
                        }
                    }, this, false, panel.title);
    
                    msg.getDialog().alignTo(panel.getEl(), 'tr-tr');
                }
            },{
                id:'minus',
                handler: function(e, target, panel) {
                    Ext.each(panel.findByType('container'),function(item) {
    
                        if (!Ext.isEmpty(item.bbar)) {
                            if (!item.getBottomToolbar().hidden) {
                                item.getBottomToolbar().hide();
                                panel.barsHidden = true;
                            }
                            else {
                                item.getBottomToolbar().show();
                                panel.barsHidden = false;
                            }
                        }
    
                        if (!Ext.isEmpty(item.tbar)) {
                            if (!item.getTopToolbar().hidden) {
                                item.getTopToolbar().hide();
                                panel.barsHidden = true;
                            }
                            else {
                                item.getTopToolbar().show();
                                panel.barsHidden = false;
                            }
                        }
                            
                        item.syncSize();
    
                    });
                }
            },{
                id:'close',
                handler: function(e, target, panel) {
                    panel.destroy();
                }
    
            }];
        },
        
        /**
         * Prepares a new portlet to display the cronk itself
         * @param Cronk<Object>
         */
        initPortlet : function(portlet) {
            Cronk.Registry.add(portlet.initialConfig);
            
            portlet.on('afterlayout',function(ct) {
                
                var params = ct.initialConfig.params;
                
                params["stateuid"] = ct.stateuid;
                params["p[stateuid]"] = ct.stateuid,
                params["p[parentid]"] = ct.id;
                
                portlet.getUpdater().setDefaultUrl({
                    url: AppKit.util.Config.get('path') + '/modules/cronks/cloader/' + ct.crname,
                    params: params,
                    scripts: true                           
                });
                
                portlet.getUpdater().refresh();
            },this,{single:true});
    
            portlet.on("add",function(el,resp) {
                Ext.each(portlet.findByType('container'),function(item) {
                    item.setHeight(portlet.getInnerHeight());
                });
                
                AppKit.log(el);
            });
            
//          portlet.on('statesave', function(cmp, state) {
//              Ext.state.Manager.set(this.id, this.getState());
//              AppKit.log("CMP-statesave");
//          }, this);

            
            
            /**
             * Fix width and height
             * This must be done via one-shot eventdispatcher to avoid
             * endless recursion (resize->change width->width changed->resize->...)
             */
            var resizeFunc = function(el) {
                Ext.each(portlet.findByType('container'),function(item) {   
                    item.setWidth(portlet.getInnerWidth());
                    item.setHeight(portlet.getInnerHeight());
                });     
                // Attach the listener again after resize
                portlet.on('resize',resizeFunc,this,{single:true})
    
            }   
            portlet.on('resize',resizeFunc,this,{single:true}); 
        },
        
        /**
         * Creates the drop zone to communicate with the CronkListingPanel
         * to accept configuration comming from that
         */
        initCronkDropZone : function() {
            var p = this;
            var tools = this.defaultTools;
            
            this.dropZone = new Ext.dd.DropTarget(p.getEl(), {
                ddGroup : 'cronk',
                grid : null,
                ac : null,
                
                notifyOut : function(){
                    this.grid = null;
                    this.ac = null;
                },
                
                notifyOver: function(dd, e, data) {
                    
                    if (data.dragData.cronkid.indexOf('portalView') == 0) {
                        return this.dropNotAllowed;
                    }
    
                    if (!this.grid) {
                        this.grid = p.dd.getGrid();
                    }
    
                    var xy = e.getXY();
    
                    Ext.iterate(this.grid.columnX, function (item, index, arry) {
                        if (xy[0] >= item.x && xy[0] < item.x+item.w ) {
                            this.ac = index;
                            return this.dropNotAllowed;
                        }
    
                    }, this);
                    return Ext.dd.DropTarget.prototype.notifyOver.call(this, dd, e, data);
                },
                
                notifyDrop: function(dd, e, data) {
                    var params = {
                        module: 'Cronks',
                        action: 'System.PortalView',
                        'p[parentid]': id
                    };
                    data.dragData.parameter = data.dragData.parameter || {};
                    if (Ext.apply(data.dragData.parameter, data.dragData["ae:parameter"] || {})) {
                        for (var k in data.dragData.parameter) {
                            params['p[' + k + ']'] = data.dragData.parameter[k];
                        }
                    }
                    
    
                    var portlet  = Cronk.factory({
                        id: Ext.id(),
                        params: params,
                        crname: data.dragData.cronkid,
                        stateuid: Ext.id('cronk-sid'),
                        title: data.dragData.name,
                        closable: true,
                        stateful:true,
                        xtype: 'portlet',
                        tools: tools,
                        height: 200,
                        border: true
                        
                    });
                    
                    /*
                     * Register some handler and the cronk loader
                     * to load the cronk data it self. This is a
                     * hack between the portlet class which is used
                     * by the portal and registering "cronk" behaviour
                     * on that object to be a "real" cronk
                     */
                    p.initPortlet(portlet);
    
                    // Add them to the portal
                    p.items.get(this.ac || 0).add(portlet);
                
                    // Bubbling render event
                    portlet.show(); // Needed for webkit
                    p.doLayout();
                }
            });
        },
        
        initComponent: function() {
            this.items = this.initItemsConfig();
            Icinga.Cronks.System.PortalView.superclass.initComponent.call(this);
            this.initTools();
            this.on('render', this.initCronkDropZone, this, { single : true });
        },
        
        getState: function () {
            var d = [];
            this.items.each(function (col, cindex, l1) {
                var crlist = {};
                col.items.each(function (cr, crindex, l2) {
                    if (Cronk.Registry.get(cr.getId())) {
                        var c = Cronk.Registry.get(cr.getId());
                        c.height = cr.getHeight();
                        c.barsHidden = cr.barsHidden;
                        crlist[cr.getId()] = c;
                        
                        if (cr.items.getCount()) {
                            cr.items.each(function(item) {
                                var state = item.getState();
                                if (Ext.isObject(state)) {
                                    c.state = state;
                                }
                            }, this);
                        }
                    }
                }, this);
                d[cindex] = crlist;
            }, this);
            return {
                col: d,
                title: this.title
            }
        },
    
        applyState: function (state) {
            var p = this;
            
            // Prevent multiple state restores
            if(this.appliedState) {
                return true;
            } else {
                this.appliedState = true;
            }
               
            // Defered execution
            (function() {
                if (state.col) {
                    Ext.each(state.col, function (item, index, arry) {
                        Ext.iterate(item, function (key, citem, o) {
                            var c = citem;
                            c.tools = this.defaultTools;
                            c.id = Ext.id(); // create new id, otherwise it might get ugly
    
                            var cronk = Cronk.factory(c);
                           
                            this.initPortlet(cronk);   
                            this.get(index).add(cronk);
    
                            cronk.show();
    
                        }, this);
    
                    }, this);
    
                    this.doLayout();
                }
    
            }).defer(200, this);
    
        }
        
    });
})();