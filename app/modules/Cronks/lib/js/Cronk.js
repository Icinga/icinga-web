/*global Ext: false, Icinga: false, AppKit: false, _: false, Cronk: false */
Ext.ns('Cronk');

(function () {
    "use strict";

    /**
     * Cronks singleton
     * 
     * @this{Cronks}
     */
    Ext.apply(Cronk, (function () {
        var lodate = new Date();
        var idstart = parseInt(lodate.getTime() / 1000, 10);

        return {

            AUTO_CID: idstart,

            getId: function (prefix) {
                return prefix + (++Cronk.AUTO_CID);
            },

            getLoaderUrl: function (crname, url) {
                if (!Ext.isDefined(url)) {
                    url = Cronk.defaults.SETTINGS.loaderUrl;
                }
                return String.format('{0}/{1}/{2}', AppKit.c.path, url, crname);
            },

            copyObjectConfig: function (dest, cmp) {
                dest = Ext.copyTo(dest || {}, cmp, Cronk.defaults.CONFIG_COPY);
                Ext.apply(dest, cmp.cronkConfig);
                return dest;
            },

            factory: function (config) {
                // Apply the needed config to our cronk
                if (Ext.isDefined(config.xtype) && config.xtype !== 'cronk') {
                    config.ptype = 'cronk-plugin';
                }

                if (!Ext.isDefined(config.xtype)) {
                    config.xtype = 'cronk';
                }

                return Ext.create(config, 'cronk');
            }
        };

    })());

    Ext.ns('Cronk.defaults', 'Cronk.lib', 'Cronk.util');

    /**
     * Cronk defaults
     */
    Cronk.defaults.SETTINGS = {
        loaderUrl: 'modules/cronks/cloader',
        autoLayout: false,
        autoRefresh: true,
        params: {},
        cdata: {},
        cenv: {},
        local: {},

        // Some default panel configs
        layout: 'fit'
    };

    Cronk.defaults.CONFIG_ITEMS = [
        'loaderUrl', 
        'params', 
        'crname', 
        'autoRefresh', 
        'cdata', 
        'cenv', 
        'autoLayout', 
        'cmpid', 
        'stateuid', 
        'vars'
    ];

    Cronk.defaults.CONFIG_COPY = [
        'title', 
        'xtype', 
        'closable', 
        'draggable', 
        'resizable', 
        'cls', 
        'frame', 
        'duration', 
        'pinned', 
        'border', 
        'id'
    ];

    /**
     * Cronk Registry (Currently used cronks)
     */
    (function () {
        Cronk.Registry = new(Ext.extend(Ext.util.MixedCollection, {
            constructor: function () {
                Ext.util.MixedCollection.prototype.constructor.call(this, false);
            },

            get: function (key) {
                var i = Ext.util.MixedCollection.prototype.get.call(this, key);
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
     * Cronk inventory (All available cronks to user)
     */
    Cronk.Inventory = new Ext.util.MixedCollection();

    /**
     * Cronk implementation as plugin
     */
    (function () {

        var _CRPLUG, _CRUTIL = Cronk.util;

        _CRPLUG = Ext.extend(Object, {

            initialConfig: {},
            cmp: null,
            cmpConfig: null,
            cmpUpdater: null,
            cmpDefaultUrl: null,
            forceId: false,
            idprefix: false,

            constructor: function (config) {

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

            init: function (c) {
                if (!this.cmp) {
                    this.cmp = c;
                }
                this.applyCronkConfig();
                this.applyCronkEvents();
                // AppKit.log("CREATE", this.getCronkInitialConfig(this.configCopy));
                Cronk.Registry.add(this.getCronkInitialConfig(this.configCopy));
            },

            onComponentRender: function (c) {
                this.getUpd();
                
                if (this.cmpConfig.autoRefresh === true) {
                    this.onComponentRefresh();
                }
                
                if (this.cmp.getEl()) {
                	this.setCronkDomAttributes();
                }
            },

            onComponentRefresh: function (cronk, me) {
                // this.getUpd().update(this.getUpdaterConfig());
                this.getUpd().refresh();
            },

            onComponentDestroy: function (c) {
                Cronk.Registry.removeKey(this.cmp.getId());
            },

            onComponentAdded: function (c, container, index) {
                if (this.cmpConfig.autoLayout === true) {
                    _CRUTIL.layoutHandler(c);
                }
            },

            applyCronkEvents: function () {
                var lcmp = this.cmp;

                if (lcmp.rendered === true) {
                    this.onComponentRefresh();
                } else {
                    lcmp.on('afterrender', this.onComponentRender, this, {
                        single: true
                    });
                    lcmp.on('added', this.onComponentAdded, this);
                }

                // inform the actual cronk items about the 'show' event
                lcmp.on('show', function () {
                    if (this.items) {
                        this.items.each(function (i) {
                            if (i.fireEvent) {
                                i.fireEvent("show", i);
                            }
                        });
                    }
                }, lcmp);
                lcmp.on('destroy', this.onComponentDestroy, this);
            },

            applyCronkConfig: function () {
                // Apply the base
                this.cmp.cronkConfig = {};

                delete this.cmp.parentid;

                Ext.applyIf(this.cmp, this.configDefaults);

                Ext.applyIf(this.cmp, {
                    stateuid: Cronk.getId('cronk-sid'),
                    cmpid: Cronk.getId('cronk-cid'),
                    parentid: this.cmp.getId()
                });

                Ext.copyTo(this.cmp.cronkConfig, this.cmp, this.configItems);

                // Rempove the old stuff
                Ext.iterate(this.configItems, function (k, i) {
                    if (Ext.isDefined(this.cmp[k])) {
                        delete this.cmp[k];
                    }
                }, this);

                // this.cmp.cronkConfig.id = this.cmp.getId();
                this.cmp.cronkConfig.parentid = this.cmp.getId();

                // Create a reference for us
                this.cmpConfig = this.cmp.cronkConfig;
            },
            
            setCronkDomAttributes: function() {
                var el = this.cmp.getEl();
                
                el.set({
                    'cronk:name': this.cmp.cronkConfig.crname,
                    'cronk:title': this.cmp.cronkConfig.title || 'untitled'
                });
                
                var cParams = {};
                Ext.iterate(this.cmp.cronkConfig.params, function(k, v) {
                	// A very very small security check
                	if (!k.match(/^pass/i)) {
                	   cParams['cronkparam:' + k] = v;
                	}
               });
               
               el.set(cParams);
            },

            getUpd: function () {
                if (!this.cmpUpdater) {
                    var _urlObj = this.getUpdaterConfig();
                    var _luObj = this.cmp.getUpdater();
                    _luObj.setDefaultUrl(_urlObj);
                    this.cmpUpdater = _luObj;
                }
                return this.cmpUpdater;
            },

            getUpdaterConfig: function () {
                var c = this.cmpConfig;

                if (!this.cmpDefaultUrl) {
                    this.cmpDefaultUrl = {
                        url: Cronk.getLoaderUrl(c.crname, c.loaderUrl),
                        params: this.getRequestParams(),
                        scripts: true,
                        discardUrl: false,
                        nocache: true,
                        scope: this
                    };
                }

                return this.cmpDefaultUrl;
            },

            getRequestParams: function () {
                if (!this.cmpRequestParams) {

                    var id = this.cmp.getId();

                    this.cmpRequestParams = {
                        parentid: id,
                        stateuid: this.cmpConfig.stateuid,
                        cmpid: this.cmpConfig.cmpid
                    };

                    if (this.cmp.stateful) {
                        this.cmpRequestParams.stateuid = this.cmp.stateId;
                    }

                    Ext.iterate(this.cmpConfig.params, function (k, v) {
                        this.cmpRequestParams['p[' + k + ']'] = v;
                    }, this);
                }
                return this.cmpRequestParams;
            },

            getCronkInitialConfig: function (items) {
                var l = this.cmpConfig;

                if (Ext.isArray(items)) {
                    Ext.copyTo(l, this.cmp, items);
                }

                delete(l.loaderUrl);

                // Space for local data exchange
                l.local = {};

                return l;
            }
        });

        _CRUTIL.layoutQueue = [];
        _CRUTIL.layoutHandler = function (cmp) {
            _CRUTIL.layoutQueue.push(cmp.getId());

            var task = new Ext.util.DelayedTask(function () {
                this.layoutQueue.shift();
                if (this.layoutQueue.length === 0) {
                    AppKit.util.Layout.doLayout(null, 300);
                }

            }, _CRUTIL);

            task.delay(100);
        };

        _CRUTIL.CPlugin = _CRPLUG;
        Cronk.CPlugin = _CRPLUG;

        Ext.preg('cronk-plugin', _CRPLUG);

    })();

    /**
     * Cronk implementation as extjs element
     */

    Cronk.Container = Ext.extend(Ext.Panel, {
        initComponent: function () {
            Cronk.Container.superclass.initComponent.call(this);

            this.CronkPlugin = new Cronk.util.CPlugin({
                cmp: this
            });
        },

        getId: function () {
            if (this.id) {
                return this.id;
            }

            return (this.id = Cronk.getId('cr-panel-'));
        }
    });

    Cronk.getIconClass = function (image_id) {
        var prefix = 'icinga-cronk-icon-';
        var suffixArray = String(image_id).toLowerCase().split('.');
        if (suffixArray.length === 2) {
            return prefix + String(suffixArray[1]).replace(/_/g, '-');
        }
    };

})();

Ext.reg('cronk', Cronk.Container);