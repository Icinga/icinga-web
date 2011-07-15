Ext.ns('Icinga.Reporting.abstract');

Icinga.Reporting.abstract.ResizedContainer = Ext.extend(Ext.Container, {
	
	constructor : function(config) {
		Icinga.Reporting.abstract.ResizedContainer.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Reporting.abstract.ResizedContainer.superclass.initComponent.call(this);
		
		this.on('afterrender', function() {
			var p = this.findParentByType('tabpanel');
		
			p.on('resize', function(tb, adjWidth, adjHeight, rawWidth, rawHeight) {
				this.setHeight(adjHeight-53);
			}, this);
			
		}, this, { single : true });
		
		
		
		var resizeFn = function(c) {
			var p = this.findParentByType('tabpanel');
			if (p) {
				this.setHeight(p.getInnerHeight()-26);
			}
		}
		
		this.on('afterrender', resizeFn, this, { single : true });
//		this.on('resize', resizeFn, this, { single : true });
//		Ext.EventManager.onWindowResize(resizeFn, this);
	}
	
});