Ext.ns('Icinga.Reporting.util');

Icinga.Reporting.util.ReportScheduling = Ext.extend(Icinga.Reporting.abstract.ResizedContainer, {
	layout : 'border',
	height : 800, // Don't worry, we resize later
	border : false,
	
	constructor : function(config) {
		Icinga.Reporting.util.ReportScheduling.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Reporting.util.ReportScheduling.superclass.initComponent.call(this);
		
		this.resourceTree = new Icinga.Reporting.util.ResourceTree({
			region : 'west',
			width : 350,
			split: true,
			collapsible: true,
			treeloader_url: this.treeloader_url,
			treeloader_filter: 'reports'
		});
		
		this.schedulingList = new Icinga.Reporting.util.SchedulingListPanel({
			region : 'center',
			parentCmp : this.parentCmp,
			scheduler_list_url : this.scheduler_list_url,
			scheduler_get_url : this.scheduler_get_url,
			scheduler_edit_url : this.scheduler_edit_url,
			scheduler_delete_url : this.scheduler_delete_url
		});
		
		this.resourceTree.getTreePanel().on('click', this.schedulingList.processNodeClick, this.schedulingList);
		
		this.add([this.resourceTree, this.schedulingList]);
		
		this.doLayout();
		
	}
});