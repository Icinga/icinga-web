Ext.ns("Icinga.Grid.Plugins");
Icinga.Grid.Plugins.FilterableGrid = function(cfg) {
    this.filter = {}
    this.target = null;
    this.descriptor = null;
    this.constructor = function(cfg) {
        this.descriptor = cfg;
    }
    
    this.init = function(grid) {
        this.target = grid;
        grid.showFilterWindow = this.showFilterWindow.createDelegate(this);
    }
    
    this.showFilterWindow = function() {
        var body = Ext.getBody();
        var wnd = new Ext.Window({
            title: _('Modify Filter'),
            width: body.getWidth()*0.5,
            height: body.getHeight()*0.5,
            minWidth: 400,
            minHeight: 400,
            constrain:true,
            layout: 'border',
            items: [
            {
                region:'center', 
                xtype: 'panel',
                title: _('Current filter (Drop elements to modify)'),
                width:200,
                minWidth: 200,
                layout: 'fit',
                items: this.getFilterTree()
             },{
                region: 'east',
                width: 200,
                minWidth: 200,
                split:true,
                xtype: 'panel',
                layout: 'fit',
                title: _('Available filter'),
                items: this.getFilterList()
            }]
        });
        wnd.show(); 
    }
   
    this.getOperatorBox = function(data) {
        var operatorArray = [];
        for(var i in data.operators) {
            operatorArray.push([data.operators[i],i]);
        }
        var cmb = new Ext.form.ComboBox({
            typeAhead: true,
            forceSelect: true,
            width: 200,
            store: operatorArray,
            fieldLabel: _('Operator')
        });
        return cmb; 
    }
 
    this.getValueField = function(data) {
        var store;
        for(var i=0;i<data.values.length;i++) {
            if(data.values[i].column)
                return new Icinga.Api.RESTFilterComboBox({
                    target: data.values[i].target,
                    width: 200,
                    field: data.values[i].column,
                    fieldLabel: _('Value')
                });
        }
    }   

    this.showFilterDialog = function(data,targetNode,tree) {
       
        var filterItems = [];
        filterItems.push({
            xtype: 'component',
            html: '<h1>Filter: '+Ext.util.Format.htmlEncode(data.displayName)+'</h1><br/>'
        });
        filterItems.push(this.getOperatorBox(data));
        filterItems.push(this.getValueField(data));
        var h = Ext.getBody().getHeight();
        var w = Ext.getBody().getWidth();
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
                items: filterItems
            
            }),
            buttons: [{
                text: _('Save'),
                handler: function() {
                    
                }
            }]
        }).show();
    }
    
    this.getFilterTree = function() {
        var defaultTreeRoot =  new Ext.tree.TreeNode({
            text:'AND', 
            filterType: 'group', 
            iconCls:'icinga-icon-bricks', 
            expanded:true
        });
        var outerScope = this;
        return new Ext.tree.TreePanel({ 
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
                beforeNodeDrop: function(event) {
                    event.cancel = false;
                    event.dropNode = [];
                
                    if(event.data.node) {
                        event.dropNode = [event.data.node];
                        return true;
                    }
                    Ext.each(event.data.selections,function(elem) {
                        var name = elem.get('name');
                        if(elem.get('type') == 'atom') {
                            // The filter needs some tweaking before it can be added 
                            outerScope.showFilterDialog(elem.data.additional,event.target,this);
                            event.cancel = true;
                        } else { 
                            // Groups and predefined filters can be directly added
                            event.dropNode.push(
                                this.loader.createNode({
                                    text: elem.get('displayName'),
                                    iconCls: (elem.get('type')  == 'group' ? 'icinga-icon-bricks' : 'icinga-icon-brick'),
                                    nodeType:'node',
                                    filterType: elem.get('type'),
                                    leaf: elem.get('type') != 'group'
                                })
                            ); 
                        }
                    },this)
                }
            }

        });
    }

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
                console.log(this.descriptor.allowedFilter[i].types);
                for(var x in this.descriptor.allowedFilter[i].types) {
                    var group = this.descriptor.allowedFilter[i].types[x]; 
                    filterList.push([group,'group',x]);
                }
            }
        }
        return filterList;
    }

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
                        value = ''
                        return value;
                    },
                    width:16
                    
                },{
                    header: _('Filter'),
                    dataIndex: 'displayName'
                }]
            
            })
        })

    }  
 
    this.constructor(cfg);
}

Icinga.Grid.Plugins.FilterableGridWindow = 
