(function() {

	Ext.ns('Cronk', 'Cronk.items');

	/**
	 * Cronks singleton
	 * 
	 * @this{Cronks}
	 */	
	Cronk = (function() {
		
		var pub = {};
		var ci = 0;
		
		Ext.apply(pub, {
			
			getLoaderUrl : function(crname, url) {
				if (!Ext.isDefined(url)) {
					url = Cronk.defaults.SETTINGS.loaderUrl
				}
				return String.format('{0}/{1}/{2}', AppKit.c.path, url, crname); 
			},
			
			removeCci : function(config, items) {
				Ext.each(items, function(item, index, l) {
					if (item in config) {
						delete(config[item]);
					}
				})
				return config;
			},
			
			extractConfig : function (config) {
				var o = Ext.copyTo({}, config, Cronk.defaults.CONFIG_ITEMS);
				this.removeCci(config, Cronk.defaults.CONFIG_ITEMS);
				return o;
			},
			
			factory : function(config) {
				config.xtype = 'cronk';
				return Ext.create(config);
			}
			
		});
		
		return pub;
	})();
	
	/*
	 * Default cronk settings
	 */
	Ext.ns('Cronk.defaults');
	
	Cronk.Registry = new Ext.util.MixedCollection(false);
	
	Cronk.defaults.SETTINGS = {
		loaderUrl:	'web/cronks/cloader',
		layout:		'fit',
		xtype:		'panel',
		autoLayout: false,
		autoRefresh: true
	}
	
	Cronk.defaults.CONFIG_ITEMS = [
		'loaderUrl', 'params', 'crname',
		'cmpid', 'parentid', 'stateuid',
		'autoLayout', 'autoRefresh'
	];
	
	/*
	 * Cronk implementation as extjs element
	 */
	
	Cronk.Container = function(config) { 
		
		this.listeners = config.listeners;
		
		this.addEvents({
			'refresh': true
		});
		
		this.rparams = null;
		this.uref = null
		
		this.applyCronkConfig(config);
		
		Cronk.Container.superclass.constructor.call(this, config);
		
		Cronk.Registry.add(this.initialCronkConfig());
		
		this.on('destroy', function(c) {
			Cronk.Registry.removeKey(c.id);
		}, this);
	};
	
	Ext.extend(Cronk.Container, Ext.Panel, {
		
		cronkConfig : {},
		cronkParams : {},
		
		initialCronkConfig : function() {
			return Ext.apply({}, this.initialConfig, this.cronkConfig);
		},
		
		onRender : function(ct, position) {
			Cronk.Container.superclass.onRender.call(this, ct, position);
			this.getUpdater();
			
			if (this.cronkConfig.autoRefresh == true) {
				this.getUpdater().refresh();
			}
		},
		
		onRefresh : function(el, res) {
			this.doLayout();
			this.fireEvent('refresh', this);
		},
		
		getUpdater : function() {
			if (!this.uref) {
				this.uref = Cronk.Container.superclass.getUpdater.call(this);
				
				this.uref.setDefaultUrl({
					url : this.loaderUrl(),
					scripts: true,
					params: this.requestParams(),
				});
				
				this.uref.on('update', this.onRefresh, this, { delay: 2 });
			}
			
			return this.uref;;
		},
		
		applyCronkConfig : function(config) {
			var id = null;
			var ls = Cronk.defaults.SETTINGS;
			
			if (config.parentid) {
				id = config.parentid;
			}
			else {
				id = (config.id) ? config.id : this.getId();
			}
			
			Ext.applyIf(config, ls);
			
			this.cronkParams = Ext.applyIf(config.params || {}, {
				parentid: config.parentid || id,
				stateuid: config.stateuid || id
			});
			
			config.id = id;
			
			this.cronkConfig = Cronk.extractConfig(config);
		},
		
		requestParams : function() {
			if (!this.rparams) {
				this.rparams = {};
				for (var i in this.cronkParams) {
					this.rparams['p[' +  i + ']'] = this.cronkParams[i]; 
				}
			}
			return this.rparams;
		},
		
		loaderUrl : function() {
			return Cronk.getLoaderUrl(this.cronkConfig.crname, this.cronkConfig.loaderUrl);
		},
		
		doRefresh : function() {
			this.getUpdater().refresh();
		}
		
	});
	
	Ext.reg('cronk', Cronk.Container);
	
})();
