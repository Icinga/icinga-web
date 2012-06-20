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

/*global Ext: false, Icinga: false, _: false, AppKit: false */
Ext.ns("AppKit.Admin.Components");

(function () {

    "use strict";

    AppKit.Admin.Components.RoleInheritanceView = Ext.extend(Ext.tree.TreePanel, {
        roleProviderURI: "",
        region: 'south',
        split: true,
        height: 300,
        grid: null,
        store: null,
        layout: 'fit',
        autoScroll : true,
        
        constructor: function (cfg) {
            if (!cfg) {
                cfg = {};
            }
            cfg.iconCls = "icinga-icon-structure";

            cfg.root = new Ext.tree.TreeNode({
                hidden: false,
                editable: false,
                text: 'Groups',
                expanded: true,
                id: 'xroot-0'
            });
            
            AppKit.Admin.Components.RoleInheritanceView.superclass.constructor.call(this, cfg);
        },
        
        initComponent : function() {
            
            AppKit.Admin.Components.RoleInheritanceView.superclass.initComponent.call(this);
            
            this.store.on("load", function () {
                this.insertRoles();
            }, this);
            
            this.getSelectionModel().on("selectionchange", function (model, node) {
                if (!node) {
                    return true;
                }
                var record = [node.record];
                if (!Ext.isEmpty(this.grid)) {
                    this.grid.getSelectionModel().selectRecords(record);
                } else {
                    throw("<object>.grid configuration was not set!");
                }

            }, this);
        },
        
        tbar: new Ext.Toolbar({
            items: [{
                xtype: 'tbtext',
                text: _('Drag&Drop groups underneath other groups to let them inherit credentials/restrictions of the parent group')
            }]
        }),
        
        enableDD: true,
        inserted: {},
        title: _('Role inheritance'),
        
        insertRoles: function () {
            this.inserted = {};
            
            var noInsert = false;
            var selected_node = this.getSelectionModel().getSelectedNode();
            var selected = null;
            
            if (selected_node) {
                selected = selected_node.id;
            }
            
            this.getRootNode().removeAll();
            
            while (!noInsert) {
                noInsert = true;
                this.store.each(function (record) {

                    var name = record.get("name");
                    var id = record.get("id");
                    var parent = record.get("parent");
                    if (!this.inserted[id] && (!parent || (parent && this.inserted[parent]))) {
                        var node = new Ext.tree.TreeNode({
                            id: 'xrole-' + id,
                            text: name,
                            iconCls: 'icinga-icon-group'
                        });
                        this.inserted[id] = node;
                        node.record = record;
                        node.recordId = id;
                        noInsert = false;
                        if (!parent) {
                            this.getRootNode().appendChild(node);
                        } else {
                            this.inserted[parent].appendChild(node);
                        }
                    }
                }, this);
            }
            
            this.doLayout();
            
            if (selected) {
                selected_node = this.getNodeById(selected);
                this.expandPath(selected_node.getPath());
                this.getSelectionModel().select(selected_node);
            }
            
            return true;
        },
        listeners: {
            movenode: function (tree, node, oldParent, newParent, index) {
                if (!node.record) {
                    return false;
                }
                var parentId = newParent.record ? newParent.record.get("id") : -1;
                node.record.set("parent", parentId);
                var groupId = node.record.get("id");
                var params = {};
                Ext.apply(params, node.record.data);
                params.role_parent = params.parent;
                params.role_name = params.name;
                params.role_description = params.description;
                params.ignorePrincipals = true;
                Ext.Ajax.request({
                    url: tree.roleProviderURI + '/create',
                    params: params,
                    success: function () {
                        // tree.store.reload();
                    },
                    scope: this
                });

            },
            scope: this
        }
    });

})();