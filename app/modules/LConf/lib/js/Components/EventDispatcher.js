Ext.ns("LConf").EventDispatcher = Ext.extend(Ext.util.Observable, {
	customEvents : {},

	constructor : function(config) {
		this.listeners = config;
		this.superclass.constructor.call(this,config);
	},

    addCustomListener : function(eventName, handler, scope,options) {
		this.addEvents(eventName);
		this.customEvents[eventName] = true;
		this.addListener(eventName,handler,scope,options);
	},

    fireCustomEvent : function() {
		var eventName = (Ext.toArray(arguments))[0]
		if(!this.customEvents[eventName])
			return false;
		this.fireEvent.apply(this,arguments);
	}

});