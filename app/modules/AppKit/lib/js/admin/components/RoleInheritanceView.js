/*global Ext: false, Icinga: false, _: false, AppKit: false */
Ext.ns("AppKit.Admin.Components");

(function () {

    "use strict";

    AppKit.Admin.Components.RoleInheritanceView = Ext.extend(Ext.tree.TreePanel, {
        roleProviderURI: "",
        region: 'south',
        height: 300,
        grid: null,
        store: null,
        layout: 'fit',
        constructor: function (cfg) {
            if (!cfg) {
                cfg = {};
            }
            cfg.iconCls = "icinga-icon-structure";

            cfg.root = new Ext.tree.TreeNode({
                hidden: false,
                editable: false,
                text: 'Groups',
                expanded: true
            });
            Ext.apply(this, cfg);
            Ext.tree.TreePanel.superclass.constructor.call(this, cfg);
            this.getSelectionModel().on("selectionchange", function (model, node) {
                if (!node) {
                    return true;
                }
                var record = [node.record];
                cfg.grid.getSelectionModel().selectRecords(record);

            }, this);
            this.store.on("load", function () {
                this.insertRoles();
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
            this.getRootNode().removeAll();
            var noInsert = false;
            while (!noInsert) {
                noInsert = true;
                this.store.each(function (record) {

                    var name = record.get("name");
                    var id = record.get("id");
                    var parent = record.get("parent");
                    if (!this.inserted[id] && (!parent || (parent && this.inserted[parent]))) {
                        var node = new Ext.tree.TreeNode({
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
                        tree.store.reload();
                    },
                    scope: this
                });

            },
            scope: this
        }
    });

})();