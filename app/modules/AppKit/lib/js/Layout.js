Ext.onReady(function() {
	
	Ext.ns('AppKit.Layout');

	/**
	 * AppKit.Layout
	 * 
	 * Singleton class of the site implementation
	 * 
	 */	
	AppKit.Layout = (function() {
		
		var queue = [];
		
		var pub = {};
		var contentel = 'content';
		var viewport = null;
		var center = null;
		var north = null;
		var menu = null;
		
		var decodeHandler = function(obj) {
			var f = function (o) {
				for (var t in o) {
					if (typeof(o[t])=='object') f(o[t]);
					else if (t == 'handler') o[t] = Ext.decode(o[t]);
				}
			}
			
			if (typeof(window['_']) == 'undefined') {
				window['_'] = function(v) { return v; }
			}
			
			f(obj);
			
			delete(window['_']);
			
			return obj;
		}
		
		/**
		 * public
		 */
		Ext.apply(pub, {
			
			getViewport: function() {
				return viewport;
			},
			
			getContentEl: function() {
				return contentel;
			},
			
			getCenter: function() {
				return center;
			},
			
			addCenter: function(items, autol) {
				autol = autol || false;
				
				if (center) {
					center.add(items);
					
					if (autol) {
						this.doLayout();
					}
				}
				else {
					alert("ohoh");
					queue.push(items);
				}
			},
			
			doLayout: function() {
				this.getViewport().doLayout();
			},
			
			getMenu : function() {
				return menu;
			},
			
			setMenu: function(json) {
				
				if (menu) {
					throw("Menu already exists");
				}
				
				json = decodeHandler(json || {});
				
				menu = north.add({
					layout: 'column',
					border: false,
					
					items: [{
						tbar: {
							style: 'border: none',
							items: json['items'] || {}
						},
						columnWidth: 1,
						border: false
					}, {
						html: 'TEST1',
						width: 100,
						border: false
					}, {
						html: 'TEST2',
						width: 25,
						height: 25,
						border: false,
						autoEl: {
							tag: 'img',
							src: '/icinga-web/images/icinga/idot-small.png'
						}
					}]
				});
				
				north.doLayout();
			}
			
		});
		
		viewport = new Ext.Viewport({
			layout: 'border',
			
			defaults: {
				border: false
			},
			
			items: [{
				layout: 'fit',
				region: 'north',
				border: false,
				id: 'viewport-north',
				autoHeight: true,
				listeners: {
					afterrender: function(p) {
						north = p;
						AppKit.fireEvent('north-ready', north, pub);
					}
				}
			}, {
				layout: 'fit',
				region: 'center',
				id: 'viewport-center',
				contentEl: contentel,
				listeners: {
					afterrender: function(p) {
						center = p;
						AppKit.fireEvent('center-ready', north, pub);
					}
				}
			}]
		});
		
		center = viewport.get('viewport-center');
		north = viewport.get('viewport-center');
		
		return pub;
	})();
});