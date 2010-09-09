AppKit.util = (function() {
	var pub = {};
	var pstores = new Ext.util.MixedCollection(true);
	
	return Ext.apply(pub, {
		
		fastMode: function() {
			return Ext.isIE6 || Ext.isIE7 || Ext.isIE8;
		},
		
		parseDOMfromString : function(string, contentType) {
			if (!Ext.isEmpty(window.DOMParser)) {
				return (new DOMParser()).parseFromString(string, contentType || 'text/xml').firstChild;
				
			}
			
			throw('parseDOMfromString: could not create a new DOMParser instance!');
		},
		
		contentWindow : function(uconf, wconf) {
	
			Ext.applyIf(wconf, {
				bodyStyle: 'padding: 30px 30px',
				bodyCssClass: 'static-content-container'
			});
	
			Ext.apply(wconf, {
				renderTo: Ext.getBody(),
				footer: true,
				closable: false,
				
				buttons: [{
					text: _('Close'),
					handler: function() {
						win.close();
					}
				}],
				
				autoLoad: uconf
			});
			
			var win = new Ext.Window(wconf);
			
			win.setSize(Math.round(Ext.lib.Dom.getViewWidth() * 60 / 100), Math.round(Ext.lib.Dom.getViewHeight() * 80 / 100));
			win.center();
			
			win.show();
		},
		
		getStore : function(store_name) {
			if (pstores.containsKey(store_name)) {
				var s = pstores.get(store_name);
				if (s instanceof Ext.util.MixedCollection) {
					return s;
				}
				else {
					throw("Store" + store_name + " was gone away, not a store class anymore!");
				}
			}		
			else {
				return pstores.add(store_name, (new Ext.util.MixedCollection));
			}
		},
		
		/**
		 * Handling logout the agavi way
		 * @param string target
		 */
		doLogout : function(target) {
			Ext.Msg.show({
				title: _('To be on the verge to logout ...'),
				msg: _('Are you sure to do this?'),
				buttons: Ext.MessageBox.YESNO,
				icon: Ext.MessageBox.QUESTION,
				fn: function(b) {
					if (b=="yes") {
						AppKit.changeLocation(target);
					}
				}
			});
		},

		loginWatchdog : function(start) {
			var t={};
			Ext.Ajax.on('requestexception', function(conn, response, options) {
				if (!options.url.match(/\/login/)) {
					if (response.status == '403') {
						if (Ext.isEmpty(this.wflag)) {
							this.wflag=true;

							Ext.Msg.show({
								title: _('Session expired'),
								msg: _('Your login session has gone away, press ok to login again!'),
								icon: Ext.MessageBox.INFO,
								buttons: Ext.MessageBox.OK,
								fn: function() {
									AppKit.changeLocation(AppKit.c.path);
								}
							});

						}
					}
				}
			}, t);
		},
		
		/**
		 * Handling the preferences editor
		 * within a window
		 * @param string target
		 */
		doPreferences : function(target) {
			if (!Ext.getCmp('user_prefs_target')) {
				var pwin = new Ext.Window({
					title: _('User preferences'),
					closable: true,
					resizable: true,
					id: 'user_prefs_target',
					width: 530,
					height: Ext.getBody().getHeight()>600 ? 600 : Ext.getBody().getHeight(),
					autoScroll: true,
					closeAction: 'hide',
					
					bbar: {
						items: [{
							text: _('OK'),
							iconCls: 'icinga-icon-accept',
							handler: function() {
								AppKit.changeLocation(AppKit.c.path);
							}
						}, {
							text: _('Cancel'),
							iconCls: 'icinga-icon-cancel',
							handler: function() {
								pwin.close();
							}
						}]
					},
					
					autoLoad: {
						url: target,
						scripts: true
					}
				});
			}
			
			Ext.getCmp('user_prefs_target').show();
		}
		
	});
})();
	
	
