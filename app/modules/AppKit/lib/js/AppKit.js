Ext.ns('AppKit', 'APPKIT.lib');

(function() {
		
	AppKit = new (Ext.extend(
		AppKit = function() {

			AppKit.superclass.constructor.call(this);
			var stateProvider = null;
			var stateInitialData = null;

		}, Ext.util.Observable, {

		ready : false,
		
		c : {
			domain: document.location.host || document.domain,
			path: document.location.pathname.replace(/\/$/, ''),
			issecure: (document.location.protocol.indexOf('https') == 0) ? true : false
		},

		initEnvironment : function() {
			Ext.BLANK_IMAGE_URL = this.c.path + '/images/ajax/s.gif';
			
			Ext.QuickTips.init();
			
			stateProvider = new Ext.ux.state.HttpProvider({
				url: String.format(this.c.path + '/appkit/ext/applicationState'),
				id: 1,
				readBaseParams: { cmd: 'read' },
				saveBaseParams: { cmd: 'write' }
			});
			
			Ext.state.Manager.setProvider(stateProvider);
			
			if (stateInitialData) {
				stateProvider.initState(stateInitialData);
			}
			
			this.ready = true;
			
			this.fireEvent('appkit-ready');
		},
		
		/**
		 * Set the initial application state
		 * before init!
		 */
		setInitialState : function(s) {
			stateInitialData = s;
			this.fireEvent('appkit-statedata');

			if (!this.ready) {
				this.initEnvironment();
			}
		},
		
		/**
		 * General log implementation
		 */
		log : function() {
			if (typeof console !== "undefined" && console.log) {
				console.log[console.firebug ? 'apply' : 'call'](console,Array.prototype.slice.call(arguments));
			}
		},
		
		/**
		 * log calee arguments
		 */
		logargs : function(context) {
			this.log(context,arguments.callee.caller.arguments);
		},

		/**
		 * Sets the window location
		 */
                changeLocation : function(sUrl) {
                        if (window.location.replace) {
                                window.location.replace(sUrl);
                        }
                        else {
                                window.location.href = sUrl;
                        }

                        return true;
                },

		pageLoadingMask : function(remove) {
                        remove = (remove || false);
                        var ids = ['icinga-portal-loading-mask', 'icinga-portal-loading']
                        if (remove) {
                                Ext.iterate(ids, function(v) {
                                        Ext.get(v).fadeOut({remove: true});
                                });
                        }
                        else {
                                Ext.iterate(ids, function(v) {
                                        Ext.DomHelper.append(Ext.getBody(), {tag: 'div', id: v});
                                });
                        }
                }
	}))();
	
})();
