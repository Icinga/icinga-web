
AppKit.Ext.ScriptDynaLoader = function() {
	
	var pub = {};
	
	pub = Ext.extend(Ext.util.Observable, {
		constructor : function() {
			
			this.listeners = {};
			this.addEvents({
				loadscript : true
			});
			
			this.scripts = {};
			this.mask = undefined;
				
			pub.superclass.constructor.call(this);
		}
	});
	
	Ext.override(pub, {
		
		loadScript : function(script) {
			var o = {};
			
			if (typeof script == 'object') {
				Ext.apply(o, script);
			}
			else {
				o.url = script;
			}
			
			if (this.scripts[ o.url ] == true) {
			
				if (o.callback && typeof o.callback == "function") {
					var f = o.callback.createCallback();
					f.call( o.callbackScope || window );
				}
				
				return true;
			}
			
			this.showMask();
			
			Ext.Ajax.request({
				url : o.url,
				params : o.params || undefined,
				method : o.method || this.method || 'GET',
				success : this.handlerSuccess,
				failure : this.handlerFail,
				callback : this.handlerAlways,
				scope: this,
				timeout : o.timeout || this.timeout || 50000,
				disableCaching : o.disableCaching || this.disableCaching || true,
				
				argument : {
					cb : o.callback,
					callbackScope : o.callbackScope,
					url : o.url,
					options : o
				}
			});
			
		},
		
		execScript : function(string) {
			if (window.execScript) {
				window.execScript(string);
				return true;
			}
			else {
				window.eval(string);
				return true;
			}
		},
		
		handlerSuccess : function(response, o) {
			
			this.scripts[o.argument.url] = true;
			
			if (this.execScript.call(window, response.responseText) == true) {
			
				this.fireEvent('loadscript', this, response, o);
			
				if (typeof o.argument.cb == "function") {
					var f = o.argument.cb.createCallback();
					f.call(o.argument.callbackScope || window);
				}
			
			}
			
		},
		
		handlerFail : function(response, o) {
			
		},
		
		handlerAlways : function() {
			this.hideMask();
		},
		
		showMask : function() {
			if (!this.mask) {
				this.mask = new Ext.LoadMask(Ext.getBody());
				this.mask.show();
			}
		},
		
		hideMask : function() {
			if (this.mask) {
				this.mask.hide();
				this.mask = null;
			}
		}
		
	});
	
	return (new pub);
}();