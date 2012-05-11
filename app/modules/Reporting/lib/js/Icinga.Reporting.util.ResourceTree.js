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

Ext.ns('Icinga.Reporting.util');

Icinga.Reporting.util.ResourceTree = Ext.extend(Icinga.Reporting.abstract.ApplicationWindow, {
    
    layout : 'fit',
    minWidth: 200,
    maxWidth: 300,
    useArrows : true,
    autoScroll : false, 
    rootName : _('Repository'),
    title : _('Resources'),
    
    mask_text : _('Loading resource tree . . .'),
    
    constructor : function(config) {
        
        config = Ext.apply(config || {}, {
            'tbar' : [{
                text : _('Reload'),
                iconCls : 'icinga-icon-arrow-refresh',
                handler : this.reloadTree,
                scope : this
            }]
        });
        
        Icinga.Reporting.util.ResourceTree.superclass.constructor.call(this, config);
    },
    
    initComponent : function() {
        Icinga.Reporting.util.ResourceTree.superclass.initComponent.call(this);
        
        this.rootNode = new Ext.tree.AsyncTreeNode({
            text : this.rootName,
            iconCls : 'icinga-icon-bricks',
            id : 'root'
        });
        
        
        this.treeLoader = this.createTreeLoader();
        
        this.treeLoader.on('beforeload', function(loader, node, cb) {
            this.showMask();
        }, this, { single : true });
        
        this.treeLoader.on('load', function(loader, node, cb) {
            this.hideMask();
        }, this);
        
        this.treePanel = new Ext.tree.TreePanel({
            useArrows : true,
            autoScroll : true,
            animate : true,
            enableDD : false,
            containerScroll : true,
            border : false,
            loader: this.treeLoader,
            root: this.rootNode
        });
        
        this.treePanel.on('afterrender', function(c) {
            this.rootNode.expand();
        }, this, { single : true });
        
        this.add(this.treePanel);
    },
    
    getRootNode : function() {
        return this.rootNode;
    },
    
    getTreeLoader : function() {
        return this.treeLoader;
    },
    
    getTreePanel : function() {
        return this.treePanel;
    },
    
    createTreeLoader : function() {
        var tl = new Ext.tree.TreeLoader({
            dataUrl : this.treeloader_url,
            
            qtipTemplate : new Ext.XTemplate(
                '<strong>{name}</strong><br />'
                + '<span>Type: {type}</span><br />'
                + '<span>URI: {uri:ellipsis(60)}</span>', 
            {
                compiled : true
            }),
            
            createNode : function(attr) {
                attr.qtip = this.qtipTemplate.applyTemplate(attr);
                
                return Ext.tree.TreeLoader.prototype.createNode.call(this, attr);
            }
        });
        
        var filter = "";
        
        if (!Ext.isEmpty(this.treeloader_filter)) {
            filter = this.treeloader_filter;
        }
        
        tl.on('beforeload', function(treeLoader, node) {
            this.baseParams.filter = filter;
        });
        
        tl.on('load', function(treeLoader, node, response) {
            if (response.responseText.match(/error/)) {
                
                var msg = "";
                
                try {
                    var data = Ext.decode(response.responseText);
                    if (data.success == false && !Ext.isEmpty(data.error)) {
                        msg = data.error;
                    }
                } catch (e) {
                        msg = response.responseText;
                }
                
                Ext.Msg.show({
                    title : _('Reporting error'),
                    msg : String.format(_("Could not connect to the JasperServer (Raw: {0}).<br />Seems that reporting is unconfigured or not installed!"), msg),
                    buttons : Ext.Msg.OK,
                    icon : Ext.Msg.WARNING,
                    modal : true
                });
            }
        }); 
        
        return tl;
    },
    
    reloadTree : function() {
        this.showMask();
        this.rootNode.collapse(true);
        this.rootNode.removeAll(true);
        this.treeLoader.load(this.rootNode);
        this.rootNode.expand();
    }
});