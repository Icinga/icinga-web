Ext.onReady(function() {
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
				id: 'template-north',
				autoHeight: true,
				html: 'NORTH'
			}, {
				layout: 'fit',
				region: 'center',
				id: 'template-center',
				contentEl: contentel
			}]
		});
		
		Ext.apply(pub, {
			
			getViewport: function() {
				return viewport;
			},
			
			getContentEl: function() {
				return contentel;
			},
			
			getCenter: function() {
				
				return this.getViewport().get('template-center');
			},
			
			doLayout: function() {
				this.getViewport().doLayout();
			}
			
		});
		
		return pub;
	})();
});