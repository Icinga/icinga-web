Ext.onReady(function() {
	
	Ext.ns('AppKit.Layout');
	
	AppKit.Layout = (function() {
		
		var pub = {};
		
		var contentel = Ext.get('content');
//		contentel.hide();
		
		var viewport = new Ext.Viewport({
			layout: 'border',
			
			defaults: {
				border: false
			},
			
			items: [{
				layout: 'fit',
				region: 'north',
				id: 'viewport-north',
				autoHeight: true,
				html: 'NORTH'
			}, {
				layout: 'fit',
				region: 'center',
				id: 'viewport-center',
				contentEl: contentel
			}]
		});
		
		var center = viewport.get('viewport-center');
		var north = viewport.get('viewport-center');
		
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
				
				center.add(items);
				
				if (autol) {
					this.doLayout();
				}
			},
			
			doLayout: function() {
				this.getViewport().doLayout();
			}
			
		});
		
		return pub;
	})();
});