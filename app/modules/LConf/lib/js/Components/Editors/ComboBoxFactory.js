Ext.ns("LConf.Editors").ComboBoxFactory = new function() {
    var baseRoute = "";
    this.setBaseRoute = function(route) {
        baseRoute = route;
    }
    this.getBaseRoute = function() {
        return baseRoute;
    }

    this.create = function(src,urls) {
        
        var propertyStore = new Ext.data.JsonStore({
            autoDestroy:false,
            url: String.format(urls.ldapmetaprovider),
            baseParams: {field:src}
            // Metadata is provided by the server
        })

        return Ext.extend(Ext.form.ComboBox,{
            triggerAction: 'all',
            lazyRender:true,
            displayField: 'entry',
            valueField: 'entry',
            enableKeyEvents: true,
            mode:'remote',
            store: propertyStore,
            listeners: {
                afterrender: function(cmp) {
                    cmp.keyNav.enter = function() {}
                }
            }
        });
    }
};