Ext.Ajax.request = function(o) {
    if(!o.icingaAction || !o.icingaModule) {
        return this.directRequest(o);
    } else {
        return this.dispatchRequest(o);
    }
};

Ext.Ajax.directRequest = function(o) {
    if(!o.allowBatch) {
        return Ext.data.Connection.prototype.request.call(this,o);
    } else {
        return Ext.data.Connection.prototype.request.call(this,o);
    }
};

Ext.Ajax.dispatchRequest = function(o) {
   
    if(!o.url)
        o.url = AppKit.c.path+'/modules/appkit/dispatch';
    var p = o.params;
    o.params = {};
    o.params.module = o.icingaModule;
    o.params.action = o.icingaAction;
    if(o.output_type)
        o.params.output_type = o.output_type;
    
    o.params.params = Ext.encode(p);
    return Ext.data.Connection.prototype.request.call(this,o);
};
