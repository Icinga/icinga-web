Ext.ns("AppKit.Admin.Components");
AppKit.Admin.Components.CredentialGrid = Ext.extend(Ext.Panel,{
    title: _('Credentials'),
    layout:'fit',
    iconCls: 'icinga-icon-key',
    
    
    
    constructor: function(cfg) {
        Ext.apply(this.cfg);
        cfg.tbar = [_('Define credentials and access rights to this ')+_(cfg.type)+_(' here')];
        this.selectionModel = new Ext.grid.CheckboxSelectionModel({
            width: 20,
            checkOnly: true,
            listeners: {
                selectionchange: function(_this) {
                    this.store.selectedValues = _this.getSelections();
                },
                scope:this
            }
        });
        cfg.items = [{
            xtype: 'grid',
            store: cfg.store,
            viewConfig: {
                forceFit: true
            },
            sm: this.selectionModel,

            columns: [ 
                this.selectionModel,
            {
                header: _('Credential'),
                dataIndex: 'target_name',
                width: 100
            },{
                header: _('Description'),
                dataIndex: 'target_description',
                width: 300
            }]
        }];
        Ext.Panel.prototype.constructor.call(this,cfg);
    },
    
    updateView: function() {
        if(this.store.selectedValues)
            this.selectionModel.selectRecords(this.store.selectedValues);
    },
    selectValues: function(principals) {
        this.selectionModel.clearSelections();
        this.store.selectedValues = [];
        Ext.iterate(principals, function(p) {
            if(p.target.target_type != 'credential') 
                return true;
            this.store.selectedValues.push(this.store.getById(p.target.target_id));
        },this);
        this.updateView();
    }
});
