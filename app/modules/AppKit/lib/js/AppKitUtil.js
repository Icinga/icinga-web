AppKit.util = (function() {
	
	var pub = {};
	var pstores = new Ext.util.MixedCollection(true);
	
	return Ext.apply(pub, {
		
		fastMode: function() {
			return Ext.isIE6 || Ext.isIE7 || Ext.isIE8;
		},
		
		contentWindow : function(uconf, wconf) {
	
			if (Ext.isEmpty(wconf.id)) {
				throw("wconf.id is mandatory");
			}
	
			var wid = wconf.id;
	
			if (!Ext.getCmp(wid)) {
		
				Ext.applyIf(wconf, {
					closeAction: 'hide'
				});
				
				if (Ext.isEmpty(wconf.disableContent)) {
					wconf.bodyStyle = 'padding: 30px 30px';
					wconf.bodyCssClass = 'static-content-container';
				}
				
				if (Ext.isEmpty(uconf.scripts, true)) {
					uconf.scripts = true;
				} 
				
				Ext.apply(wconf, {
					renderTo: Ext.getBody(),
					footer: true,
					closable: false,
					
					buttons: [{
						text: _('Close'),
						iconCls: 'icinga-icon-close',
						handler: function() {
							win.close();
						}
					}],
					
					autoLoad: uconf
				});
				
				var win = new Ext.Window(wconf);
				
				win.setSize(Math.round(Ext.lib.Dom.getViewWidth() * 60 / 100), Math.round(Ext.lib.Dom.getViewHeight() * 80 / 100));
			}
			
			Ext.getCmp(wid).show();
			Ext.getCmp(wid).center();
			
			return Ext.getCmp(wid);
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
		
		doTasks : function(target) {
			AppKit.util.contentWindow({
				url: target
			}, {
				id: 'admin_tasks_window',
				disableContent: true,
				title: _('Admin tasks'),
				bodyStyle: 'background-color: #fff;'
			});
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
					width: 630,
					layout: 'fit',
					height: Ext.getBody().getHeight()>600 ? 600 : Ext.getBody().getHeight(),
					constrain: true,
					closeAction: 'hide',
					defaults: {
						autoScroll: true,
						style: 'overflow:scroll'
					},
					bbar: {
						items: ['->', {
							text: _('OK'),
							iconCls: 'icinga-action-icon-ok',
							handler: function() {
								AppKit.changeLocation(AppKit.c.path);
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

AppKit.util.Config = (function() {
	return (new (Ext.extend(Ext.util.MixedCollection, {
		
		constructor : function() {
			this.items = {};
			Ext.util.MixedCollection.prototype.constructor.call(this);
			
			this.addAll({
				domain: document.location.host || document.domain,
				path: document.location.pathname.replace(/\/$/, ''),
				issecure: (document.location.protocol.indexOf('https') == 0) ? true : false
			});
			
			if (Ext.isEmpty(Icinga.AppKit.configMap) == false) {
				this.addAll(Icinga.AppKit.configMap);
			}
		},
		
		getMap : function() {
			return this.map;
		},
		
		getBaseUrl : function() {
			var out = (this.get('issecure')==true) ? 'https://' : 'http://';
			out += this.get('domain');
			out += this.get('path');
			return out;			
		}
		
	}))());
})();

// Reset the config object
AppKit.c = AppKit.util.Config.getMap();

AppKit.util.Date = (function() {
       var time = {};
           time.second = 1;
           time.minute = 60;
           time.hour = 3600;
           time.day  = 86400;
       return {
           getElapsedString: function(value) {
                var now = new Date();
                var valueDate = Date.parseDate(value,'Y-m-d H:i:s')
                    || Date.parseDate(value,'Y-m-d H:i:sP')
                    || Date.parseDate(value+":00",'Y-m-d H:i:sP');
                var elapsed = parseInt(now.getElapsed(valueDate)/1000,10);

                var dd = parseInt(elapsed/time.day,10);
                elapsed %= time.day;
                var hh = parseInt(elapsed/time.hour,10);
                elapsed %= time.hour;
                var mm = parseInt(elapsed/time.minute,10);
                elapsed %= time.minute;
                var ss = parseInt(elapsed/time.second,10);

                var result = dd ? dd+_(" days, ") :"";
                    result += hh ? hh+_(" hrs, ") : "";
                    result += mm ? mm+_(" min, ") : "";
                return (result += ss+_(" sec. ago"));
           }
     };
})();

// Domhelper
AppKit.util.Dom = (function () {
	
	var pub = {};
	
	var lDef = {
		imageSuffix:			'png',
		imagePathSeperator:		'/',
		imageSeperator:			'.'
	};
	
	var lDH = Ext.DomHelper; 
	
	Ext.apply(pub, {
	
		DEFAULTS : lDef,
		
		imageUrl: function(def, suffix) {
			try {
				return AppKit.util.Config.get('image_path') + '/'
				+ String.prototype.replace.call(def, lDef.imageSeperator, lDef.imagePathSeperator)
				+ '.' + (Ext.isEmpty(suffix) ? lDef.imageSuffix : suffix);
			}
			catch(e) {
				throw('imageUrl: Rethrow ' + e.toString());
			}
		},
		
		makeImage : function(el, def, spec, suffix) {
			try {
				spec = Ext.apply(spec || {}, {
					id: (el.id || Ext.id()) + '-makeImage',
					tag: 'img',
					src: this.imageUrl(def, suffix)
				});
				
				return lDH.append(Ext.get(el), spec); 
				
			}
			catch (e) {
				throw('makeImage: Rethrow ' + e.toString());
			}
		},
		
		parseDOMfromString : function(string, contentType) {
			if (!Ext.isEmpty(window.DOMParser)) {
				return (new DOMParser()).parseFromString(string, contentType || 'text/xml').firstChild;
			}
			else {
				xmlDoc=new ActiveXObject('Microsoft.XMLDOM');
				xmlDoc.async='false';
				xmlDoc.loadXML(string);
				return xmlDoc.firstChild
			}
			
			throw('parseDOMfromString: could not create a new DOMParser instance!');
		}
		
	});
	
	return pub;
})();


	
