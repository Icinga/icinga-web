(function() {
	AppKit.util.ScriptDynaLoader = new (Ext.extend(Ext.util.Observable, {
		
		constructor : function() {
				
				this.listeners = {};
				
				this.addEvents({
					loadscript : true,
					bulkfinish : true
				});
				
				this.scripts = {};
				this.activeTransactions = {};
				this.transCount = 0;
				this.bulkMode = false;
				this.mask = undefined;
					
				this.superclass.constructor.call(this);
			},
		
		loadScript : function(script) {
			var o = {};
			
			if (typeof script == 'object') {
				Ext.apply(o, script);
			}
			else {
				o.url = script;
			}
			
			this.transCount++;
			
			// console.log("BULK START" + this.transCount);
			// console.log("BULK SCRIPT " + o.url);
			
			if (this.scripts[ o.url ] == true) {
			
				if (o.callback && typeof o.callback == "function") {
					var f = o.callback.createCallback();
					f.call( o.callbackScope || window );
				}
				
				if (this.bulkMode == true) {
					this.fireBulkEvent();
				}
				
				return true;
			}
			
			this.showMask();
			
			var trans = Ext.Ajax.request({
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
			
			this.activeTransactions[ trans.tId ] = true;
			
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
		
		handlerAlways : function(options, success, request) {
			
			if (this.activeTransactions[ request.tId ]) {
				delete this.activeTransactions[ request.tId ];
				
				if (this.bulkMode == true && this.transCount > 0) {
					this.fireBulkEvent();
				}
			}
			
			this.hideMask();
		},
		
		startBulkMode : function() {
			this.bulkMode = true;
			this.transCount = 0;
		},
		
		fireBulkEvent : function() {
			this.transCount--;
			// console.log("BULK" + this.transCount);
			if (this.transCount == 0) {
				this.fireEvent('bulkfinish', this);
				this.bulkMode=false;
				this.transCount = 0;
			}
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
				
	}))();
})();