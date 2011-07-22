Ext.ns('Icinga.Reporting.util');


Icinga.Reporting.util.SchedulingListPanel = Ext.extend(Icinga.Reporting.abstract.ApplicationWindow, {
	
	layout : 'border',
	border: false,
	
	constructor : function(config) {
		
		config = Ext.apply(config || {}, {
			tbar : [{
				text : _('Schedule job'),
				iconCls : 'icinga-icon-alarm-clock-add',
				handler : this.processScheduleNew,
				scope : this
			}, {
				text : _('Run now'),
				iconCls : 'icinga-icon-alarm-clock-arrow',
				handler : this.processRunNow,
				scope : this
			}, '-', {
				text : _('Edit job'),
				iconCls : 'icinga-icon-alarm-clock-edit',
				handler : this.processEditJob,
				scope : this
			}, {
				text : _('Remove job'),
				iconCls : 'icinga-icon-alarm-clock-remove',
				handler : this.processRemoveJob,
				scope : this
			}, '-', {
				text : _('Refresh list'),
				iconCls : 'icinga-icon-arrow-refresh',
				handler : this.processRemoveJob,
				scope : this
			}]
		});
		
		Icinga.Reporting.util.SchedulingListPanel.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Reporting.util.SchedulingListPanel.superclass.initComponent.call(this);
		
		this.scheduleTaskList = new Icinga.Reporting.util.ScheduleTaskList({
			region : 'center',
			border : false
		});
		
		this.scheduleEditForm = new Icinga.Reporting.util.ScheduleEditForm({
			title : _('Parameters'),
			border : false,
			region : 'south'
		});
		
		this.on('afterlayout', function() {
			this.scheduleEditForm.setHeight(Math.floor(this.getInnerHeight() * .75));
		}, this, { single : true })
		
		this.add([this.scheduleTaskList, this.scheduleEditForm]);
		
		this.doLayout();
	},
	
	processNodeClick : function(node) {
		alert(node);
	},
	
	processScheduleNew : function(b, e) {
		
	},
	
	processRunNow : function(b, e) {
		
	},
	
	processEditJob : function(b, e) {
		
	},
	
	processRemoveJob : function(b, e) {
		
	},
	
	processRefreshTasklist : function(b, e) {
		
	}
	
});