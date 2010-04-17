Ext.ns('AppKit', 'APPKIT.lib');

(function() {
		
	AppKit = new (Ext.extend(
		AppKit = function() {
			var stateProvider = null;
			var stateInitialData = null;
			AppKit.superclass.constructor.call(this);
		}, Ext.util.Observable, {
			
			ready : false,
			
			c : {
				domain: document.location.host || document.domain,
				path: document.location.pathname.replace(/\/$/, ''),
				issecure: (document.location.protocol.indexOf('https') == 0) ? true : false
			},
			
			constructor : function() {
				
				this.on('i18n-ready', function() {
					alert("OK");
				});
				
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
					stateProvider.initState(this.stateInitialData);
				}
				
				this.ready = true;
				
				this.fireEvent('appkit-ready');
			},
			
			/**
			 * Set the initial application state
			 * before init!
			 */
			setInitialState : function(s) {
				this.initialState = s;
				this.fireEvent('appkit-statedata');
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
			}	
	}))();
	
})();