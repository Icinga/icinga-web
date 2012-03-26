Ext.ns("LConf.View").ConfigWizard = Ext.extend(LConf.View.PropertyManager,{
    
    id : Ext.id("wizard"),
    root: 'properties',
    enableFb : true,
    noLoadOnSave: true,
    presets: {},

    constructor: function(config) {
        Ext.apply(this,config);
        this.presets = config.presets;
        LConf.Helper.Debug.d("Created ConfigWizard",this,arguments);
        
        LConf.View.PropertyManager.prototype.constructor.call(this,config);

        this.initMe();

    },

    initComponent: function() {
        LConf.View.PropertyManager.prototype.initComponent.apply(this,arguments);
        this.height = Ext.getBody().getHeight()*0.9 > 400 ? 400 : Ext.getBody().getHeight()*0.9;
        this.getStore().proxy.api.create.url = this.urls.modifynode;
        this.getStore().baseParams.xaction = "create";
        LConf.Helper.Debug.d("Rewrote proxy route",this.getStore());
    },

    initMe : function() {
        this.on("render", function(elem) {
            this.ownerCt.on("hide",function(elem) {
                this.getStore().rejectChanges();
                this.getStore().on("beforeload",function(store,rec,ope) {
                    return false; //supress reading
                });
            },this,{single:true})
            var record = Ext.data.Record.create(['id','property','value']);

            this.getStore().on("exception",function(proxy,type,action,options,response) {
                if(response.status != '200')
                    Ext.Msg.alert(_("Element could not be created"),_("An error occured: "+response.responseText));
                else if(response.status == '200') {
                    if(this.getStore().closeOnSave)
                        this.ownerCt.hide();
                    this.getStore().closeOnSave = false;

                    this.eventDispatcher.fireCustomEvent("refreshTree");
                }
                return true;
            },this);
            this.getStore().removeListener("save");

            if(this.presets[this.wizardView]) {
                var properties = this.presets[this.wizardView];
                for(var property in properties) {
                    this.getStore().add(new record({property:property,value:properties[property]}));
                }
            } else
                this.getStore().add(new record());

            this.fbar.add({
                xtype: 'button',
                text: _('Save and close'),
                iconCls: 'icinga-icon-disk',
                handler: function() {
                    this.getStore().closeOnSave = true;
                    this.getStore().save();
                },
                scope: this
            })
        },this);
    },
    
    width:40

});
