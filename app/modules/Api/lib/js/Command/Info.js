Ext.ns('Icinga.Api.Command');

Icinga.Api.Command.Info = Ext.extend(Object, {
	
    infoUrl : null,
    loaded : false,
    data : {},
    loaded : false,
    autoLoad : false,

    constructor : function(config) {
    	Icinga.Api.Command.Info.superclass.constructor.call(this);
    	
    	Ext.apply(this, config);
    	
    	this.infoUrl = String.format('{0}/web/api/cmdInfo/json', AppKit.util.Config.get("path"));
    	
    	if (this.autoLoad === true) {
    		this.loadCommandDefinitions();
    	}
    },
    
    loadCommandDefinitions : function() {
    	if (this.loaded === true) {
    		return true;
    	}
    	
    	var abort = false;
    	
    	Ext.Ajax.request({
    		url : this.infoUrl,
    		callback : function(options, success, response) {
    			
    			if (success === false) {
    				this.data = { success : false };
    				this.loaded = false;
    				return false;
    			}
    			try {
	    			var data = Ext.decode(response.responseText);
	                this.data = data.results;
	                this.loaded = data.success;
    			} catch(e) {
    				this.loaded = false;
    			}
    		},
    		scope : this
    	});
    	
    	return true;
    },
    
    get : function(commandName) {
    	if (Ext.isEmpty(commandName)) {
    		return this.data;
    	} else {
    		return this.data[commandName];
    	}
    }
});
