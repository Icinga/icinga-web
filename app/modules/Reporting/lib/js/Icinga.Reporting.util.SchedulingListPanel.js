// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}

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
			scheduler_list_url : this.scheduler_list_url,
			parentCmp : this
		});
		
		this.scheduleTaskList.getGrid().on('rowclick', this.processRowClick, this);
		
		this.scheduleEditForm = new Icinga.Reporting.util.ScheduleEditForm({
			border : false,
			region : 'south',
			collapsible : false,
			collapsed : false,
			scheduler_edit_url : this.scheduler_edit_url,
			scheduler_get_url : this.scheduler_get_url,
			parentCmp : this
		});
		
		this.on('afterlayout', function() {
			this.scheduleEditForm.setHeight(Math.floor(this.getInnerHeight()));
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
							
							this.cancelEdit();
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
		this.reloadTaskList();
	},
	
	reloadTaskList : function() {
		this.scheduleTaskList.reload();
	},
	
	processRowClick : function(grid, rowIndex, e) {
		var sm = grid.getSelectionModel();
		
		this.scheduleEditForm.cancelEdit();
		
		delete(this.selected_record);
		
		if (sm.getCount()) {
			this.selected_record = sm.getSelected();
			this.setToolbarEnabled(true, [3,4,6]);
		}
	}
	
});