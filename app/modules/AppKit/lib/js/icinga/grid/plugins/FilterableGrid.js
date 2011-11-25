Ext.ns("Icinga.Grid.Plugins");
Icinga.Grid.Plugins.FilterableGrid = function(cfg) {
    this.filter = {};
    this.target = null;
        this.nodeTpl = new Ext.XTemplate('{filter_displayName} <b>{filter_operator}</b> {filter_value}'); 
    this.descriptor = null;
    this.constructor = function(cfg) {
        this.descriptor = cfg.filter;
    };
    
    this.init = function(grid) {
        this.target = grid;
        grid.showFilterWindow = this.showFilterWindow.createDelegate(this);
        this.extendGridStore();
        this.extendToolbar();
    };

    this.extendToolbar = function() {
        var grid = this.target;
        var tbar = grid.getTopToolbar();
        tbar.add({
            text: _('Filter'),
            iconCls: 'icinga-icon-pencil',
            menu: [{
                text: _('Modify'),
                iconCls: 'icinga-icon-pencil',
                handler: function(c) {
                    var grid = c.findParentByType('toolbar').ownerCt; 
                    grid.showFilterWindow();
                }
            },{
                text: _('Remove'),
                iconCls: 'icinga-icon-cancel',
                handler: function(c) {
                    var grid = c.findParentByType('toolbar').ownerCt; 
                    grid.getStore().setFilter(null);
                }
            }]
        });    
    };
    
    this.extendGridStore = function() {
        var grid = this.target;
        var store = grid.getStore();
        store.addEvents("filterChanged");
        store.setFilter =  (function(filter) {
            var store = this.target.getStore(); 
            
            var filterParam = this.descriptor.params.filter;
            store.filterDefinition = filter;
            
            store.setBaseParam(this.descriptor.params.filter,Ext.encode(filter));
           
          
            store.fireEvent("filterChanged");
        }).createDelegate(this);
        store.on("filterChanged", grid.getStore().reload,grid.getStore());
        
    };    
    
    this.showFilterWindow = function() {
        var body = Ext.getBody();
        var filters = this.target.getStore().filterDefinition;
        var tree = this.getFilterTree();
        if(filters)
            tree.setFilterFromObject(filters);
        var wnd = new Ext.Window({
            title: _('Modify Filter'),
            width: body.getWidth()*0.5,
            height: body.getHeight()*0.5,
            minWidth: 400,
            minHeight: 400,
            constrain:true,
            layout: 'border',
            items: [{
                region:'center', 
                xtype: 'panel',
                title: _('Current filter (Drop elements to modify)'),
                width:200,
                minWidth: 200,
                layout: 'fit',
                items: tree
            },{
                region: 'east',
                width: 200,
                minWidth: 200,
                split:true,
                xtype: 'panel',
                layout: 'fit',
                title: _('Available filter'),
                items: this.getFilterList()
            }],
            buttons: [{
                text: _('Save filter'),
                iconCls: 'icinga-icon-disk',
                handler: function(btn) {
                    var filter = tree.getTreeAsObject();
                    if(filter === false)
                        return false;
                    this.target.getStore().setFilter(filter);
                    btn.findParentByType("window").close();
                },
                scope:this
            }]
        });
        wnd.show(); 
    };
   
    this.getOperatorBox = function(data) {
        var operatorArray = [];
        for(var i in data.operators) {
            operatorArray.push([data.operators[i],i]);
        }
        
        var cmb = new Ext.form.ComboBox({
            typeAhead: true,
            forceSelection: true,
            width: 200,
            mode: 'local',
            name: 'filter_operator_show',
            hiddenName: 'filter_operator',
            triggerAction: 'all',
            store: new Ext.data.ArrayStore({
                data: operatorArray,
                id: 0, 
                fields: ['operator','display']
            }),
            
            fieldLabel: _('Operator'),
            allowBlank: false,
            valueField: 'operator',
            displayField: 'display'
        });
        return cmb; 
    };
 
    this.getValueField = function(data) {
        var store;
        if(data.values[0]) { 
            if(data.values[0].column)
                return new Icinga.Api.RESTFilterComboBox({
                    target: data.values[0].target,
                    width: 200,
                    field: data.values[0].column,
                    fieldLabel: _('Value'),
                    name: 'filter_value',
                    allowBlank: false
                });
           else {
                var vals = [];
                for(var x=0;x<data.values.length;x++) {
                    vals.push([x,data.values[x]]);
                }
                
                return  new Ext.form.ComboBox({
                    typeAhead: true,
                    forceSelection: true,
                    width: 200,
                    mode: 'local',
                    
                    hiddenName: 'filter_value',
                    triggerAction: 'all',
                    store: new Ext.data.ArrayStore({
                        data: vals,
                        id: 0, 
                        fields: ['value','display']
                    }),
                    
                    fieldLabel: _('Value'),
                    allowBlank: false,
                    valueField: 'value',
                    displayField: 'display'
                });
           }  
        } else {
            return new Ext.form.TextField({
                width: 200,
                fieldLabel: _('Value'),
                name: 'filter_value',
                allowBlank: false
            });
        }
    };   

    this.showFilterDialog = function(data,targetNode,tree,replace) {
        var h = Ext.getBody().getHeight();
        var w = Ext.getBody().getWidth();
        var wndId = Ext.id();
        
        // create comboboxes for filtering
        var filterItems = [];
        filterItems.push({
            xtype: 'component',
            html: '<h1>Filter: '+Ext.util.Format.htmlEncode(data.displayName)+'</h1><br/>'
        });
        filterItems.push(new Ext.form.Hidden({
            name: 'filter_target',
            value: data.name
        }));
        filterItems.push(new Ext.form.Hidden({
            name: 'filter_displayName',
            value: data.displayName
        }));
        filterItems.push(this.getOperatorBox(data));
        filterItems.push(this.getValueField(data));
        
        new Ext.Window({
            title: _('Add filter'),
            layout: 'fit',
            minHeight: 200,
            width: 400,
            height: 200, 
            modal:true,
           
            items : new Ext.form.FormPanel({
                layout: 'form',
                padding: 5,
                items: filterItems,
                id: wndId 
            }), 
            buttons: [{
                text: _('Save'),
                iconCls: 'icinga-icon-disk',
                handler: function(btn) {
                    var loader = tree.getLoader();
                    var form = Ext.getCmp(wndId); 
                    
                    var values = form.getForm().getValues();
                    if(!form.getForm().isValid())
                        return false;
                    
                    var nodeText = this.nodeTpl.apply(values); 
                    
                    var node = loader.createNode({
                        text: nodeText,
                        nodeType: 'node',
                        iconCls: 'icinga-icon-brick', 
                        isLeaf: true, 
                        values: values
                    });
                   
                    if(replace) {
                        targetNode.parentNode.appendChild(node);
                        targetNode.parentNode.removeChild(targetNode); 
                    } else {
                        targetNode.appendChild(node);
                    }
                    form.ownerCt.close(); 
                },
                scope: this
            }, {
                text: _('Cancel'),
                iconCls: 'icinga-icon-cancel',
                scope:this,
                handler: function(btn) {
                    btn.findParentByType('window').close();
                }
            }]
        }).show();
        if(replace) {
            Ext.getCmp(wndId).getForm().setValues(replace);
        }
    };
    
    this.getDescriptorForNode = function(node) {
        var filterList = this.descriptor.allowedFilter;
        
        for(var i = 0;i<filterList.length;i++) {
            if(filterList[i].type == "atom") {
                for(var x=0;x<filterList.filter.length;x++) {
                    var filter = filterList[i].filter[x];
                    
                    if(filter.name == node.attributes.values.filter_target)
                        return filter;
                }
            }
        } 
    };

    this.getFilterTree = function() {
        var defaultTreeRoot =  new Ext.tree.TreeNode({
            text:'AND', 
            value:'AND',
            filterType: 'group', 
            iconCls:'icinga-icon-bricks', 
            expanded:true
        });
        var outerScope = this;
        var treePanel = Ext.extend(Ext.tree.TreePanel,{
            setFilterFromObject: function(object) {
                var node = this.getRootNode();
                node.removeAll();
                for(var i=0;i<object.items.length;i++) {
                    this.addNodeFromObject(object.items[i],node);
                }
            },

            addNodeFromObject: function(node,parent) {
                var item = this.getNodeFromObject(node);
                if(item) {
                    parent.appendChild(item);
                    if(Ext.isArray(node.items)) {
                        Ext.iterate(node.items,function(subNode) {
                            this.addNodeFromObject(subNode,item);
                        },this);
                    }
                }
            },

            getNodeFromObject: function(node) {
                for(var i=0;i<outerScope.descriptor.allowedFilter.length;i++) {
                    var filter = outerScope.descriptor.allowedFilter[i];
                    
                    if(filter.type == "group"  && node.type) { // type only exists in groups
                         return this.loader.createNode({
                            text:   node['type'],
                            value:  node['type'],
                            iconCls: 'icinga-icon-bricks',
                            nodeType:'node',
                            filterType: 'group',
                            leaf: false
                        });
                
                    } else if(node.field && filter.type == "atom") {
                        
                        for(var x = 0;x<filter.filter.length;x++) {
                            var currentFilter = filter.filter[x];
                            
                            if(currentFilter.name == node.field) {
                                var txt = outerScope.nodeTpl.apply({
                                    filter_displayName: node.field,
                                    filter_operator: node.operator,
                                    filter_value: node.value
                                });
                                return this.loader.createNode({
                                    text: txt,
                                    iconCls: 'icinga-icon-brick',
                                    nodeType: 'node',
                                    leaf: 'true',
                                    values: {
                                        filter_target: node.field,
                                        filter_operator: node.operator,
                                        filter_value: node.value
                                    }
                                });
                            }
                        }
                    }
                }
            },

            getObjectFromFilter: function(node) {
                if(node.attributes.filterType == 'group') {
                    var subFilter = [];
                    if(!node.hasChildNodes()) {
                        Ext.Msg.alert(_("Invalid filter"),_("Empty filtergroups are not allowed!"));
                        return null; 
                    }
                    var error = false;
                    node.eachChild(function(childNode) {
                        var subFilterNode = this.getObjectFromFilter(childNode);
                        if(!subFilterNode) {
                            error = true;
                            return false;
                        }
                        subFilter.push(subFilterNode);
                    },this);
                    if(error)
                        return null;
                    return {
                        type: node.attributes.text,
                        items: subFilter
                    };
                } else {
                    
                    var values = node.attributes.values;
                    return {field: values.filter_target,operator: values.filter_operator, value: values.filter_value};
                }
            },
            getTreeAsObject: function() {
                var root = this.getRootNode();
                if(!root.hasChildNodes())
                    return null;
                var object =this.getObjectFromFilter(root);
                if(object == null)
                    return false;
                return object;
            }    
        });
        
        var tree = new treePanel({ 
            height:400,
            rootVisible:true,
            autoDestroy: true,
            enableDD: true,
            ddGroup:'filterEditor',
            autoScroll:true,
             
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
            root: defaultTreeRoot,
            loader:  new Ext.tree.TreeLoader({
            
            }),
           
            listeners: {
                contextmenu: function(node,event) {
                    event.preventDefault();
                        if(!node.parentNode)
                            return false;
                    var ctx = new Ext.menu.Menu({
                        items: [{
                            text: _('Edit this node'),
                            iconCls: 'icinga-icon-pencil',
                            handler: function(btn) {
                                outerScope.showFilterDialog(
                                    outerScope.getDescriptorForNode(node),
                                    node,
                                    this,
                                    node.attributes.values
                                );
                            },
                            scope: this
                        },{
                            text: _('Remove this node'),
                            iconCls: 'icinga-icon-delete',
                            handler: function(btn) {
                                node.parentNode.removeChild(node,true);
                            },
                            scope: this
                        }]
                    }).showAt(event.getXY());
                },

                beforeNodeDrop: function(event) {
                    event.cancel = false;
                    event.dropNode = [];
                
                    if(event.data.node) {
                        event.dropNode = [event.data.node];
                        return true;
                    }
                    Ext.each(event.data.selections,function(elem) {
                        var name = elem.get('name');
                        
                        // distinguish between groups and filters 
                        if(elem.get('type') == 'atom') {
                            outerScope.showFilterDialog(elem.data.additional,event.target,this);
                            event.cancel = true;
                        } else {  
                            event.dropNode.push(
                                this.loader.createNode({
                                    text: elem.get('displayName'),
                                    value: elem.get('name'),
                                    iconCls: (elem.get('type')  == 'group' ? 'icinga-icon-bricks' : 'icinga-icon-brick'),
                                    nodeType:'node',
                                    filterType: elem.get('type'),
                                    leaf: elem.get('type') != 'group'
                                })
                            ); 
                        }
                    },this);
                }
            }

        });
        
        return tree;        
    };

    this.getFilterListData = function() {
        var filterList = []; 
        for(var i=0;i<this.descriptor.allowedFilter.length;i++) {

            if(this.descriptor.allowedFilter[i].type == "atom") {
                for(var x=0;x<this.descriptor.allowedFilter[i].filter.length;x++) {
                    var filter = this.descriptor.allowedFilter[i].filter[x];
                    filterList.push([
                        filter.displayName,
                        'atom',
                        this.descriptor.allowedFilter[i].filter[x]]
                    );
                }
            } else if(this.descriptor.allowedFilter[i].type == "group") {

                for(var x in this.descriptor.allowedFilter[i].types) {
                    var group = this.descriptor.allowedFilter[i].types[x]; 
                    filterList.push([group,'group',x]);
                }
            }
        }
        return filterList;
    };

    this.getFilterList = function() { 
        return new Ext.grid.GridPanel({    
            enableDragDrop: true,
            autoDestroy: true,
            ddGroup:'filterEditor',
            store: new Ext.data.ArrayStore({
                fields: ['displayName','type','additional'],
                idIndex: 0,
                data: this.getFilterListData()
            }),
            colModel: new Ext.grid.ColumnModel({
                
                columns: [{
                    header:_(''),
                    dataIndex: 'type',
                    menuDisabled:true,
                    
                    renderer: function(value, metaData) {
                        metaData.css = (value == 'group' ? 'icinga-icon-bricks' : (value == 'atom') ? 'icinga-icon-brick' : 'icinga-icon-attach');
                        return '';
                    },
                    width:16
                    
                },{
                    header: _('Filter'),
                    dataIndex: 'displayName'
                }]
            
            })
        });

    };  
 
    this.constructor(cfg);
};


