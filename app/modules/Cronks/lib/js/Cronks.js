Ext.ns('Cronks');

Ext.onReady(function() {

	/*
	 * Cronks singleton
	 */	
	Cronks = (function() {
		
		var pub = {};
		
		Ext.apply(pub, {
			
		});
		
		return pub;
		
	})();
	
	
	
	/*
	 * Default cronk settings
	 */
	
	Ext.ns('Cronks.defaults');
	
	Cronks.defaults.SETTINGS = {
		loaderUrl:	'web/cronks/cloader',
		layout:		'fit',
		xtype:		'panel',
		params:		{}
	}
	
	Cronks.defaults.CONFIG_ITEMS = [
		'loaderUrl', 'params', 'crname',
		'cmpid', 'parentid', 'stateuid'
	];
	
	/*
	 * Cronk implementation as extjs element
	 */ 
	Cronks.Container = function(config) {
		
		this.cronkConfig = this.extractCc(config);
		
		Cronks.Container.superclass.constructor.call(this, config);
		
		// [...]
	}
	
	Ext.extend(Cronks.Container, Ext.Panel, {
		
		getLoaderUrl : function(cronk, url) {
			return (url || def.loaderUrl) + '/'+  cronk; 
		},
		
		removeCci : function(config, items) {
			Ext.each(items, function(item, index, l) {
				if (item in config) {
					delete(config[item]);
				}
			})
			return config;
		},
		
		extractCc : function (config) {
			var o = Ext.copyTo({}, config, Cronks.defaults.CONFIG_ITEMS);
			this.removeCci(config, Cronks.defaults.CONFIG_ITEMS);
			return o;
		}
		
	});
	
	Ext.reg('cronk', Cronks.Container);
	
	
	
});