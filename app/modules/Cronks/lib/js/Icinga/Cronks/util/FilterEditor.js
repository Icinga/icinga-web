// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2015 Icinga Developer Team.
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

/*jshint browser:true, curly:false */
/*global Ext: false, Icinga: false, AppKit: false, _: false, Cronk: false */
Ext.ns("Icinga.Cronks.util").FilterEditor = Ext.extend(Ext.tree.TreePanel, {
    height:400,
    rootVisible:true,
    autoDestroy: true,
    enableDD: true,
    ddGroup:'filterEditor',
    autoScroll:true,
    
    title: _('Drop Elements here'),
    columns: [{
        header:'Type',
        dataIndex: 'typefield',
        width:150,
        resizeable:true
    },{
        header:'Name',
        dataIndex: 'namefield',
        width:150,
        resizeable:true
    },{
        header:'Value',
        dataIndex: 'valuefield',
        width:200,
        resizeable:true
    }],
    labelFilterMap: {

    },
    presets: null,
    possibleFilters: [
        ['AND','group'],
        ['OR','group'],
        ['NOT','group']
    ],

    constructor: function(cfg) {
        if(typeof cfg !== "object")
            cfg = {};
        this.filterCfg = cfg.filterCfg;
        this.grid = cfg.grid;
        this.presets = cfg.presets;
        this.registerFilters();
        
        Ext.tree.TreePanel.prototype.constructor.apply(this,arguments);
        this.addEvents({
            "filterchanged": true

        });
    },

    registerFilters: function() {
        var known = {};
        this.possibleFilters = [
            ['AND','group'],
            ['OR','group'],
            ['NOT','group']
        ];
        for(var i=0;i<this.filterCfg.length;i++) {
            var filter = this.filterCfg[i];
            if (filter.enabled !== true) {
                continue;
            }
            if(known[filter.label])
                continue;
            this.possibleFilters.push([filter.label,'filter',filter]);
            this.labelFilterMap[filter.label] = filter;
            known[filter.label] = true;
        }
    },

    setLastState: function(state) {
        this.currentState = state;
    },

    getCurrentFilter: function() {
        return this.treeToFilterObject();
    },
    
    addReference: function(target,elem) {
        var node = this.loader.createNode({
            text: elem.get('name'),
            iconCls: 'icinga-icon-attach',
            nodeType:'node',
            referenceId : elem.get('type'),
            filterType: 'reference',
            leaf: elem.get('type') !== 'group'
        });
        node.filterAttributes = {
            referenceId : elem.get('type'),
            filterType: 'reference',
            filter_name: elem.get('name')
        };
        target.appendChild(node);
    },

    treeToFilterObject: function() {
        var root = this.getRootNode();
        if(!root.hasChildNodes()) {
            return null;
        }
        var filterObj = {
            "AND" : []
        };
        filterObj = this.nodeToFilterObject(root);

        return filterObj;
    },

    nodeToFilterObject : function(node) {
        var filter = {};
        if(node.filterType === 'group' || node.attributes.filterType === 'group') {
            filter[node.text] = [];
            node.eachChild(function(childNode) {
                filter[node.text].push(this.nodeToFilterObject(childNode));

            },this);
        } else if(node.filterType === 'filter' || node.attributes.filterType === 'filter') {
            delete(node.filterAttributes.filter_parent);
            filter = node.filterAttributes;
        } else if(node.filterType === 'reference' || node.attributes.filterType === 'reference') {

            delete(node.filterAttributes.filter_parent);
            filter = node.filterAttributes;
            filter = {
                "REFERENCE" : filter
            };
        }
        return filter;
    },

    treeFromFilterObject : function(presets) {

        var root = this.nodeFromFilterObject(presets);
        return root;
    },

    nodeFromFilterObject : function(presets) {
        var node;
        if (Ext.isString(presets)) {
            presets = Ext.decode(presets);
        }
        if(Ext.isObject(presets)) {
            var presetFn = function(preset) {
                node.appendChild(this.nodeFromFilterObject(preset));
            };
            if(presets.AND || presets.OR || presets.NOT) {
                for(var i in presets) {
                    node = new Ext.tree.TreeNode({
                        text: i,
                        expanded:true,
                        iconCls: 'icinga-icon-conjunction',
                        filterType: 'group'
                    });
                    Ext.each(presets[i], presetFn,this);
                }
            } else if(presets.REFERENCE) {
                node = new Ext.tree.TreeNode({
                    text:presets.REFERENCE.filter_name,
                    leaf:true,
                    iconCls: 'icinga-icon-attach'
                });

                node.filterAttributes = presets;
                node.referenceId = presets.REFERENCE.referencedId;
                node.filterType = 'reference';
            } else {
                node = new  Ext.tree.TreeNode({
                    text:this.buildTextFromFilter(presets),
                    leaf:true,
                    iconCls: 'icinga-icon-brick'
                });
                node.filterAttributes = presets;
                node.filterType = 'filter';
            }
        }
        return node;
    },


    getAvailableFiltersArray: function(record) {
        return this.possibleFilters;
    },

    hasCyclicRedundancies: function(record1,record2) {
        var id1 = record1.get("filter_id");

        var json2 = Ext.decode(record2.get('filter_json'));

        if(this.searchReferenceInFilterObject(id1,json2))
            return true;


        return false;
    },

    searchReferenceInFilterObject : function(id,filterObj) {
        var found = false;
        var searchFn = function(elem) {
            found = found || this.searchReferenceInFilterObject(id,elem);
            // get out of the loop if found anything
            if(found)
               return false;
            return true;
        };
        if(filterObj.AND || filterObj.OR || filterObj.NOT) {
            for(var i in filterObj) {
                Ext.each(filterObj[i],searchFn,this);
            }
        } else if(filterObj.REFERENCE) {
            if(filterObj.REFERENCE.referenceId === id)
                found = true;
        }
        return found;
    },

    getAvailableElementsList: function(record) {
        return new Ext.grid.GridPanel({
            height:400,
            enableDragDrop: true,
            autoDestroy: true,
            viewConfig: {
                forceFit:true
            },
            ddGroup:'filterEditor',
            store: new Ext.data.ArrayStore({
                fields: ['name','type','cfg'],
                idIndex: 0,
                data: this.getAvailableFiltersArray(record)
            }),
            colModel: new Ext.grid.ColumnModel({
                columns: [{
                    header:_(''),
                    dataIndex: 'type',
                    menuDisabled:true,

                    renderer: function(value, metaData) {
                        metaData.css = (value === 'group' ? 'icinga-icon-conjunction' : (value === 'filter') ? 'icinga-icon-brick' : 'icinga-icon-attach');
                        value = '';
                        return '';
                    },
                    width:16

                },{
                    header: _('Type'),
                    dataIndex: 'name'
                }]
            })
        });
    },

    getDefaultRootNode: function() {
        return new Ext.tree.TreeNode({
            text:'AND',
            filterType: 'group',
            iconCls:'icinga-icon-conjunction',
            id:'root',
            expanded:true
        });
    },
    initEvents: function() {
        Ext.tree.TreePanel.prototype.initEvents.apply(this,arguments);
        this.on({
            afterlayout: function() {
                this.registerFilters();
                if(this.currentState)
                    this.setRootNode(this.nodeFromFilterObject(this.currentState));

            },
            click: function(node,event) {
                if(node.filterType != "filter")
                    return;
                event.preventDefault();
                if(!node.parentNode)
                    return false;
                this.addFilterTo(node,this.labelFilterMap[node.filterAttributes.label || node.id],true);
               
            },
            contextmenu: function(node,event) {
                event.preventDefault();
                if(!node.parentNode)
                    return false;
                new Ext.menu.Menu({
                    items: [{
                        text: _('Edit this filter'),
                        iconCls: 'icinga-icon-application-edit',
                        hidden: node.filterType != "filter",
                        handler: function() {

                            this.addFilterTo(node,this.labelFilterMap[node.filterAttributes.label || node.id],true);
                        },
                        scope: this
                    },{
                        text: _('Remove this filter'),
                        iconCls: 'icinga-icon-delete',
                        handler: function() {
                            node.parentNode.removeChild(node,true);
                            this.fireEvent('filterchanged',this.getCurrentFilter());
                        },
                        scope: this
                    }]
                }).showAt(event.getXY());
                return true;
            },
            beforeNodeDrop: function(event) {
                event.cancel = false;
                event.dropNode = [];

                if(event.data.node) {
                    event.dropNode = [event.data.node];
                    return true;
                }
                Ext.each(event.data.selections,function(elem) {
                    if(elem.get('type') === 'filter') {
                        // The filter needs some tweaking before it can be added
                        this.addFilterTo(event.target,elem.data.cfg);
                        event.cancel = true;
                    } else if(elem.get('type') !== 'group') {
                        this.addReference(event.target,elem.data.cfg);
                        event.cancel = true;
                    } else {
                        // Groups and predefined filters can be directly added
                        event.dropNode.push(this.loader.createNode({
                            text: elem.get('name'),
                            iconCls: (elem.get('type') === 'group' ? 'icinga-icon-conjunction' : 'icinga-icon-brick'),
                            nodeType:'node',
                            filterType: elem.get('type'),
                            leaf: elem.get('type') !== 'group'
                        }));
                    }
                },this);
                return true;
            }
        });
    },

    initComponent: function() {
        this.loader =  new Ext.tree.TreeLoader({});
        this.root   = this.presets ? this.treeFromFilterObject(this.presets) : this.getDefaultRootNode();
        Ext.tree.TreePanel.prototype.initComponent.apply(this,arguments);
    },
    /**
     * Creates a popup at the mouse location
     * 
     */
    addFilterTo: function(targetNode,node,replace) {
        var defaults = targetNode.filterAttributes || {};
        var _f = this.FILTERTYPES; // filter shorthand
        var form = this.getFormForFilter(node,defaults);
        form.filterCfg = node;
        form.bbar = ['->',{
            text: replace ? _('Edit filter') : _('Add filter'),
            iconCls: replace ? 'icinga-icon-application-edit' : 'icinga-icon-add',
            handler: this.addFilterHandler.createDelegate(this,[targetNode,replace]),
            scope:this
        },{
            text: _('Close'),
            iconCls: 'icinga-icon-cancel',
            handler: function(cmp) {
                cmp.ownerCt.ownerCt.ownerCt.close();
            }
        }];
        this.form = new Ext.form.FormPanel(form);
        if(this.addctx) {
            this.addctx.close();
            this.addctx.destroy();
        }
        this.addctx = new Ext.Window({
            width: 400,
            renderTo:Ext.getBody(),
            resizable:false,
            draggable:false,
            closable:false,
            unstyled:true,
            shadow:false,
            autoHeight:true,
            layout:'fit',
            items: this.form
        });
        this.addctx.setPosition(Ext.EventObject.getXY());
        
        this.addctx.show();

        return true;
    },
  
    getFormForFilter: function(filterCfg,defaults) {
        var filter_class = Ext.util.Format.capitalize(filterCfg.subtype.replace('appkit.ext.filter.',''));
        if (typeof Icinga.Cronks.util.FilterTypes[filter_class] !== "function") {
            Ext.Msg.alert(_("Filter config error"),_("The grid has an invalid/unknown filter definition for this field."));
            return {};
        }
        return Icinga.Cronks.util.FilterTypes[filter_class](filterCfg,defaults);
    },
    
    buildTextFromFilter: function(values) {
        var txtVal = values.value;
        if(typeof values.value === "boolean")
            txtVal = values.value ? "set" : "not set";
        return "<b>"+values.label+"</b> "+values.operator+" "+ txtVal ;
    },
    
    addFilterHandler: function(targetNode,replace) {
        if(!this.form.getForm().isValid())
            return false;

        if(targetNode.isLeaf() && !replace)
            targetNode = targetNode.parentNode;

        var	values = this.form.getForm().getFieldValues();
        var txt = this.buildTextFromFilter(values);
        values.filter_parent = targetNode;
        var node = this.loader.createNode({
            text: txt,
            iconCls: 'icinga-icon-brick',
            nodeType:'node',
            leaf: true
        });
        
        node.filterCfg = this.form.filterCfg;
        node.filterType = 'filter';
        node.filterAttributes = values;
        if(replace) {
            targetNode.parentNode.appendChild(node);
            targetNode.parentNode.removeChild(targetNode);
        } else
            targetNode.appendChild(node);

        this.addctx.close();
        this.fireEvent('filterchanged',this.getCurrentFilter());
        return true;
    }
});
