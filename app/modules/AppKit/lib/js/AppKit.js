(function() {
	Ext.ns('AppKit');
	
	AppKit = (function() {
	
		var pub = {};
		
		Ext.apply(pub, {
			
			log : function() {
				if (typeof console !== "undefined" && console.log) {
					console.log[console.firebug ? 'apply' : 'call'](console,Array.prototype.slice.call(arguments));
				}
			},
			
			logargs : function(context) {
				this.log(context,arguments.callee.caller.arguments);
			}
			
		});
		
		window.log = pub.log;
		window.logargs = pub.logargs;
		
		return pub;
	
	})();
	
	Ext.ns('AppKit.util');
	
	AppKit.util.EventDispatcher = new (Ext.extend(Ext.util.Observable, {
		
		constructor : function(config) {
			
			this.listeners = {};
			this.events = {};
			
			this.superclass.constructor.call(this, config);
		},
	    
		hasEvent : function(eventName) {
			if (eventName in this.events && Ext.isDefined(this.events[eventName])) {
				return true;
			}
			return false;
		},
		
		addEvent : function(eventName, etrue) {
			var e = {};
			e[eventName] = (etrue) ? true : false;
			this.addEvents(e);
		},
		
		addListener : function(eventName, fn, scope, options) {
			if (this.hasEvent(eventName) == false) {
				this.addEvent(eventName);
			}
			
			return this.superclass.addListener.call(eventName, fn, scope, options);
		}
		
	}))();
	
})()