
AppKit.Ext.CronkMgr = function() {
	var pub = {};
	
	var events = {
		'beforecronkcreate' : true,
		'aftercronkcreate' : true,
	}
	
	var defaultCronkConfig = {
		loaderUrl : 'web/cronks/cloader/',
		layout : 'fit',
		xtype : 'panel',
		params : {}
	};
	
	var cronkConfigItems = ['loaderUrl', 'params', 'crname', 'cmpid', 'parentid'];
	
	var getCronkLoaderUrl = function(cronk, loaderUrl) {
		return (loaderUrl || defaultCronkConfig.loaderUrl) + cronk;
	};
	
	var extractCronkConfig = function(config) {
		var o = {};
		Ext.each(cronkConfigItems, function(item, index, arry) {
			if (config[item]) {
				o[item] = config[item];
			}
		});
		return o;
	}

	var removeCronkConfig = function(config) {
		Ext.each(cronkConfigItems, function(key, index, ary) {
				delete config[key];
		});
	}

	var cronks = new Ext.util.MixedCollection(true);
	
	pub = Ext.extend(Ext.util.Observable, {
		constructor : function() {
			this.listeners = {};
			this.addEvents({});
			
			pub.superclass.constructor.call(this);
		}
	});
	
	Ext.override(pub, {
		
		addCronk : function(d, key) {
			cronks.add(key || d.cmpid, d);
		},
		
		removeCronk : function(k) {
			cronks.removeKey(k);
		},
		
		removeCronkByObject : function(o) {
			cronks.remove(o);
		},
		
		getCronks : function() {
			return cronks;
		},
		
		create : function(config) {
			
			var d = {};
			
			Ext.applyIf(config, defaultCronkConfig);
			
			var params = config.params || {};
			var crname = config.crname;
			var url = getCronkLoaderUrl(crname, config.loaderUrl);
			
			params.cmpid = config.cmpid || AppKit.Ext.genRandomId('cronk'); 
			params.parentid = config.parentid || null;
			
			if (params.parentid) {
				config.id = params.parentid;
			}
			
			// Remove useless stuff from the config
			d.crconf = extractCronkConfig(config);
			removeCronkConfig(config);
			
			this.fireEvent('beforecronkcreate', this, config, d[config], params);
			
			// Prepare the real request params
			var rParams = new Object();
			for (var i in params) {
				rParams['p['+ i + ']'] = params[i];
			}
			
			var panel = Ext.ComponentMgr.create(config, 'panel');
			
			if (!params.parentid) {
				params.parentid = panel.id;
			}
			
			
			// Modify the updater if the panel is rendered
			panel.on('render', function(panel) {
				panel.getUpdater().setDefaultUrl({
					url: url,
					scripts: true,
					params: rParams
				});
				
				panel.getUpdater().refresh();
			});
			
			// On destroy, remove them from list
			panel.on('beforedestroy', function(cmp) {
				if (cmp.iscronk && cmp.iscronk == true) {
					AppKit.Ext.CronkMgr.removeCronk(cmp.cronkkey);
				}
			}, this)
			
			this.fireEvent('aftercronkcreate', this, panel);
			
			d.config = config;
			d.params = params;
			d.cronk = panel;
			d.cmpid = params.cmpid;
			d.parentid = params.parentid;
			d.name = crname;
			
			this.addCronk(d);
			
			panel.cronkkey = d.cmpid;
			panel.iscronk = true;
			
			return panel;
		}
		
	});
	
	return (new pub());
}();
