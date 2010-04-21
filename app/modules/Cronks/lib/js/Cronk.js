Ext.ns('Cronk');

/**
 * Cronks singleton
 * 
 * @this{Cronks}
 */	
Cronk = (function(){
	
	return {
		getLoaderUrl : function(crname, url) {
			if (!Ext.isDefined(url)) {
				url = Cronk.defaults.SETTINGS.loaderUrl
			}
			return String.format('{0}/{1}/{2}', AppKit.c.path, url, crname); 
		},
		
		copyObjectConfig : function(dest, cmp) {
			dest = Ext.copyTo(dest || {}, cmp, Cronk.defaults.CONFIG_COPY);
			Ext.apply(dest, cmp.cronkConfig);
			return dest;
		},
		
		factory : function(config) {
			// Apply the needed config to our cronk
			if (Ext.isDefined(config['xtype']) && config.xtype !== 'cronk') {
				config.ptype = 'cronk-plugin'
			}
			
			return Ext.create(config, 'cronk');
		}
	}
	
})();

Ext.ns('Cronk.defaults', 'Cronk.lib', 'Cronk.util');

/**
 * Cronk defaults
 */
Cronk.defaults.SETTINGS = {
	loaderUrl:		'web/cronks/cloader',
	autoLayout:		false,
	autoRefresh:	true,
	params:			{},
	cdata:			{},
	cenv:			{},
	
	// Some default panel configs
	layout: 		'fit'
}

Cronk.defaults.CONFIG_ITEMS = [
	'loaderUrl', 'params', 'crname',
	'autoRefresh', 'cdata', 'cenv',
	'autoLayout', 'cmpid', 'stateuid'
];

Cronk.defaults.CONFIG_COPY = [
	'title', 'id', 'xtype',
	'closable', 'draggable', 'resizable',
	'cls', 'frame', 'duration', 'pinned',
	'border', 'layout'
];

/**
 * Cronk Registry
 */
(function() {
	
	var _REGISTRY;
	
	Cronk.Registry = new (_REGISTRY=Ext.extend(Ext.util.MixedCollection, {
		constructor: function() {
			_REGISTRY.superclass.constructor.call(this, false);
		},
		
		get : function(key) {
			var i = _REGISTRY.superclass.get.call(this, key);
			if (i) {
				var cronk = Ext.getCmp(i.id);
				if (cronk) {
					Cronk.copyObjectConfig(i, cronk);
					this.replace(key, i);
				}
			}
			return i;
		}
	}))();

})();

/**
 * Cronk implementation as plugin
 */
(function() {
	
	var _CRPLUG, _CRUTIL = Cronk.util;
	
	_CRPLUG = Ext.extend(Object, {
		
		initialConfig	: {},
		cmp				: null,
		cmpConfig		: null,
		cmpUpdater		: null,
		forceId			: false,
		idprefix		: false,
		
		constructor: function(config) {
			
			_CRPLUG.superclass.constructor.call(this);
			
			Ext.apply(this, {
				configCopy: Cronk.defaults.CONFIG_COPY,
				configItems: Cronk.defaults.CONFIG_ITEMS,
				configDefaults: Cronk.defaults.SETTINGS,
				forceId: false,
				idprefix: 'cronk'
			});
			
			this.initialConfig = config || {};
			Ext.apply(this, this.initialConfig);
			
			if (Ext.isDefined(config.cmp)) {
				this.init(config.cmp);
			}
			
			return this;
		},
		
		init: function(c) {;
			if (!this.cmp) {
				this.cmp = c;
			}
			
			this.applyCronkConfig();
			
			this.applyCronkEvents();
			
			Cronk.Registry.add(this.getCronkInitialConfig(this.configCopy));
		},
		
		onComponentRender : function(c) {
			this.getUpd();
			if (this.cmpConfig.autoRefresh == true) {
				this.onComponentRefresh();
			}
		},
		
		onComponentRefresh : function(cronk, me) {
			this.getUpd().update(this.getUpdaterConfig());
		},
		
		onComponentDestroy : function(c) {
			Cronk.Registry.removeKey(this.cmp.getId());
		},
		
		onComponentAdded : function(c, container, index) {
			if (this.cmpConfig.autoLayout == true) {
				_CRUTIL.layoutHandler(c);
			}
		},
		
		applyCronkEvents : function() {		
			var lcmp = this.cmp;
			
			// console.log(lcmp.getId() + ' rendered: ' + lcmp.rendered);
			
			if (lcmp.rendered == true) {
				this.onComponentRefresh();
			}
			else {
				lcmp.on('afterrender', this.onComponentRender, this, { single: true });
				lcmp.on('added', this.onComponentAdded, this);
			}
			
			lcmp.on('destroy', this.onComponentDestroy, this);
		},
		
		applyCronkConfig: function() {
			// Apply the base
			this.cmp.cronkConfig = {};
			
			Ext.applyIf(this.cmp, this.configDefaults);
			
			Ext.applyIf(this.cmp, {
				stateuid: Ext.id(null, 'cronk-sid'),
				cmpid: Ext.id(null, 'cronk-cid')
			});
			
			Ext.copyTo(this.cmp.cronkConfig, this.cmp, this.configItems);
			
			// Rempove the old stuff
			Ext.iterate(this.configItems, function(k,i) {
				if (Ext.isDefined(this.cmp[k])) {
					delete this.cmp[k];
				}
			}, this);
			
			this.cmp.cronkConfig.id = this.cmp.getId();
			
			
			// Create a reference for us
			this.cmpConfig = this.cmp.cronkConfig;
		},
		
		getUpd : function() {
			if (!this.cmpUpdater) {
				this.cmpUpdater = this.cmp.getUpdater();
			}
			
			return this.cmpUpdater;
		},
		
		getUpdaterConfig: function() {
			var c = this.cmpConfig;
			return {
				url: Cronk.getLoaderUrl(c.crname, c.loaderUrl),
				params: this.getRequestParams(),
				scripts: true,
				scope: this
			};
		},
		
		getRequestParams: function() {
			if (!this.cmpRequestParams) {
				
				var id=this.cmp.getId();
				
				this.cmpRequestParams = {
					parentid: id,
					stateuid: this.cmpConfig.stateuid,
					cmpid: this.cmpConfig.cmpid
				};
				
				if (this.cmp.stateful) {
					this.cmpRequestParams.stateuid = this.cmp.stateId;
				}
				
				Ext.iterate(this.cmpConfig.params, function(k, v) {
					this.cmpRequestParams['p[' + k +  ']'] = v;
				}, this);
			}
			return this.cmpRequestParams;
		},
		
		getCronkInitialConfig : function(items) {
			var l = this.cmpConfig;
			
			if (Ext.isArray(items)) {
				Ext.copyTo(l, this.cmp, items);
			}
			
			delete(l.loaderUrl);
			
			return l;
		},
	});
	
	_CRUTIL.layoutQueue = [];
	_CRUTIL.layoutHandler = function(cmp) {
		_CRUTIL.layoutQueue.push(cmp.getId());
		
		var task = new Ext.util.DelayedTask(function() {
			this.layoutQueue.shift();
			if (this.layoutQueue.length == 0) {
				AppKit.util.Layout.doLayout(null, 300);
			}
			
		}, _CRUTIL);
		
		task.delay(100);
	}
	
	_CRUTIL.CPlugin = _CRPLUG;
	Cronk.CPlugin = _CRPLUG;
	
	Ext.preg('cronk-plugin', _CRPLUG);
	
})();

/**
* Cronk implementation as extjs element
 */

Cronk.Container = Ext.extend(Ext.Panel, {
	initComponent: function() {
		Cronk.Container.superclass.initComponent.call(this);
		
		this.CronkPlugin = new Cronk.util.CPlugin({ cmp: this });
	}
});

Ext.reg('cronk', Cronk.Container);
