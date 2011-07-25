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
				handler : this.processRefreshTasklist,
				scope : this
			}]
		});
		
		Icinga.Reporting.util.SchedulingListPanel.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Reporting.util.SchedulingListPanel.superclass.initComponent.call(this);
		
		this.setToolbarEnabled(false);
		
		this.scheduleTaskList = new Icinga.Reporting.util.ScheduleTaskList({
			region : 'center',
			border : false,
			scheduler_list_url : this.scheduler_list_url
		});
		
		this.scheduleTaskList.getGrid().on('rowclick', this.processRowClick, this);
		
		this.scheduleEditForm = new Icinga.Reporting.util.ScheduleEditForm({
			border : false,
			region : 'south',
			collapsible : false,
			collapsed : true,
			scheduler_get_url : this.scheduler_get_url
		});
		
		this.on('afterlayout', function() {
			this.scheduleEditForm.setHeight(528); // Math.floor(this.getInnerHeight() * .75)
		}, this, { single : true })
		
		this.add([this.scheduleTaskList, this.scheduleEditForm]);
		
		this.doLayout();
	},
	
	processNodeClick : function(node) {
		this.setToolbarEnabled(false);
		this.scheduleEditForm.collapse(true);
		
		delete(this.selected_report);
		
		if (node.attributes.type == "reportUnit") {
			this.selected_report = node.attributes;
			this.scheduleTaskList.loadJobsForUri(node.attributes.uri);
			this.setToolbarEnabled(true, [1,7]);
		}
	},
	
	processScheduleNew : function(b, e) {
		if (!Ext.isEmpty(this.selected_report)) {
			this.scheduleEditForm.startEdit(this.selected_report.uri);
		}
	},
	
	processRunNow : function(b, e) {
		
	},
	
	processEditJob : function(b, e) {
		if (!Ext.isEmpty(this.selected_report) && !Ext.isEmpty(this.selected_record)) {
			this.scheduleEditForm.startEdit(this.selected_report.uri, this.selected_record.id);
		}
	},
	
	processRemoveJob : function(b, e) {
		var reFunc = function(buttonId, text, opt) {
			if (buttonId == 'yes') {
				Ext.Ajax.request({
					url : this.scheduler_delete_url,
					params : {
						uri : this.selected_report.uri,
						job : this.selected_record.id
					},
					success : function(response, opts) {
						try {
							var o = Ext.util.JSON.decode(response.responseText);
							if (o.success == true) {
								AppKit.notifyMessage(_('Job deleted'), _('Job was deleted successfully'));
								this.scheduleTaskList.reload();
							}
							else {
								AppKit.notifyMessage(_('Error'), String.format(_('Could not delete job: {0}'), o.error));
							}
					} catch(e) {
							AppKit.log(response);
						}
					},
					scope : this
				})
			}
		};
		
		Ext.MessageBox.show({
			title : _('Delete job'),
			msg : _('Do you want delete the selected job?'),
			buttons : Ext.MessageBox.YESNO,
			fn : reFunc,
			icon : Ext.MessageBox.WARNING,
			animEl : e.getRelatedTarget(),
			scope : this
		});
	},
	
	processRefreshTasklist : function(b, e) {
		this.scheduleTaskList.reload();
	},
	
	processRowClick : function(grid, rowIndex, e) {
		var sm = grid.getSelectionModel();
		
		this.scheduleEditForm.cancelEdit();
		
		delete(this.selected_record);
		
		if (sm.getCount()) {
			this.selected_record = sm.getSelected();
			this.setToolbarEnabled(true, [2,4,5]);
		}
	}
	
});