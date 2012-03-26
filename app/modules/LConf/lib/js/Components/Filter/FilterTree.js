Ext.ns("LConf.Filter").FilterTree = Ext.extend(Ext.tree.TreePanel, {
    height:400,
    rootVisible:true,
    autoDestroy: true,
    enableDD: true,
    ddGroup:'filterEditor',
    autoScroll:true,
    filterState: null,
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
    presets: null,
    FILTERTYPES : {
		1 : _('matches'),
		2 : _('starts with'),
		3 : _('ends with'),
		4 : _('contains')
	},

    constructor: function(cfg) {
        if(typeof cfg !== "object")
            cfg = {};
        this.presets = cfg.presets;
        this.filterState = cfg.filterState;
        Ext.tree.TreePanel.prototype.constructor.apply(this,arguments);
    },

    addReference: function(target,elem) {
        var node = this.loader.createNode({
            text: elem.get('name'),
            iconCls: 'icinga-icon-attach',
            nodeType:'node',
            referenceId : elem.get('type'),
            filterType: 'reference',
            leaf: elem.get('type') != 'group'
        });
        node.filterAttributes = {
            referenceId : elem.get('type'),
            filterType: 'reference',
            filter_name: elem.get('name')
        }
        target.appendChild(node);
    },

    treeToFilterObject: function() {
        var root = this.getRootNode();
        if(!root.hasChildNodes()) {
            Ext.Msg.alert(_("No filters defined"),_("You haven't defined any filters!"));
            return false;
        }
        var filterObj = {"AND" : []}
        filterObj = this.nodeToFilterObject(root);

        return filterObj;
    },

    nodeToFilterObject : function(node) {
        var filter = {};
        if(node.filterType == 'group' || node.attributes.filterType == 'group') {
            filter[node.text] = [];
            node.eachChild(function(childNode) {
                 filter[node.text].push(this.nodeToFilterObject(childNode));
            },this);
        } else if(node.filterType == 'filter' || node.attributes.filterType == 'filter') {
            delete(node.filterAttributes.filter_parent);
            filter = node.filterAttributes;
        } else if(node.filterType == 'reference' || node.attributes.filterType == 'reference') {

            delete(node.filterAttributes.filter_parent);
            filter = node.filterAttributes;
            filter = {"REFERENCE" : filter};
        }
        return filter;
    },

    treeFromFilterObject : function(presets) {

        var root = this.nodeFromFilterObject(presets);
        return root;
    },

    nodeFromFilterObject : function(presets) {
        var node;
        if(Ext.isObject(presets)) {
            if(presets["AND"] || presets["OR"] || presets ["NOT"]) {
                for(var i in presets) {
                    node = new Ext.tree.TreeNode({
                        text: i,
                        expanded:true,
                        iconCls: 'icinga-icon-bricks',
                        filterType: 'group'
                    });
                    Ext.each(presets[i], function(preset) {
                        node.appendChild(this.nodeFromFilterObject(preset));
                    },this);
                }
            } else if(presets["REFERENCE"]) {
                node = new Ext.tree.TreeNode({
                    text:presets["REFERENCE"]["filter_name"],
                    leaf:true,
                    iconCls: 'icinga-icon-attach'
                });
                node.filterAttributes = presets["REFERENCE"];
                node.referenceId = presets["REFERENCE"]["referencedId"];
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

    buildTextFromFilter: function(values) {
        return (values["filter_negated"] ? 'NOT' : '')
        +" <i>"+values["filter_attribute"]+"</i>"
        +" <b>"+this.FILTERTYPES[values["filter_type"]]+"</b>"
        +" '"+values["filter_value"]+"'";
    },

    getStore: function() {
        return this.filterState.getStore();
    },

    getAvailableFiltersArray: function(record) {
        var basic = [
            ['AND','group'],
            ['OR','group'],
            ['NOT','group'],
            ['FILTER','filter']
        ];


        var store = this.getStore();
        store.each(function(checkRecord) {
            if(record) {
                if(record == checkRecord)
                    return true;
                if(this.hasCyclicRedundancies(record,checkRecord))
                    return true;
            }
            basic.push([checkRecord.get('filter_name'),checkRecord.get('filter_id')]);
            return true;
        },this);

        return basic;
    },

    hasCyclicRedundancies: function(record1,record2) {
        var id1 = record1.get("filter_id");
        var id2 = record2.get("filter_id");

        var json1 = Ext.decode(record1.get('filter_json'));
        var json2 = Ext.decode(record2.get('filter_json'));

        if(this.searchReferenceInFilterObject(id1,json2))
            return true;


        return false;
    },

    searchReferenceInFilterObject : function(id,filterObj) {
        var found = false;

        if(filterObj["AND"] || filterObj["OR"] || filterObj["NOT"]) {
            for(var i in filterObj) {
                Ext.each(filterObj[i],function(elem) {
                    found = found || this.searchReferenceInFilterObject(id,elem)
                    // get out of the loop if found anything
                    if(found)
                        return false;
                    return true;
                },this)
            }
        } else if(filterObj["REFERENCE"]) {
            if(filterObj["REFERENCE"]["referenceId"] == id)
                found = true;
        }
        return found;
    },

    getAvailableElementsList: function(record) {
        return new Ext.grid.GridPanel({
            height:400,
            enableDragDrop: true,
            autoDestroy: true,
            ddGroup:'filterEditor',
            store: new Ext.data.ArrayStore({
                fields: ['name','type'],
                idIndex: 0,
                data: this.getAvailableFiltersArray(record)
            }),
            colModel: new Ext.grid.ColumnModel({

                columns: [{
                    header:_(''),
                    dataIndex: 'type',
                    menuDisabled:true,

                    renderer: function(value, metaData) {
                        metaData.css = (value == 'group' ? 'icinga-icon-bricks' : (value == 'filter') ? 'icinga-icon-brick' : 'icinga-icon-attach');
                        value = ''
                        return value;
                    },
                    width:16

                },{
                    header: _('Type'),
                    dataIndex: 'name'
                }]

            })
        })
    },

    getDefaultRootNode: function() {
        return new Ext.tree.TreeNode({
            text:'AND',
            filterType: 'group',
            iconCls:'icinga-icon-bricks',
            id:'root',
            expanded:true
        });
    },
    initEvents: function() {
        Ext.tree.TreePanel.prototype.initEvents.apply(this,arguments);
        this.on({
            contextmenu: function(node,event) {
                event.preventDefault();
                if(!node.parentNode)
                    return false;
                new Ext.menu.Menu({
                    items: [{
                        text: _('Edit this node'),
                        iconCls: 'icinga-icon-page-edit',
                        handler: function(btn) {
                            this.addFilterTo(node,node.filterAttributes,true);
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
                    var name = elem.get('name');
                    if(elem.get('type') == 'filter') {
                        // The filter needs some tweaking before it can be added
                        this.addFilterTo(event.target);
                        event.cancel = true;
                    } else if(elem.get('type') != 'group') {
                        this.addReference(event.target,elem);
                        event.cancel = true
                    } else {
                        // Groups and predefined filters can be directly added
                        event.dropNode.push(this.loader.createNode({
                            text: elem.get('name'),
                            iconCls: (elem.get('type')  == 'group' ? 'icinga-icon-bricks' : 'icinga-icon-brick'),
                            nodeType:'node',
                            filterType: elem.get('type'),
                            leaf: elem.get('type') != 'group'
                        }))
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

    addFilterTo: function(targetNode,defaults,replace) {

        defaults = defaults || {}
        var _f = this.FILTERTYPES; // filter shorthand
        var form = new Ext.form.FormPanel({
            padding:5,
            layout:'form',
            margins:5,
            autoDestroy: true,
            autoHeight:true,
            border:false,
            defaults: {
                xtype: 'textfield',
                anchor: '90%'
            },
            items: [{
                fieldLabel: _('NOT'),
                xtype:'checkbox',
                name: 'filter_negated'
            },
            new LConf.Editors.EditorFieldManager.getEditorFieldForProperty("property",{
                name:'filter_attribute',
                value: defaults['filter_attribute'] ? defaults['filter_attribute'] :'',
                fieldLabel: _('Attribute')
            }),
            {
                fieldLabel: _('Type'),
                xtype: 'combo',
                triggerAction:'all',
                valueField: 'id',
                displayField: 'filterType',
                mode:'local',
                forceSelection:true,
                value: defaults['filter_type'] ? defaults['filter_type'] :'',
                store: new Ext.data.ArrayStore({
                    id: '0',
                    fields: ['id','filterType'],
                    data:[[1,_f[1]],[2,_f[2]],[3,_f[3]],[4,_f[4]]]
                }),
                name: 'filter_type',
                allowBlank:false
            },{
                fieldLabel: _('Value'),
                name: 'filter_value',
                value: defaults['filter_value'] ? defaults['filter_value'] :'',
                allowBlank:false
            }]
        });

        this.addctx = new Ext.Window({
            title:_('Specify filter'),
            width:500,
            renderTo:Ext.getBody(),
            modal:true,
            autoHeight:true,
            layout:'fit',
            items: form,
            buttons: [{
                text: replace ? _('Edit filter') : _('Add filter'),
                iconCls: replace ? 'icinga-icon-page-edit' : 'icinga-icon-add',
                handler: function() {
                    if(!form.getForm().isValid())
                        return false;

                    if(targetNode.isLeaf() && !replace)
                        targetNode = targetNode.parentNode;

                    var	values = form.getForm().getFieldValues();
                    var txt = this.buildTextFromFilter(values)
                    values.filter_parent = targetNode;
                    var node = this.loader.createNode({
                        text: txt,
                        iconCls: 'icinga-icon-brick',
                        nodeType:'node',
                        leaf: true
                    })
                    node.filterType = 'filter';
                    node.filterAttributes = values;
                    if(replace) {
                        targetNode.parentNode.appendChild(node)
                        targetNode.parentNode.removeChild(targetNode);
                    } else
                        targetNode.appendChild(node);

                    this.addctx.close()
                    return true;
                },
                scope:this
            },{
                text: _('Close'),
                iconCls: 'icinga-icon-cancel',
                handler: function() {this.ownerCt.ownerCt.close()}

            }]
        });
        this.addctx.show();
        return true;
    }
});