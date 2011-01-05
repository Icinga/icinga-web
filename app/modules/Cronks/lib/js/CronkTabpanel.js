Ext.ns('Cronk.util');

Cronk.util.Tabpanel = function(config) {

	this.stateEvents = ['add', 'remove', 'tabchange', 'titlechange'];
	
	Cronk.util.Tabpanel.superclass.constructor.call(this, config);	
}

Ext.extend(Cronk.util.Tabpanel, Ext.ux.SlidingTabPanel, {
	URLTabData : false,
	
	minTabWidth: 115,
    tabWidth:135,
	
	setURLTab : function(params) {
		this.URLTabData = params;
		
	},
	
	getState: function() {
		
		var cout = {};
	
		this.items.each(function(item, index, l) {
			if (Cronk.Registry.get(item.getId())) {
				cout[item.getId()] = Cronk.Registry.get(item.getId());
				
				if (Ext.isDefined(item.iconCls)) {
					cout[item.getId()].iconCls = item.iconCls;
				}
			}
		});
		// AppKit.log("STATE", cout);
		var t = this.getActiveTab();
		return {
			cronks: cout,
			items: this.items.getCount(),
			active: ( (t) ? t.getId() : null )
		}
	},
	
	applyState: function(state) {
		(function() {
			if (state.cronks) {
				// Adding all cronks
				Ext.iterate(state.cronks, function(index, item, o) {
					this.add(item);
				}, this);
				
				// Sets the active tab
				this.setActiveTab(state.active || 0);
				if(this.URLTabData) {

					this.setActiveTab(this.add(this.plugins.createURLCronk(this.URLTabData)));		
				}				
				this.getActiveTab().doLayout();
			}
				
						
		}).defer(5, this);
				
		return true;
	},
	listeners: {
		tabchange: function(tab) {
			var aTab = tab.getActiveTab();
			document.title = "Icinga - "+aTab.title;
		}
	}
});

Ext.reg('cronk-control-tabs', Cronk.util.Tabpanel);
