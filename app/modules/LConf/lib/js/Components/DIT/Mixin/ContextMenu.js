
Ext.ns("LConf.DIT.Mixin").ContextMenu = function() {

    this.showGeneralNodeDialog = function(node,e,justCreate) {
        e.preventDefault();
        var tree = node.getOwnerTree();
        var ctx = new Ext.menu.Menu({
            items: [{
                text: _('Refresh this part of the tree'),
                iconCls: 'icinga-icon-arrow-refresh',
                handler: tree.refreshNode.createDelegate(tree,[node,true]),
                scope: this,
                hidden: node.isLeaf() || justCreate
            },{
                text: _('Create new node on same level'),
                iconCls: 'icinga-icon-add',
                handler: tree.wizardManager.callNodeCreationWizard.createDelegate(tree.wizardManager,[{node:node}]),
                scope: this,
                hidden: !(node.parentNode)
            },{
                text: _('Create new node as child'),
                iconCls: 'icinga-icon-sitemap',
                handler: tree.wizardManager.callNodeCreationWizard.createDelegate(tree.wizardManager,[{node:node,isChild:true}]),
                scope: this
            },{
                text: _('Remove <b>only this</b> node'),
                iconCls: 'icinga-icon-delete',
                handler: function() {
                    Ext.Msg.confirm(_("Remove selected nodes"),
                        _("Do you really want to delete this entry?<br/>")+
                        _("Subentries will be deleted, too!"),
                        function(btn){
                            if(btn == 'yes') {
                                tree.removeNodes([node]);
                            }
                        },this);
                },
                hidden: justCreate,
                scope: this
            },{
                text: _('Remove <b>all selected</b> nodes'),
                iconCls: 'icinga-icon-cross',
                hidden:!(tree.getSelectionModel().getSelectedNodes().length),
                handler: function() {
                    Ext.Msg.confirm(_("Remove selected nodes"),
                        _("Do you really want to delete the selected entries?<br/>")+
                        _("Subentries will be deleted, too!"),
                        function(btn){
                            if(btn == 'yes') {
                                var toDelete = tree.getSelectionModel().getSelectedNodes();
                                tree.removeNodes(toDelete);
                            }
                        },this);
                },
                hidden: justCreate,
                scope: this
            },{
                text: _('Jump to alias target'),
                iconCls: 'icinga-icon-arrow-redo',
                hidden: justCreate || !node.attributes.isAlias && !node.id.match(/\*\d{4}\*/),
                handler: tree.jumpToRealNode.createDelegate(tree,[node])
            },{
                text: _('Resolve alias to nodes'),
                iconCls: 'icinga-icon-arrow-application-expand',
                hidden: justCreate || !node.attributes.isAlias && !node.id.match(/\*\d{4}\*/),
                handler: tree.callExpandAlias.createDelegate(tree,[node])
            },{
                text: _('Display aliases to this node'),
                iconCls: 'icinga-icon-wand',
                hidden: node.attributes.isAlias || node.id.match(/\*\d{4}\*/),
                handler: function(btn) {
                    tree.eventDispatcher.fireCustomEvent("aliasMode",node);
                },
                scope:this,
                hidden: justCreate
            },{
                text: _('Search/Replace'),
                iconCls: 'icinga-icon-zoom',
                handler: tree.searchReplaceManager.execute,
                hidden: (node.parentNode),
                scope: tree.searchReplaceManager
            }]
        });
        ctx.showAt(e.getXY())
    };

    this.showNodeDroppedDialog = function(e) {
        var containsAlias = false;
        var tree = null;
        Ext.each(e.dropNode,function(node) {
            tree = node.getOwnerTree();
            if(node.attributes.isAlias)
                containsAlias = true;
            return !containsAlias;
        });
        if(!tree)
            return false;
        
        var tabPanel = tree.ownerCt;
        var ctx = new Ext.menu.Menu({
            items: [{
                text: _('Clone node here'),
                handler: tree.copyNode.createDelegate(tabPanel.getActiveTab(),[e.point,e.dropNode,e.target]),
                scope:this,
                iconCls: 'icinga-icon-arrow-divide'
            },{
                text: _('Move node here'),
                handler: tree.copyNode.createDelegate(tabPanel.getActiveTab(),[e.point,e.dropNode,e.target,true]),
                scope:this,
                iconCls: 'icinga-icon-arrow-turn-left'
            },{
                text: _('Clone node <b>as subnode</b>'),
                handler: tree.copyNode.createDelegate(tabPanel.getActiveTab(),["append",e.dropNode,e.target]),
                scope:this,
                hidden: !e.target.isLeaf(),
                iconCls: 'icinga-icon-arrow-divide'
            },{
                text: _('Move node  <b>as subnode</b>'),
                handler: tree.copyNode.createDelegate(tabPanel.getActiveTab(),["append",e.dropNode,e.target,true]),
                scope:this,
                hidden: !e.target.isLeaf(),
                iconCls: 'icinga-icon-arrow-turn-left'
            },{
                text: _('Create alias here'),
                iconCls: 'icinga-icon-attach',
                hidden: containsAlias || e.dropNode.connId != e.target.ownerTree.connId,
                handler: tree.buildAlias.createDelegate(this,[e.point,e.dropNode,e.target])
            },{
                text: _('Cancel'),
                iconCls: 'icinga-icon-cancel'
            }]
        });
        ctx.showAt(e.rawEvent.getXY());
        return true;
    }

}