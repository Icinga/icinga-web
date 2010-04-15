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
	
})()