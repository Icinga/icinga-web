
Ext.ns('Cronk.util');

Cronk.util.Tabpanel = Ext.extend(Ext.ux.SlidingTabPanel, {
	
	id : 'cronk-tabs',
	border : false,
	enableTabScroll :true,
	resizeTabs : false,
	
	// This component is stateful!
	stateful: true,
	stateId: 'cronk-tab-panel',
	
	stateEvents: ['add', 'remove', 'tabchange', 'titlechange'],
	
	getState: function() {
		
		var cout = { };
		
		this.items.each(function(item, index, l) {
			if (item.iscronk && AppKit.Ext.CronkMgr.cronkExist(item.cronkkey)) {
				var c = AppKit.Ext.CronkMgr.getCronk(item.cronkkey);
				var cronk = AppKit.Ext.CronkMgr.getCronkComponent(item.cronkkey);

				c.config.title = cronk.title;

				cout[c.cmpid] = Ext.apply(c);
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
					var config = {};
					
					Ext.apply(config, item.config, item.crconf);
					
					var cronk = AppKit.Ext.CronkMgr.create(config);
	
					this.add(cronk);
					
				}, this);

				// Sets tehe active tab
				this.setActiveTab(state.active);
			}
			
						
		}).defer(5, this);
		
		return true;
	}
});

Ext.reg('cronk-tabpanel', Cronk.util.Tabpanel);
