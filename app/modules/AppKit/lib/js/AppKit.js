Ext.ns('AppKit', 'APPKIT.lib');

(function() {
	
	AppKit = (function() {
		var pub = function() { pub.superclass.constructor.call(this); };
		
		var stateProvider = null;
		var stateInitialData = null;
		
		var growlStackElement = null;
		var growlTemplate = new Ext.Template([
			'<div class="growl-msg-message">',
				'<div class="head">{header}</div>',
				'<div>{message}</div>',
			'</div>'
		]);
		
		var taskRunner = null;
		
		Ext.extend(pub, Ext.util.Observable, {
			
			constructor: function() {
				this.superclass.constructor.call(this);
			},
			
			ready : false,
			
			c : {
				domain: document.location.host || document.domain,
				path: document.location.pathname.replace(/\/$/, ''),
				issecure: (document.location.protocol.indexOf('https') == 0) ? true : false
			},
	
			initEnvironment : function() {
				Ext.BLANK_IMAGE_URL = this.c.path + '/images/ajax/s.gif';
				
				Ext.QuickTips.init();
				
				this.growlStack();
				
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
	
			
			pageLoadingMask : function(time, remove) {
	                remove = (remove || false);
	                time = (time || 2000);
	                
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
	                        
	                        if (time > 0) {
	                        	setTimeout(AppKit.pageLoadingMask.createDelegate(this, [0, true]), time);
	                        }
	                }
	        },
	        
	        growlStack : function() {
	        	if (!growlStackElement) {
	        		growlStackElement = Ext.DomHelper.insertFirst(Ext.getBody(), {
	        			id: 'growl-msg-stack'
	        		}, true);
	        	}
	        	
	        	growlStackElement.alignTo(Ext.getDoc(), 'tr-tr', [-18, 10]);
	        	
	        	return growlStackElement;
	        },
	        
	        growlPopupBox : function(message, title, icon) {
	        	
	        	var box = growlTemplate.append(this.growlStack(), {
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
	        	}
	        	
	        	var nm = String.format.apply(this, la);
	        	
	        	var ele = this.growlPopupBox(nm, title);
	        	
	        	ele.slideIn('t').pause(c.waitTime).ghost('t', {remove:true});
	        },
	        
	        getTr : function() {
	        	if (!taskRunner) {
	        		taskRunner = new Ext.util.TaskRunner();
	        	}
	        	return taskRunner;
	        }
			
		});
		
		return new pub();
	})();
	
})();
