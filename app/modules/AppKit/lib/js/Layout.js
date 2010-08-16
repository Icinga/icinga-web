Ext.onReady(function() {
	 
	var _LAYOUT,
	_UTIL = AppKit.util,
	_A = AppKit,
	_FE = AppKit.fireEvent;
	
	_UTIL.Layout = new (_LAYOUT=Ext.extend(Object, function() {
		
		// - Private
		var viewport = null,
		
		createNew = function() {
			return new Ext.Viewport({
				id: 'appkit-viewport',
				layout: 'border',
				defaults: { border: false },
				items: [
					{ layout: 'fit', region: 'north', id: 'viewport-north', border: false, height: 30 }, 
					{ layout: 'fit',region: 'center', id: 'viewport-center', border: false, contentEl: 'content' }
				]
			});
		};
		
		// - Public
		return {
			cidmap : {
				main	: 'appkit-viewport',
				center	: 'viewport-center',
			 	north	: 'viewport-north',
			 	menu	: 'viewport-menu',
			 	coel	: 'content'
			},
			
			constructor : function() {
				_LAYOUT.superclass.constructor.call(this);
				
				Ext.onReady(this.getViewport, _LAYOUT);
			},
			
//			on : function() {
//				var v = this.getViewport();
//				return v.on.apply(v, arguments);
//			},
			
			doLayout : function(n, buffer, hard) {
				n = (n||'__VIEWPORT');
				buffer = (buffer?new Number(buffer):0);
				hard = (hard||false);
				
				
				
				var cmp=null, fn, args=[];
				
				cmp=((n=='__VIEWPORT') ? this.getViewport() : this.byName(n).doLayout());
				
				if (!cmp) return;
				
				if (hard !==false) {
					args = [false, true];
				}
				
				fn=(function() {
					this.doLayout.apply(this, arguments);
				}).createDelegate(cmp, args);
				
				// cmp.doLayout.createDelegate(cmp, args);
				
				if (buffer>0) {
					var task = new Ext.util.DelayedTask(fn, this);
					task.delay(buffer);
				}
				else {
					fn.call(this);
				}
			},
			
			addTo : function(item, dlayout, rname) {
				rname = (rname||'center');
				dlayout = (dlayout?new Number(dlayout):0);
				var rv = this.byName(rname).add(item);
				
				if (dlayout>0) {
					var task = new Ext.util.DelayedTask(this.doLayout, this);
					task.delay(dlayout);
				}
				
				return rv;
			},
			
			byName : function(n) {
				if (Ext.isDefined(this.cidmap[n])) {
					return this.getViewport().items.get( this.cidmap[n] );
				}
				return null;
			},
			
			getNorth : function() {
				return this.byName('north');
			},
			
			getCenter : function() {
				return this.byName('center');
			},
			
			getViewport : function() {
				if (!viewport) {
					viewport = createNew();
				}
				return viewport;
			},
			
			_decodeMenuData : function (obj) {
				
					var f = function (o) {
						for (var t in o) {
							if (typeof(o[t])=='object') f(o[t]);
							else if (t == 'handler') o[t] = Ext.decode(o[t]);
						}
					}
					
					// if (typeof(window['_']) == 'undefined') {
					// 	window['_'] = function(v) { return v; }
					// }
					
					f(obj);
					
					// delete(window['_']);
					
					return obj;
			}
		}
		
	}()));
	
	_A.Layout = _UTIL.Layout;

//		
//		setMenu: function(json) {
//			var north = this.getNorth();
//			json = this._decodeMenuData((json || {}));
//			var menu = north.add({
//				layout: 'column',
//				autoHeight: true,
//				id: this.fuid_menu,
//				border:false,
//				items: [{
//					tbar: new Ext.Toolbar({
//						id: 'menu-bar',
//						items: json['items'] || {}
//					}),
//					columnWidth: 1,
//					border: false
//				}, {
//					id: 'menu-user',
//					width: 150,
//					border: false
//				}, {
//					id: 'menu-logo',
//					width: 25,
//					border: false
//					
//				}]
//			});
//		}
//	 	
//	 });


});
