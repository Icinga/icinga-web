/**
 * Helper class for the DIT Tree's clipboard data
 *
 * It's kind of a global state container class for all DIT Trees
 */
Ext.ns("LConf.DIT.Mixin").Clipboard = function() {
    /**
     * This is intended to be public static, because the clipboard is a kind of
     * global state for all DIT Trees. 
     * 
     * When a clipboard action is performed, there's always the information given,
     * which tree was used, so cross tree modifications are possible.
     */
    var clipboard = [];

    /**
     * Adds a set of nodes to the clipboard, overwriting previous values
     *
     * @param Array             The nodes to add, should be an array of Ext.tree.TreeNode classes
     * @tree Lconf.DIT.DITTree  The DITTree instance the node-set belong to
     * @cutted boolean          Whether it's a destructive copy operation or not
     */
    this.clipboardInsert = function(nodes,tree,cutted) {
        this.clearClipboard();
        clipboard = [nodes,tree,cutted];
        if(cutted) {
            Ext.each(nodes,function(node) {
                node.setCls("italic");
            });

        }
    }
    /**
     * Resets the clipboard state to empty
     */
    this.clearClipboard = function(cutted) {
        if (!cutted) {
            try {
                Ext.each(clipboard[0], function(node){
                    node.setCls("");
                });
            } catch(e) {}
        }
        clipboard = [];
    }

    /**
     * Returns an object containing the current clipboard state or an empty
     * object if no nodes are currently in the clipboard
     *
     * @return Object an object with the parameters:
     *      clipboard: A collection of Ext.tree.TreeNodes which are currently in the clipboard
     *      tree     : The LConf.DIT.DITTree instance these nodes belong to
     *      cut      : Whether these nodes are marked to be removed when copied (cut operation)
     */
    this.getClipboardContent = function() {
        if (!Ext.isEmpty(clipboard)) {
            return {
                clipboard: clipboard[0],
                tree: clipboard[1],
                cut: clipboard[2] || false
            }
        }
        return {};
    }
}