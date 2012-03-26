/* 
 * Class that contains the implementation of node modifications
 * used by the DIT Tree
 *
 */

Ext.ns("LConf.DIT.Mixin").NodeModifier = function() {

    this.processDNForServer = function(dn) {
        dn = dn.replace("ALIAS=Alias of:","");
        dn = dn.replace(/^\*\d{4}\*/,"");
        return dn;
    },

    this.removeNodes = function(nodes) {

        var dn = [];
        if(!Ext.isArray(nodes))
            nodes = [nodes];
        Ext.each(nodes,function(node) {
            var id = (node.attributes["aliasdn"] || node.id)
            dn.push(this.processDNForServer(id));
        },this);
        var updateNodes = this.getHighestAncestors(nodes);
        Ext.Ajax.request({
            url: this.urls.modifynode,
            params: {
                properties: Ext.encode(dn),
                xaction:'destroy',
                connectionId: this.connId
            },
            success: function(resp) {
              
                Ext.each(updateNodes,function(node) {
                    if(node)
                        this.refreshNode(node);
                },this)
            },
            failure: function(resp) {
                
                var err = (resp.responseText.length<50) ? resp.responseText : 'Internal Exception, please check your logs';
                Ext.Msg.alert(_("Error"),_("Couldn't remove Node:<br/>"+err));
            },
            scope: this
        });

    },
    
    this.refreshNode = function(node,preserveStructure,callback) {
        var tree = node.getOwnerTree();
        var selected = tree.getSelectionModel().getSelectedNodes();
        var	expandTree;
        if(Ext.isArray(selected))
            selected = selected[0];

        if(tree.reloadFilters) {
            tree.getLoader().baseParams["filters"] = tree.reloadFilters;
            preserveStructure = false;
        }
        tree.reloadFilters = false;

        if(!node)
            node = this.getRootNode();
        if(preserveStructure) {
            expandTree = tree.getExpandedSubnodes(node);
        }
        /**
         * TODO: Does this work? getAliasedNode doesn't occur anywhere else
         **/
        if(node.attributes.isAlias) {
            var aliased = this.getAliasedNode(node);
            if(aliased) {
                aliased.reload();
                tree.getExpandedSubnodes(node);
            }
        }


        if(preserveStructure) {
            this.on("load", function(elem) {
                tree.expandViaTreeObject(expandTree,callback,selected);
            },this,{single:true});
        }
        node.reload();
    }

    /**
     * Reduces a set of nodes to the highest ancestors of them.
     * A set [A,B,C,D,E,F] with the followinf structure
     *
     * X1--A-F
     *
     * X2-|     |-D-E
     *    |-B---|
     *    |     |-C
     *
     * Would return [X1,X2] (The parents of A/B)
     * If the root node is reached, this node will be returned
     *
     * Is used to determine which par
     *
     * @param {Array} An array of Ext.tree.TreeNode
     * @return {Array}
     */
    this.getHighestAncestors = function(nodeSet) {
        var returnSet = [];
        for(var i=0;i < nodeSet.length;i++) {
            var node = nodeSet[i];
            var hasAncestor = false;
            for(var x=0;x < nodeSet.length;x++) {
                var checkNode = nodeSet[x];
                if(checkNode == node)
                    continue;
                if(node.isAncestor(checkNode)) {
                    hasAncestor = true;
                    break;
                }
            }
            if(!hasAncestor) {
                if(node.parentNode)
                    returnSet.push(node.parentNode);
                else // it's the root node
                    returnSet.push(node);
            }
        }
        return Ext.unique(returnSet);
    }

    this.buildAlias = function(pos,fromArr,to) {
        Ext.each(fromArr,function(from) {
            var toDN = to.id;
            if(pos != 'append')
                toDN = to.parentNode.id;

            if(from.parentNode.id == toDN) {
                Ext.Msg.alert(_("Error"),_("Target and source are the same"))
                return false;
            }

            var properties = [{
                "property" : "objectclass",
                "value" : "extensibleObject"
            },{
                "property" : "objectclass",
                "value" : "alias"
            },{
                "property" : "aliasedObjectName",
                "value" : from.id
            },{
                "property" : "ou",
                "value" : from.id.split(",")[0].split("=")[1]
            }]
            Ext.Ajax.request({
                url: this.urls.modifynode,
                params: {
                    connectionId: this.connId,
                    xaction: 'create',
                    parentNode: toDN,
                    properties: Ext.encode(properties)
                },
                failure:function(resp) {
                    var err = (resp.responseText.length<1024) ? resp.responseText : 'Internal Exception, please check your logs';
                    Ext.Msg.alert(_("Error"),_("Couldn't create alias node:<br/>"+err));
                },
                success: function() {
                    if(to.getOwnerTree())
                        this.refreshNode(to.parentNode,true);
                },
                scope:this
            });
            return true;
        },this);
    }

    this.copyNode = function(pos,fromArr,to,move) {
        Ext.each(fromArr,function(from) {
            var toDN = to.id;
            if(pos != 'append')
                toDN = to.parentNode.id;

            if(move && from.parentNode.id == toDN) {
                Ext.Msg.alert(_("Error"),_("Target and source are the same"))
                return false;
            }

            var copyParams = {
                targetDN: this.processDNForServer(toDN),
                targetConnId: this.connId,
                sourceDN: this.processDNForServer(from.id)
            }
            LConf.Helper.Debug.d("Copying nodes",this,arguments);
            Ext.Ajax.request({
                url: this.urls.modifynode,
                params: {
                    connectionId: fromArr.connId,
                    xaction: move ? 'move' :'clone' ,
                    properties: Ext.encode(copyParams)
                },
                failure:function(resp) {
                    var err = (resp.responseText.length<1024) ? resp.responseText : 'Internal Exception, please check your logs';
                    Ext.Msg.alert(_("Error"),_("Couldn't copy node:<br\>"+err));
                },
                success: function() {
                    if(to.getOwnerTree())
                        this.refreshNode(to.parentNode,true);
                    if(from.getOwnerTree())
                        this.refreshNode(from.parentNode,true);
                },
                scope:this
            });
            return true;
        },this)
    }

    this.callExpandAlias = function(nodeCfg) {
        if(!nodeCfg.attributes.isAlias) {
            Ext.Msg.alert(_("Invalid operation"),_("Only aliases can be expanded"));
        }
        var dn = nodeCfg.attributes.dn;
        Ext.Ajax.request({
            url: this.urls.modifynode,
            params: {
                properties: dn,
                xaction:'expandAlias',
                connectionId: this.connId
            },
            success: function(resp) {
                this.refreshNode(nodeCfg.parentNode);

            },
            failure: function(resp) {
                var err = (resp.responseText.length<50) ? resp.responseText : 'Internal Exception, please check your logs';
                Ext.Msg.alert(_("Error"),_("Couldn't expand Alias:<br/>"+err));
            },
            scope: this
        });
    }


    this.callExpandAlias = function(nodeCfg) {
        if(!nodeCfg.attributes.isAlias) {
            Ext.Msg.alert(_("Invalid operation"),_("Only aliases can be expanded"));
        }
        var dn = nodeCfg.attributes.dn;
        Ext.Ajax.request({
            url: this.urls.modifynode,
            params: {
                properties: dn,
                xaction:'expandAlias',
                connectionId: this.connId
            },
            success: function(resp) {
                this.refreshNode(nodeCfg.parentNode);

            },
            failure: function(resp) {
                var err = (resp.responseText.length<50) ? resp.responseText : 'Internal Exception, please check your logs';
                Ext.Msg.alert(_("Error"),_("Couldn't expand Alias:<br\>"+err));
            },
            scope: this
        });
    }
}


