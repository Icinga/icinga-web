var AppKit, _=function() { return Array.prototype.join.call(arguments, ' '); };

(function() {

	var _APPKIT;
	
	 AppKit = new (_APPKIT=Ext.extend(Ext.util.Observable, function() {
		
		// - Private
		var stateInitialData = null,
		
		stateProvider = null,
		
		taskRunner = null,
		
		growlStackElement = null,
		
		growlTemplate = new Ext.Template([
			'<div class="growl-msg-message">',
				'<div class="head">{header}</div>',
				'<div>{message}</div>',
			'</div>'
		]),

		userPreferences = {},
		
		initEnvironment = function() {
			var me = AppKit;
			
			Ext.BLANK_IMAGE_URL = me.c.path + '/images/ajax/s.gif';
			Ext.chart.Chart.CHART_URL = me.c.path + '/js/ext3/resources/charts.swf';
			
			Ext.QuickTips.init();			
			growlStack();
			
			stateProvider = new Ext.ux.state.HttpProvider({
				url: String.format(me.c.path + '/modules/appkit/ext/applicationState'),
				id: 1,
				readBaseParams: { cmd: 'read' },
				saveBaseParams: { cmd: 'write' }
			});
			
			Ext.state.Manager.setProvider(stateProvider);
			
			if (stateInitialData) {
				stateProvider.initState(stateInitialData);
			}

			AppKit.util.loginWatchdog();

			me.ready = true;
			me.fireEvent('appkit-ready');
			
			return true;
		},
		
		growlStack = function() {
        	if (!growlStackElement) {
        		growlStackElement = Ext.DomHelper.insertFirst(Ext.getBody(), {
        			id: 'growl-msg-stack'
        		}, true);
        	}
        	
        	growlStackElement.alignTo(Ext.getDoc(), 'tr-tr', [-18, 10]);
        	
        	return growlStackElement;			
		};
		
		// - Public
		return {

			setPreferences : function(o) {
				if (!Ext.isEmpty(o)) {
					userPreferences = Ext.apply({}, o);
					return true;
				}

				return null;
			},

			getPreferences : function() {
				return userPreferences;
			},

			getPrefVal : function(key, def) {
				if (key in userPreferences) {
					return userPreferences[key];
				}

				if (!Ext.isEmpty(def)) {
					return def;
				}

				return null;
			},

			constructor : function() {
				this.events = {};
				this.listeners = {};
				
				this.addListener('appkit-statedata', this.onStateData, this, { single: true });
				
				this.addEvents({
					'appkit-statedata'	: true,
					'appkit-ready'		: true
				});
				
				_APPKIT.superclass.constructor.call(this);
				
				this.c = {};
				
				this.ready = false;
				
			},
			
			onStateData : function(d) {
				stateInitialData = d;
				initEnvironment();
				return true;
			},
			
			/**
			 * Set the initial application state
			 * before init!
			 */
			setInitialState : function(s) {
				var me = this;
				return me.fireEvent('appkit-statedata', s);
			},
			
			/**
			 * General log implementation
			 */
			log : function() {
				try {
					if (!Ext.isIE && console) {
						if(typeof(console.log) === 'function')
							console.log[console.firebug ? 'apply' : 'call'](console,Array.prototype.slice.call(arguments));
					}
				} catch(e) {
					/*.No logging.*/
				}
			},
			/**
			 * Sets the window location
			 */
	        changeLocation : function(sUrl) {
					// Just simple ;-)
	                window.location.href = sUrl;
	                return true;
	        },
	
			
			pageLoadingMask : function(time, remove) {
	                remove = (remove || false);
	                time = (time || 2000);
	                
	                var ids = ['icinga-portal-loading-mask', 'icinga-portal-loading'];
	                
	                if (remove) {
	                        Ext.iterate(ids, function(v) {
	                                Ext.get(v).fadeOut({remove: true});
	                        });
	                }
	                else {
	                        Ext.iterate(ids, function(v) {
	                                Ext.DomHelper.append(Ext.getBody(), {tag: 'div', id: v});
	                        });
	                        
	                        if (time > 0) {
	                        	// setTimeout(AppKit.pageLoadingMask.createDelegate(this, [0, true]), time);
	                        	var task = new Ext.util.DelayedTask(this.pageLoadingMask.createCallback(0, true), AppKit);
	                        	task.delay(time);
	                        }
	                }
	        },
	        
	        growlPopupBox : function(message, title, icon) {
	        	var box = growlTemplate.append(growlStack(), {
	        		header: title,
	        		message: message
	        	}, true);
	        	return box.boxWrap('x-box');
	        },
	        
	        notifyMessage : function(title, msg) {
	        	var la = Ext.toArray(arguments);
	        	var title = la.shift();
	        	
	        	var c = {
	        		waitTime: 2
	        	};
	        	
	        	if (Ext.isObject(la[ la.length -1 ])) {
	        		Ext.apply(c, la.pop());
	        	};
	        	
	        	var nm = String.format.apply(this, la);
	        	
	        	var ele = this.growlPopupBox(nm, title);
	        	
	        	ele.slideIn('t').pause(c.waitTime).ghost('t', {remove:true});
	        },
	        
	        getTr : function() {
	        	if (!taskRunner) {
	        		taskRunner = new Ext.util.TaskRunner();
	        	};
	        	return taskRunner;
	        },
	        
	        onReady : function(fn, scope) {
	        	var me = this;
	        	if (Ext.isFunction(fn)) {
	        		if (this.ready == true) {
	        			fn.call(scope || fn);
	        		} 
	        		else {
	        			this.on('appkit-ready', fn, scope || fn, { single: true });
	        		}
	        	}
	        }	
		}
		
	}()));
})();

Ext.ns('AppKit.lib', 'AppKit.util');
