Ext.ns("LConf").LoadingLayer = (function() {
    var mask = new Ext.LoadMask(Ext.getBody(), {msg:_("Please wait...")});

    return {
        show: function() {
            mask.show();
        },
        hide: function() {
            mask.hide();
        }
    }
})()