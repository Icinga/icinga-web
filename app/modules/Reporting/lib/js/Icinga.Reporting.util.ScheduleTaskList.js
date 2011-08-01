Ext.ns('Icinga.Reporting.util');

Icinga.Reporting.util.ScheduleTaskList = Ext.extend(Ext.Panel, {
	constructor : function(config) {
		Icinga.Reporting.util.ScheduleTaskList.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Reporting.util.ScheduleTaskList.superclass.initComponent.call(this);
		this.taskListStore = new Ext.data.JsonStore({
			autoDestroy : true,
			url : this.scheduler_list_url,
			autoLoad : false
		});
		
		this.taskGrid = new Ext.grid.GridPanel({
			border: false,
			store : this.taskListStore,
			layout : 'fit',
			autoFill : true,
			autoHeight : true,
			colModel: new Ext.grid.ColumnModel({
				defaults : {
					width: 120,
					sortable : false
				},
				
				columns : [{
					id : 'id',
					header : _('Id'),
					dataIndex : 'id',
					width: 40
				}, {
					header : _('Job name'),
					dataIndex : 'label',
					width : 250
				}, {
					header : _('Owner'),
					dataIndex : 'username',
					width : 80
				}, {
					header : _('State'),
					dataIndex : 'state',
					renderer : {
						fn : function(value, metaData, record, rowIndex, colIndex, store) {
							s = new String(value);
							return s.charAt(0) + s.substr(1).toLowerCase();
						},
						scope : this
					},
					width : 60
				}, {
					header : _('Last run'),
					dataIndex : 'previousFireTime',
					width : 120
				}, {
					header : _('Next run'),
					dataIndex : 'nextFireTime',
					width : 120
				}]
			}),
			viewConfig: {
				forceFit: true
			},
			sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		});
		
		this.add(this.taskGrid);
		
		this.doLayout();
	},
	
	reload : function() {
		this.taskListStore.reload();
	},
	
	loadJobsForUri : function(uri) {
		this.taskListStore.removeAll();
		this.taskListStore.setBaseParam('uri', uri);
		this.taskListStore.load();
	},
	
	getGrid : function() {
		return this.taskGrid;
	},
	
	getStore : function() {
		return this.taskListStore();
	}
});