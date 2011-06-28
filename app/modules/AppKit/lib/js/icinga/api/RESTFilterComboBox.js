Ext.ns('Icinga.Api').RESTFilterComboBox = Ext.extend(Ext.ux.AutoComboBox,{
    constructor: function(cfg) {
        Ext.apply(this,cfg);
        cfg.idProperty = cfg.field; 
        var store = new Icinga.Api.RESTStore({
            idProperty: cfg.field,
            columns: cfg.field,
            target: cfg.target
        });
        
        cfg.displayField = cfg.field; 
        cfg.valueField = cfg.field; 
        this.storeFieldName = cfg.field;
        cfg.store = store; 
        Ext.ux.AutoComboBox.prototype.constructor.call(this,cfg);
    },

    doQuery: function(query) {
        var store = this.getStore();
        AppKit.log(query);
        store.setFilter(
            {
                type: 'AND',
                field: [{
                    type: 'atom',
                    field: [this.storeFieldName],
                    method: ['LIKE'],
                    value: ['%'+query+'%']
                }]
            }
        );
        Ext.ux.AutoComboBox.prototype.doQuery.apply(this,arguments);
    }
});
