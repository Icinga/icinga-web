Ext.ns('Icinga.Reporting');

Icinga.Reporting.Cronk = Ext.extend(Ext.TabPanel, {
	
	border : false,
	
	defaults : {
		border: false
	},
	
	constructor : function(c) {
		Icinga.Reporting.Cronk.superclass.constructor.call(this, c);
	},
	
	initComponent : function() {
		Icinga.Reporting.Cronk.superclass.initComponent.call(this);
		
		this.appOnTheFly = new Icinga.Reporting.util.OnTheFly({
			treeloader_url: this.treeloader_url,
			parampanel_url: this.parampanel_url,
			creator_url : this.creator_url
		});
		
		this.on('added', function() {
			this.activate(0);
		}, this, { single : true });

		this.add([{
			title : _('On-the-fly'),
			tabTip : _('Go here to create reports on the fly'),
			items: this.appOnTheFly
			
		}, {
			title: _('Repository'),
			tabTip: _('Explore the server-repository')
		}, {
			title: _('Schedules'),
			tabTip: _('Create and modify scheduled jobs')
		}]);
	}
});