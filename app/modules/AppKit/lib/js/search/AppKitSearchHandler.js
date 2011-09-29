Ext.ns('AppKit.search', 'AppKit.lib');

AppKit.search.SearchHandler = (new (Ext.extend(Ext.util.Observable, {
	
	query : "",
	
	handlers : [],
	
	constructor : function(config) {
		
		config = config || {};
		
		this.addEvents({
			'activate' : true,
			'deactivate' : true,
			'process' : true
		})
		
		this.listeners = config.listeners;
		
		Ext.util.Observable.prototype.constructor.call(this, config);
	},
	
	getQuery : function() {
		return this.query;
	},
	
	doSearch : function(query) {
		
		if (query !== this.query) {
			if (this.fireEvent('process', this, query) !== false) {
				this.query = query;
			}
		}
	},
	
	activate : function(wnd, field) {
		return this.fireEvent('activate', this, wnd, field);
	},
	
	deactivate : function() {
		return this.fireEvent('deactivate', this);
	},
	
	registerHandler : function(fn, scope) {
		Ext.util.Observable.capture(this, fn, scope || this);
		this.handlers.push([fn, scope]);
	},
	
	isReady : function() {
		return (this.handlers.length || this.hasListener('process'));
	}
	
})));