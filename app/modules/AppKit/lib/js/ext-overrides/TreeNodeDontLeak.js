(function () {
    'use strict';
    var removeChild = Ext.tree.TreeNode.prototype.removeChild;
    Ext.override(Ext.tree.TreeNode, {
        removeChild: function () {
            var node = removeChild.apply(this, arguments);
            this.ownerTree.unregisterNode(node);
            return node;
        }
    });
}());
