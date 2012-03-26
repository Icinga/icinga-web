Ext.ns("LConf.DIT.Helper").KeyMap = function(DITTree) {
    var sm = DITTree.getSelectionModel();
    return new Ext.KeyMap(DITTree.getEl(),[{
        // copy
        key: "c",
        ctrl: true,
        fn: function() {
            DITTree.clipboardInsert(sm.getSelectedNodes(),DITTree.connId,false)
        },
        scope: this
    },{ // cut
        key: "x",
        ctrl: true,
        fn: function() {
            DITTree.clipboardInsert(sm.getSelectedNodes(),DITTree.connId,true)
        },
        scope: this
    },{ // insert
        key: "v",
        ctrl: true,
        fn: function() {
            var nodes = DITTree.getClipboardContent();
            // no nodes in the clipboard
            if(!nodes.clipboard)
                return false;
            nodes.clipboard.connId = nodes.tree;
            var selected = sm.getSelectedNodes();
            if(selected.length == 0)
                Ext.Msg.confirm(_("No node selected"), _("You haven't selected nodes to copy to"));

            // Check if this would be a recursive operation,
            // i.e. the node is at a branch of the current node
            for(var i=0;i<selected.length;i++) {
                var toNode = selected[i];
                for(var i=0;i<nodes.clipboard.length;i++)  {
                    if (toNode == nodes.clipboard[i] || toNode.isAncestor(nodes.clipboard[i])) {
                        Ext.Msg.alert(_("Invalid operation"), _("Moving or Copying a node below itself is not supported."));
                        return false;
                    }
                }
            }

            // Copy the node to the clipboard
            for(var i=0;i<selected.length;i++) {
                toNode.connId = DITTree.connId;
                DITTree.copyNode("append",nodes.clipboard,selected[i],nodes.cut);
            }
            if(nodes.cut) {
                DITTree.clearClipboard(true)
            }
            return true;
        },
        scope: this
    },{
        key: "n",
        ctrl: true,
        fn: function(key,ev) {
            
            var selected = sm.getSelectedNodes();
            if(selected.length == 0)
                Ext.Msg.confirm(_("No node selected"), _("You haven't selected a node"));
            var lastSelect = selected[selected.length-1];
            sm.select(lastSelect);

            DITTree.getContextMenu().show(lastSelect,{
                preventDefault: function() {},
                getXY: function() {
                    return [25,Ext.getBody().getHeight()/2-25]
                }
            },true);
            ev.preventDefault();
        },
        scope: this
    },{
        key: 46, //delete
        fn: function() {
            Ext.Msg.confirm(_("Remove selected nodes"),_("Do you really want to delete the selected entries?<br/>")+
                                              _("Subentries will be deleted, too!"),
                function(btn){
                    if(btn == 'yes') {
                        var toDelete = sm.getSelectedNodes();
                        DITTree.removeNodes(toDelete);
                    }
            },this);
        },
        scope: this
    }]);
}