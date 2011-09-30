Ext.ns('AppKit.search', 'AppKit.lib');

AppKit.search.SearchHandler = (new (Ext.extend(Ext.util.Observable, {
	
	query : "",
	
	handlers : [],
	
	searchBox : null,
	
	constructor : function(config) {
		
		config = config || {};
		
		this.addEvents({
			'activate' : true,
			'deactivate' : true,
			'process' : true,
			'submit' : true
		})
		
		this.listeners = config.listeners;
		
		Ext.util.Observable.prototype.constructor.call(this, config);
	},
	
	setSearchbox : function(cmp) {
		this.searchBox = cmp;
	},
	
	getSearchbox : function() {
		return this.searchBox;
	},
	
	getTargetElement : function() {
		return this.searchBox.getEl();
	},
	
	getQuery : function() {
		return this.query;
	},
	
	doSearch : function(query, event) {
		
		if (Ext.isEmpty(event)) {
			event = 'process';
		}
		
		if (query !== this.query || event == 'submit') {
			if (this.fireEvent(event, this, query) !== false) {
				this.query = query;
			}
		}
	},
	
	activate : function(wnd, field) {
		return this.fireEvent('activate', this, wnd, field);
	},
	
	deactivate : function() {
		if (this.fireEvent('deactivate', this) !== false) {
			this.query = "";
		}
	},
	
	registerHandler : function(fn, scope) {
		Ext.util.Observable.capture(this, fn, scope || this);
		this.handlers.push([fn, scope]);
	},
	
	isReady : function() {
		return (this.handlers.length || this.hasListener('process'));
	}
	
})));