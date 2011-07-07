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
		
		this.on('added', function() {
			this.activate(0);
		}, this, { single : true });
		
		if (this.enable_onthefly == true) {
			
			this.appOnTheFly = new Icinga.Reporting.util.OnTheFly({
				treeloader_url: this.treeloader_url,
				parampanel_url: this.parampanel_url,
				creator_url : this.creator_url,
				parentCmp : this
			});
			
			this.add({
				title : _('On-the-fly'),
				tabTip : _('Go here to create reports on the fly'),
				items : this.appOnTheFly,
				iconCls : 'icinga-icon-report-run'
				
			});
		}
		
		if (this.enable_repository == true) {
			
			this.appRepository = new Icinga.Reporting.util.Repository({
				treeloader_url: this.treeloader_url,
				parentCmp : this
			});
			
			this.add({
				title: _('Repository'),
				tabTip: _('Explore the server-repository'),
				items : this.appRepository,
				iconCls : 'icinga-icon-bricks'
			});
		}
		
//		, {
//			title: _('Schedules'),
//			tabTip: _('Create and modify scheduled jobs')
//		}
	}
});