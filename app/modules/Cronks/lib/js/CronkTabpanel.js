Ext.ns('Cronk.util');

Cronk.util.Tabpanel = function(config) {

	this.stateEvents = ['add', 'remove', 'tabchange', 'titlechange'];

	Cronk.util.Tabpanel.superclass.constructor.call(this, config);	
}

Ext.extend(Cronk.util.Tabpanel, Ext.ux.SlidingTabPanel, {

	getState: function() {
		
		var cout = {};
	
		this.items.each(function(item, index, l) {
			if (item.getXType() == 'cronk' && Cronk.Registry.get(item.getId())) {
				cout[item.getId()] = Cronk.Registry.get(item.getId());
			}
		});
		
		return {
			cronks: cout,
			items: this.items.getCount(),
			active: this.getActiveTab().id
		}
	},
	
	applyState: function(state) {
		(function() {
			
			if (state.cronks) {
				// Adding all cronks
				Ext.iterate(state.cronks, function(index, item, o) {
					var c = Ext.apply({}, item);
					delete(c.loaderUrl);
					this.add(c);
					
				}, this);

				// Sets tehe active tab
				this.setActiveTab(state.active);
			}
			
						
		}).defer(5, this);
		
		return true;
	}
});

Ext.reg('cronk-control-tabs', Cronk.util.Tabpanel);
