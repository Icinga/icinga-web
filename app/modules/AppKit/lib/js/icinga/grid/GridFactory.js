Ext.ns("Icinga.Grid");
Icinga.Grid.GridFactory = function() {
    var getFieldName = function(field,allFields) {
        for(var i in allFields)
            if(allFields[i] == field)
                return i;
        return field;
    };

    var pluginMap = {
        filter:  Icinga.Grid.Plugins.FilterableGrid,
        pagination: Icinga.Grid.Plugins.PageableGrid,
        sort: Icinga.Grid.Plugins.SortableGrid      
    };



    var addTopToolbar = function(cfg, descriptor) {        
        var tbarCfg = {
            items:[{
                text: _('Refresh'),
                iconCls: 'icinga-icon-arrow-refresh',
                handler: function(btn) {
                    btn.ownerCt.ownerCt.load();         
                }
            },{
                text: _('Settings'),
                iconCls: 'icinga-icon-cog',
                menu: [{
                    text: _('Auto refresh'),
                    checked: true
                }]
            },'-']  
        }; 
        
       
        var tbar = new Ext.Toolbar(tbarCfg);
        cfg.tbar = tbar;
        return cfg;
    };  
             

    var loadFunction = function(cfg) {
        cfg = cfg || {};
        var cm = this.getColumnModel();
        var fieldParam = this.descriptor.fields.params.fields;
        var fields = [];
        
        var params = cfg.params || {};
        for(var i=0;i<cm.getColumnCount();i++) {
            fields.push(cm.getDataIndex(i));
        }
       
        for(var i in params)
            this.getStore().setDispatcherParam(i,params[i]); 
        this.getStore().setDispatcherParam(fieldParam,Ext.encode(fields));
        this.getStore().load();
    };

    this.getGridFor = function(module,provider,store,db,overrides) {
        var obj = Ext.grid.GridPanel;  
        var dataStore = Icinga.Api.Provider.getStoreFor(module,provider,store,db,overrides);
        var descriptor = Icinga.Api.Provider.getProviderDescriptor(module,provider,store);
        if(!dataStore)
            return null;
        var factoryScope = this; 
        cfg = this.getDefaultExtendConfig(descriptor,dataStore,store); 
        cfg = addTopToolbar(cfg, descriptor);
        
        for(var i in descriptor) {
            if(pluginMap[i])
                cfg.plugins.push(new pluginMap[i](descriptor,cfg));
        }
        var grid = Ext.extend(obj,cfg);
         
        return grid;
    };


    this.getDefaultExtendConfig = function(descriptor,dataStore,target) {
        return {
            canSort: {}, 
            constructor: function(cfg) {
                if(cfg.providerColumns) {
                    cfg.colModel = this.resolveNamedColumns(cfg.providerColumns);
                } else if(!cfg.colModel && !cfg.columns) {
                    cfg.colModel = this.getColModel(descriptor.fields); 
                } 
                cfg.store = this.setupStore(dataStore,cfg,descriptor,target);    
                Ext.grid.GridPanel.prototype.constructor.call(this,cfg);
            
            },

            getColModel : function(fields) {
                var fieldList = fields.defaultFields || fields.allowedFields;
                var cols = [];
                for(var i=0;i<fieldList.length;i++) { 
                    
                    cols.push({
                        id: fieldList[i],
                        header: getFieldName(fieldList[i],fields.allowedFields),
                        dataIndex: fieldList[i],
                        sortable: this.canSort[fieldList[i]] || false
                    });
                }
                return new Ext.grid.ColumnModel(cols);
            },

            setupStore: function(storeClass,gridCfg,descriptor,target) {
                var colModel = gridCfg.colModel;
                
                var cfg = {
                    root: 'result',
                    remoteSort: true,
                    totalProperty: 'totalCount',
                    fields: [],
                    paramNames: this.storeParamNames || Ext.data.Store.prototype.defaultParamNames
                };
                for(var i=0;i<colModel.getColumnCount();i++) { 
                    cfg.fields.push(colModel.getDataIndex(i));
                }
                
                var store = new storeClass(cfg);
                store.setDispatcherParam(descriptor.fields.params.target,target);

                return store;
            },

            /*
            * Allows to only use the column aliases for defining colums
            * Checks if a alias is defined and uses it's dataIndex as defined 
            * by the apiProvider
            **/  
            resolveNamedColumns: function(cols) {
                var colModelCols = [];
                var available = descriptor.fields.allowedFields;
                
                for(var i=0;i<cols.length;i++) {
                    var column = cols[i];
                    if(Ext.isObject(column)) {
                        column.header = column.header || _(column.alias);
                        column.dataIndex = available[column.alias]; 
                        column.sortable = column.sortable || this.canSort[available[column.alias]] || false;
                    } else {
                        column = {
                            header: column,
                            dataIndex: available[column],
                            sortable: column.sortable || this.canSort[available[column]] || false
                        };
                    }
                    
                    colModelCols.push(column); 
                }
                return new Ext.grid.ColumnModel(colModelCols);
            },
        
            descriptor: descriptor,
            storeParamNames: {},
            plugins: [],
            load: loadFunction
        };
    };
};
