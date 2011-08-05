Ext.ns('Icinga.Api').RESTStore = Ext.extend(Ext.data.JsonStore,{
    target: null,
    columns: null,
    filter: null, 

    orderColumn: null,
    orderDirection: null,
    limit: -1,
    offset: 0,
    countField: null,

    constructor: function(cfg) {
        Ext.apply(this,cfg);
        if(cfg.columns) {
            Ext.isArray(this.fields) ? 
                cfg.fields = cfg.columns : cfg.fields = [cfg.columns];
        }
        cfg.root = 'result';
        cfg.url = AppKit.c.path+"/modules/web/api/json"; 
        Ext.data.JsonStore.prototype.constructor.call(this,cfg);
    },  

    setColumns: function(cols) {
        this.columns = cols;
        
    },
    
    setCountField: function(field) {
        this.countField = field;
    },    

    setTarget: function(target) {
        this.target = target;
    },

    setFilter: function(filter) {
        this.filter = filter;
    },

    setOderColumn: function(order) {
        this.orderColumn = order;
    },

    setOrderDirection: function(dir) {
        if(dir == "ASC")
            this.orderDirection = dir;
        else
            this.orderDirection = "DESC";
    },

    setDB: function(db) {
        this.db = db;
    },

    setLimit: function(limit) {
        limit = parseInt(limit,10);
        if(limit>0)
            this.limit = limit;
        else 
            this.limit = -1;
    },

    setOffset: function(offset) {
        offset = parseInt(offset,10);
        if(offset > 0)
            this.offset = offset;
        else
            this.offset = 0;
    },

    getTarget: function() {
        return this.target;
    },
    
    getFilter: function() {
        return this.filter;
    },
    
    getFilterAsJson: function() { 
        return Ext.encode(this.getFilter());
    },
    
    getOrderColumn: function() {
        return this.orderColumn;
    },
    
    getOrderDirection: function() {
        return this.orderDirection == "ASC" ? "ASC" : "DESC";
    },
    
    getCountColumn: function() {
        return this.countColumn;
    },
    
    getLimit: function() {
        if(this.limit < 0)
            return null;
        return parseInt(this.limit,10);
    },
    
    getOffset: function() {
        if(this.offset < 1)
            return null;
        return parseInt(this.limit,10);
    },
    
    getDB: function() {
        return this.db; 
    },
    getColumns: function() {
        return this.columns;
    }, 
    load: function(options) {
        options = options || {params: {}};
        
        var cols    = this.getColumns();
       
        var target      = this.getTarget();
        var filter      = this.getFilterAsJson();
        var order       = this.getOrderColumn() ? this.getOrderColumn()+";"+this.getOrderDirection() : null;
        var countCol    = this.getCountColumn();
        var limit       = this.getLimit();
        var offset      = this.getOffset();
        var db          = this.getDB();
        cfg = {
            db : db,
            target: target
        }
        if(filter != 'null' && filter)
            cfg.filters_json = filter;
        if(order)
            cfg.order = order;
        if(countCol)
            cfg.countColumn = countCol;
        if(limit)
            cfg.limit = limit
        if(offset)
            cfg.limit_start = offset
        if(!Ext.isArray(cols)) 
            cols = [cols];
         for(var i=0;i<cols.length;i++) {
            cfg["columns["+i+"]"] = cols[i];      
        } 
        options.params = cfg;
        return Ext.data.JsonStore.prototype.load.call(this,options);
    }
});
