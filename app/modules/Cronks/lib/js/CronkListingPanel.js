

Cronk.util.CronkListingPanel = function(c) {
	Cronk.util.CronkListingPanel.superclass.constructor.call(this, c);
	
	var loadData = function() {
		
	};
	
}

Ext.extend(Cronk.util.CronkListingPanel, Ext.Panel, {
	layout: 'accordion',
	layoutConfig: {
		animate: true,
		renderHidden: false,
		hideCollapseTool: true,
		fill: true
	},
	
	autoScroll: true,
	border: false,
	
	defaults: { border: false },
	
	html: 'LAOLA',
	
	constructor: function(c) {
		Cronk.util.CronkListingPanel.superclass.constructor.call(this, c);
		
	}
});
 