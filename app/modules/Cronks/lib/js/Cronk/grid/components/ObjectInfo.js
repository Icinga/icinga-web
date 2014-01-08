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

Ext.ns('Cronk.grid.components');

(function() {
    
    "use strict";
    
    /**
     * @static
     * Object to borrow the "tackle" detail view functionality and
     * use them in a render of the grids
     * 
     */
    Cronk.grid.components.ObjectInfo = new (Ext.extend(Ext.Window, {
        width: '80%',
        height: 400,
        title: _('Object information'),
        defaultTitle: _('Object information'),
        prefixTitle: _('Object information for {0}'),
        closeAction: 'hide',
        layout: 'fit',
        modal: true,
        
        /**
         * @private
         */
        constructor : function() {
            this.addEvents({
                "showobjectinfo": true
            });
            
            Ext.Window.prototype.constructor.call(this, {});
        },
        
        /**
         * @private
         */
        initComponent : function() {
            this.bbar = ['->', {
                text: _('Close'),
                iconCls: 'icinga-action-icon-cancel',
                handler: (function(b, e) {
                    this.hide();
                }).createDelegate(this)
            }];
            
            Ext.Window.prototype.initComponent.call(this);
            
            this.tabs = new Ext.TabPanel();
            
            this.tabItems = {
                host: {},
                service: {}
            };
            
            Ext.iterate(this.tabItems, function(k, v) {
                v.information = new Icinga.Cronks.Tackle.Information.Head({
                    type: k,
                    connection: this.connection
                });
                
                // We do not want to call this explicit
                v.information.getStore().on('beforeload', function() {
                    this.show();
                    this.getEl().mask(_('Loading . . .'));
                }, this);
                
                v.information.getStore().on('load', function() {
                    this.getEl().unmask();
                }, this);
                
                v.relation = new Icinga.Cronks.Tackle.Relation.Head({
                    type: k
                });
                v.externalRefs = new Icinga.Cronks.Tackle.ExternalReferences.Head({
                    type: k,
                    connection: this.connection
                });
                Ext.iterate(v, function(k2, v2) {
                    this.tabs.add(v2);
                }, this);
            }, this);
            
            this.add(this.tabs);
            
            this.doLayout();
            
            this.on('showobjectinfo', this.onShowObjectInfo, this);
            
            this.on('beforeshow', function(me) {
                if (me.type) {
                    me.prepareView(me.type);
                }
            });
        },
        
        /**
         * Prepare the view, show or hide sub components based on type
         * @param {String} type host or service
         */
        prepareView : function(type) {
            var hide = (type==="host") ? "service" : "host";
            var show = (hide==="host") ? "service" : "host";
            
            Ext.iterate(this.tabItems[hide], function(k, object) {
                this.tabs.hideTabStripItem(object);
            }, this);
            
            Ext.iterate(this.tabItems[show], function(k, object) {
                this.tabs.unhideTabStripItem(object);
            }, this);
        },
        
        /**
         * @private
         * Target of the event
         */
        onShowObjectInfo : function(type, oid,connection) {
            this.type = type;
            
            Ext.iterate(this.tabItems[type], function(k, object) {
                if (!Ext.isEmpty(object.loadDataForObjectId)) {
                  object.loadDataForObjectId(oid,connection);
                } else {
                    throw("WHOO, loadDataForObjectId is not implemented!");
                }
            }, this);
            
            this.tabs.setActiveTab(this.tabItems[type].information);
            
        },

        /**
         * Sets suffix to title
         *
         * @param {String} suffix
         */
        setTitleSuffix: function(suffix) {
            if (!suffix) {
                this.setTitle(this.defaultTitle);
            } else {
                this.setTitle(String.format(this.prefixTitle, suffix));
            }
        },
        
        /**
         * Interface method to show the window (Event)
         */
        showObjectInfo : function(type, oid, connection, titleSuffix) {
            this.fireEvent('showobjectinfo', type, oid, connection);
            this.setTitleSuffix(titleSuffix);
        }
    }))();
})();