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
					if (Ext.isDefined(config[item])) {
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
				// Apply the needed config to our cronk
				if (Ext.isDefined(config['xtype']) && config.xtype !== 'cronk') {
					var p = Ext.ComponentMgr.types[ config.xtype ];
					Ext.iterate(p.prototype, function(key, val) {
						if (Ext.isPrimitive(val)) {
							config[key] = val;
						}
					})
				}
				
				return new Cronk.Container(config);
			}
			
		});
		
		return pub;
	})();
	
	/*
	 * Default cronk settings
	 */
	Ext.ns('Cronk.defaults');
	
	Cronk.RegistryClass = function() {
		Cronk.RegistryClass.superclass.constructor.call(this, false);
	}
	
	Ext.extend(Cronk.RegistryClass, Ext.util.MixedCollection, {
		get : function(key) {
			var i = Cronk.RegistryClass.superclass.get.call(this, key);
			var cronk = Ext.getCmp(i.id);
			if (cronk) {
				Ext.apply(i, cronk.initialCronkConfig());
				this.replace(key, i);
			}
			return i;
		}
	});
	
	Cronk.Registry = new Cronk.RegistryClass();
	
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
	
	Cronk.defaults.CONFIG_COPY = [
		'title', 'id', 'xtype',
		'closable', 'draggable', 'resizable'
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
		
		Cronk.Registry.add(this.initialCronkConfig(Cronk.defaults.CONFIG_COPY));

		this.iscronk = true;

		this.on('destroy', function(c) {
			Cronk.Registry.removeKey(c.id);
		}, this);
	};
	
	Ext.extend(Cronk.Container, Ext.Panel, {
		
		cronkConfig : {},
		cronkParams : {},
		
		initialCronkConfig : function(items) {
			var l = this.cronkConfig;
			
			items = (items || Cronk.defaults.CONFIG_COPY);
			
			if (Ext.isArray(items)) {
				Ext.copyTo(l, this, items);
			}
			
			delete(l.loaderUrl);
			
			return l;
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
				id = (config.id) ? config.id : Ext.id(null, 'cronkitem-');
			}

			if (!config.parentid) config.parentid=id;
			if (config.stateId) {
				config.stateuid = config.stateId;
			}
			else {
				config.stateuid = id;
			}
			
			config = Ext.applyIf(config, ls);

			this.cronkParams = Ext.applyIf(config.params || {}, {
				parentid: config.parentid || id,
				stateuid: config.stateuid || id
			});
			
			config.id = id;
			
			this.cronkConfig = Cronk.extractConfig(config);
			this.orgConfig = {};
			Ext.apply(this.orgConfig, config);			
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
