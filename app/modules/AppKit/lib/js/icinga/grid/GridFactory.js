Ext.ns("Icinga.Grid");
Icinga.Grid.GridFactory = function() {
    var getFieldName = function(field,allFields) {
        for(var i in allFields)
            if(allFields[i] == field)
                return i;
        return field;
    }
    
    var getColModel = function(fields) {
        var fieldList = fields.defaultFields || fields.allowedFields;
        var cols = [];
        for(var i=0;i<fieldList.length;i++) { 
            cols.push({
                id: fieldList[i],
                header: getFieldName(fieldList[i],fields.allowedFields),
                dataIndex: fieldList[i] 
            });
        }
        return new Ext.grid.ColumnModel(cols);
    }

    var addTopToolbar = function(cfg, descriptor) {        
        var tbarCfg = {
            items:[{
                text: _('Refresh'),
                iconCls: 'icinga-icon-arrow-refresh'
            },{
                text: _('Settings'),
                iconCls: 'icinga-icon-cog',
                menu: [{
                    text: _('Auto refresh'),
                    checked: true
                }]
            },'|']  
        } 
        
        if(descriptor.filter) {
            tbarCfg.items.push({
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
                    iconCls: 'icinga-icon-cancel'
                }]
            });    
            cfg.plugins.push(new Icinga.Grid.Plugins.FilterableGrid(descriptor.filter));
        }
        
        var tbar = new Ext.Toolbar(tbarCfg);
        cfg.tbar = tbar;
        return cfg;
    }           

    this.getGridFor = function(module,provider,store,db,overrides) {
        var obj = Ext.grid.GridPanel;  
        var dataStore = Icinga.Api.Provider.getStoreFor(module,provider,store,db,overrides);
        var descriptor = Icinga.Api.Provider.getProviderDescriptor(module,provider,store);
        if(!dataStore)
            return null; 
        cfg = {
            store: new dataStore(),
            colModel: getColModel(descriptor.fields,descriptor),
            plugins: []
        }
      
        cfg = addTopToolbar(cfg, descriptor);
        var grid = Ext.extend(obj,cfg);
        
        return grid;
    }
}
