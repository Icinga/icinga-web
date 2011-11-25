Ext.ns('Icinga.Api');
Icinga.Api.RESTFilterComboBox = Ext.extend(Ext.ux.AutoComboBox,{
    constructor: function(cfg) {
        cfg = cfg || {};
     
        Ext.apply(this,cfg);
        cfg.pageSize = 20;

        cfg.idProperty = cfg.targetField; 
        var store = new Icinga.Api.RESTStore({
            idProperty: cfg.idField || cfg.targetField,
            columns: cfg.idField ? [cfg.idField,cfg.targetField] : [cfg.targetField],
            target: cfg.target,
            orderColumn: cfg.targetField,
            countColumn: cfg.targetField
           
        });
        
        cfg.displayField = cfg.targetField; 
        cfg.valueField = cfg.targetField; 
        this.storeFieldName = cfg.targetField;
        AppKit.log(cfg);
        cfg.store = store; 
        Ext.ux.AutoComboBox.prototype.constructor.call(this,cfg);

    },
    setTarget: function(target) {
        this.target = target;
        this.getStore().setTarget(target);
    },
    resetField: function(targetField,idField) {
        this.store = new Icinga.Api.RESTStore({
            idProperty: idField || targetField,
            columns: idField ? [idField,targetField] : [targetField],
            target: this.target,
            orderColumn: targetField,
            countColumn: targetField
        });
        this.storeFieldName = targetField;
        this.displayField = targetField;
    },
    filter: function(field, value, exact) {
        var method = ['LIKE'];
        if(exact)
            method = ['='];
        else 
            value = '%'+value+'%';
        var store = this.getStore();
        store.addColumn(field);
        
        this.baseFilter = {
            type: 'atom',
            field: [field],
            method: method,
            value: [value]
        };
    },
    
    getFilter:  function(query) {
        var filter = {
            type: 'AND',
            field: [{
                type: 'atom',
                field: [this.storeFieldName],
                method: ['LIKE'],
                value: ['%'+query+'%']
            }]
        };
        if(this.baseFilter)
            filter.field.push(this.baseFilter);
        return filter;
    },
    
    doQuery: function(query) {
        var store = this.getStore();
        
        store.setFilter(
            this.getFilter(query)
        );
        Ext.ux.AutoComboBox.prototype.doQuery.apply(this,arguments);
    }
});


Icinga.Api.HostgroupsComboBox = Ext.extend(Icinga.Api.RESTFilterComboBox, {
    constructor: function(cfg) {

        Icinga.Api.RESTFilterComboBox.prototype.constructor.call(this,{
            targetField: 'HOSTGROUP_NAME',
            idField: 'HOSTGROUP_ID',
            target: 'hostgroup',
            displayField: 'HOSTGROUP_NAME',
            listeners: cfg ? cfg.listeners : null
        });
    }
});

Icinga.Api.ServicegroupsComboBox = Ext.extend(Icinga.Api.RESTFilterComboBox, {
    constructor: function(cfg) {

        Icinga.Api.RESTFilterComboBox.prototype.constructor.call(this,{
            targetField: 'SERVICEGROUP_NAME',
            idField: 'SERVICEGROUP_ID',
            target: 'servicegroup',
            displayField: 'SERVICEGROUP_NAME',
            listeners: cfg ? cfg.listeners : null
        });
    }
});


Icinga.Api.HostsComboBox = Ext.extend(Icinga.Api.RESTFilterComboBox, {
    constructor: function(cfg) {
        Icinga.Api.RESTFilterComboBox.prototype.constructor.call(this,{
            targetField: 'HOST_NAME',
            idField: 'HOST_ID',
            target: 'host',
            displayField: 'HOST_NAME',
            listeners: cfg ? cfg.listeners : null
        });
    }
});

Icinga.Api.ServicesComboBox = Ext.extend(Icinga.Api.RESTFilterComboBox, {
    constructor: function(cfg) {

        Icinga.Api.RESTFilterComboBox.prototype.constructor.call(this,{
            targetField: 'SERVICE_NAME',
            target: 'service',
            idField: 'SERVICE_ID',
            displayField: 'SERVICE_NAME',
            listeners: cfg ? cfg.listeners : null
        });
    }
});
Ext.reg('IcingaRESTComboBox', Icinga.Api.RESTFilterComboBox);
Ext.reg('IcingaHostgroupComboBox', Icinga.Api.HostgroupsComboBox);
Ext.reg('IcingaServicegroupComboBox', Icinga.Api.ServicegroupsComboBox);
Ext.reg('IcingaHostComboBox', Icinga.Api.HostsComboBox);
Ext.reg('IcingaServiceComboBox', Icinga.Api.ServicesComboBox);
