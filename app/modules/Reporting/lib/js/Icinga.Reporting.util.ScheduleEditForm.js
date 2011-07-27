Ext.ns('Icinga.Reporting.util');

Icinga.Reporting.util.ScheduleEditForm = Ext.extend(Ext.form.FormPanel, {
	
	constructor : function(config) {
		
		config = Ext.apply(config || {}, {
			border : false,
	
			labelAlign : 'top',
			msgTarget : 'side',
			
			bbar : [{
				text : _('Save'),
				iconCls : 'icinga-icon-accept',
				handler : this.processFormSave,
				scope : this
			}, '-', {
				text : _('Cancel'),
				iconCls : 'icinga-icon-cancel',
				handler : this.processFormCancel,
				scope : this
			}]
		});
		
		Icinga.Reporting.util.ScheduleEditForm.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		
		Icinga.Reporting.util.ScheduleEditForm.superclass.initComponent.call(this);
		
		this.formTabs = new Ext.TabPanel({
			height : 500,
			border : false,
			activeTab : 0,
			
			layoutConfig : {
				deferredRender : false
			},
			
			forceLayout : true,
			
			defaults:{
				bodyStyle:'padding:10px',
				layout : 'form',
				forceLayout : true
			},
			items : [{
				iconCls : 'icinga-cronk-icon-1',
				title : _('Basics'),
				autoScroll : true,
				height : 500,
				defaults : {
					width : 300
				},
				items : [{
					xtype : 'textfield',
					fieldLabel : _('Job name'),
					name : 'label'
				}, {
					xtype : 'textarea',
					fieldLabel : _('Description'),
					name : 'description'
				}, {
					xtype : 'textfield',
					fieldLabel : _('Report URI'),
					name : 'reportUnitURI',
					readOnly : true
				}, {
					xtype : 'textfield',
					fieldLabel : _('Job ID'),
					name : 'id',
					readOnly : true
				}]
			}, {
				iconCls : 'icinga-cronk-icon-2',
				title : _('Scheduling'),
				items : [{
					xtype : 'fieldset',
					title : _('Start time'),
					height : 120,
					layout : {
						type : 'vbox',
						defaultMargins : ' 0 10 0 0'
					},
					items : [{
						xtype : 'radio',
						name : 'trigger.startType',
						boxLabel : _('Run immediately'),
						checked : true,
						inputValue : 1 
					}, {
						xtype : 'container',
						width : 350,
						layout : {
							type : 'hbox',
							defaultMargins : ' 10 0 0 0'
						},
						items : [{
							xtype : 'radio',
							name : 'trigger.startType',
							boxLabel : _('On'),
							inputValue : 2,
							listeners : {
								check : this.processStartTimeToggle.createDelegate(this)
							}
						}, {
							xtype : 'datefield',
							name : 'trigger.startDate',
							format : 'c',
							disabled : true,
							width : 300
						}]
					}]
				}]
			}, {
				iconCls : 'icinga-cronk-icon-3',
				title : _('Recurrence'),
				autoScroll : true,
				items : [{
					xtype : 'radiogroup',
					fieldLabel : _('Type'),
					name : 'trigger',
					anchor : '50%',
					listeners : {
						change : this.processTriggerTypeChange.createDelegate(this)
					},
					items : [{
						xtype : 'radio',
						boxLabel : _('None'),
						name : 'trigger',
						inputValue : 'recurrence-none',
						checked : true
					}, {
						xtype : 'radio',
						boxLabel : _('Simple'),
						name : 'trigger',
						inputValue : 'recurrence-simple'
					}, {
						xtype : 'radio',
						boxLabel : _('Calendar'),
						name : 'trigger',
						inputValue : 'recurrence-calendar'
					}]
				}, {
					xtype : 'fieldset',
					title : _('Simple'),
					id : this.getId() + '-recurrence-simple',
					hidden : false,
					items : [{
						xtype : 'container',
						layout : {
							type : 'hbox',
							defaultMargins : ' 10 0 0 0' // BUG: Prefix with space to get work
						},
						fieldLabel : 'Repeat every',
						items : [{
							xtype : 'textfield',
							width : 50,
							value : 1,
							name : 'simpleTrigger.recurrenceInterval'
						}, {
							xtype : 'combo',
							typeAhead : true,
							triggerAction : 'all',
							width: 100,
							
							mode : 'local',
							store : new Ext.data.ArrayStore({
								id : 0,
								fields : [
									'interval',
									'label'
								],
								data : [
									['MINUTE', _('minutes')],
									['HOUR', _('hours')],
									['DAY', _('days')],
									['WEEK', _('weeks')]
								]
							}),
							value : 'DAY',
							valueField : 'interval',
							displayField : 'label',
							name : 'simpleTrigger.recurrenceIntervalUnit'
						}]
					}, {
						xtype : 'container',
						layout : {
							type : 'vbox',
							defaultMargins : ' 0 10 0 0'
						},
						height : 150,
						items : [{
							xtype : 'container',
							width: 300,
							height : 25,
							layout : {
								type : 'hbox',
								defaultMargins : ' 10 0 0 0'
							},
							items : [{
								xtype : 'radio',
								boxLabel : _('Indefinitely'),
								name : 'recurrence_type',
								checked : true
							}]
						}, {
							xtype : 'container',
							layout : {
								type : 'hbox',
								defaultMargins : ' 10 0 0 0'
							},
							width : 350,
							height : 25,
							items : [{
								xtype : 'radio',
								boxLabel : _('Times'),
								name : 'recurrence_type',
								width : 60,
								listeners : {
									check : this.processRecurrenceChange.createDelegate(this, ['simpleTrigger.occurrenceCount'], true)
								}
							}, {
								xtype : 'textfield',
								name : 'simpleTrigger.occurrenceCount',
								disabled : true,
								width: 200
							}]
						}, {
							xtype : 'container',
							layout : {
								type : 'hbox',
								defaultMargins : ' 10 0 0 0' // BUG: Prefix with space to get work
							},
							width : 350,
							height : 25,
							items : [{
								xtype : 'radio',
								boxLabel : _('Until'),
								name : 'recurrence_type',
								width : 60,
								listeners : {
									check : this.processRecurrenceChange.createDelegate(this, ['simpleTrigger.endDate'], true)
								}
							}, {
								xtype : 'datefield',
								name : 'simpleTrigger.endDate',
								format : 'c',
								disabled : true,
								width: 200
							}]
						}]
					}]
				}, {
					xtype : 'fieldset',
					title : _('Calendar'),
					id : this.getId() + '-recurrence-calendar',
					height: 500,
					hidden : false,
					items : [{
						xtype : 'container',
						height : 200,
						autoScroll : true,
						defaults : {
							flex : 0
						},
						layout : {
							type : 'vbox'
						},
						items: [{
							border : false,
							xtype : 'container',
							layout : {
								type : 'column'
							},
							defaults : {
								border : false
							},
							height : 200,
							items : [{
								columnWidth : '200px',
								width: 200,
								height : 200,
								layout: {
									type : 'vbox',
									defaultMargins : ' 0 5 0 0'
								},
								items: [{
									xtype : 'label',
									text : _('Months')
								}, {
									xtype : 'radio',
									boxLabel : _('Every month'),
									name : 'calendar_month_type'
								}, {
									xtype : 'radio',
									boxLabel : _('Selected months'),
									name : 'calendar_month_type'
								}, new Icinga.Reporting.widget.MonthPicker({
									name : 'calendarTrigger.months',
									height : 80
								})]
							}, {
								columnWidth : '200px',
								width : 200,
								height: 200,
								layout : {
									type : 'vbox',
									defaultMargins : ' 0 5 0 0'
								},
								items: [{
									xtype : 'label',
									text : _('Days')
								}, {
									xtype : 'radio',
									boxLabel : _('Every day'),
									name : 'calendar_day_type'
								}, {
									xtype : 'radio',
									boxLabel : _('Week days'),
									name : 'calendar_day_type'
								}, new Icinga.Reporting.widget.WeekDayPicker({
									name : 'calendarTrigger.weekDays',
									height : 80
								}), {
									xtype : 'radio',
									boxLabel : _('Month days'),
									name : 'calendar_day_type'
								}, {
									xtype : 'textfield',
									name : 'calendarTrigger.monthDays'
								}]
							}]
						}]
					}, {
						xtype : 'container',
						layout : {
							type : 'vbox',
							defaultMargins : ' 0 5 0 0'
						},
						defaults : {
							width : 300
						},
						height: 300,
						items : [{
							xtype : 'label',
							text : _('Times')
							
						}, {
							xtype : 'container',
							height : 25,
							layout : {
								type : 'hbox',
								defaultMargins : ' 0 0 10 0'
							},
							items : [{
								xtype : 'textfield',
								name : 'calendarTrigger.hours'
							}, {
								xtype : 'label',
								text : _('Hours')
							}]
						}, {
							xtype : 'label',
							height : 25,
							text : _('Hint: Enter 24-hour times like 9,12,15 or ranges like 9-12,1-17')
						}, {
							xtype : 'container',
							height : 25,
							layout : {
								type : 'hbox',
								defaultMargins : ' 0 0 10 0'
							},
							items : [{
								xtype : 'textfield',
								name : 'calendarTrigger.minutes'
							}, {
								xtype : 'label',
								text : _('Minutes')
							}]
						}, {
							xtype : 'label',
							height : 25,
							text : _('Hint: Enter 0,15,30,45 to run every 1/2 hour')
						}, {
							xtype : 'container',
							height : 25,
							layout : {
								type : 'hbox',
								defaultMargins : ' 10 0 0 0'
							},
							items : [{
								xtype : 'label',
								text : _('Recur until')
							}, {
								xtype :'datefield',
								name : 'calendarTrigger.endDate',
								format : 'c',
								width : 200
							}]
						}]
					}]
				}]
			}, {
				iconCls : 'icinga-cronk-icon-4',
				title : _('Parameters'),
				autoScroll : true,
				layout : {
					type : 'auto'
				},
				items : [{
					xtype : 'container',
					items : [{
						xtype : 'fieldset',
						title : _('Parameterise report'),
						id : this.getId() + '-parameter-target'
					}]
				}]
			}, {
				iconCls : 'icinga-cronk-icon-5',
				title : _('Output'),
				autoScroll : true,
				layout : {
					type : 'auto'
				},
				items : [{
					xtype : 'container',
					layout : {
						type : 'form'
					},
					items : [{
						xtype : 'fieldset',
						title : _('Output identification'),
						defaults : {
							width : 400
						},
						items : [{
							xtype : 'textfield',
							fieldLabel : _('Base output filename'),
							name : 'baseOutputFilename'
						}, {
							xtype : 'textarea',
							fieldLabel : _('Output description'),
							name : 'repositoryDestination.outputDescription'
						}]
					}, {
						xtype : 'fieldset',
						title : _('Output location'),
						defaults : {
							width : 400
						},
						items : [{
							xtype : 'textfield',
							fieldLabel : _('The file will be added to'),
							name : 'repositoryDestination.folderURI'
						}, {
							xtype : 'container',
							layout : {
								type : 'hbox',
								defaultMargins : ' 10 0 0 0'
							},
							items : [{
								xtype : 'checkbox',
								boxLabel : _('Sequential File Names'),
								name : 'repositoryDestination.sequentialFilenames'
							}, {
								xtype : 'textfield',
								name : 'repositoryDestination.timestampPattern'
							}, {
								xtype : 'label',
								text : '(' + _('Timestamp pattern') + ')'
							}]
						}, {
							xtype : 'checkbox',
							boxLabel : _('Overwrite files'),
							name : 'repositoryDestination.overwriteFiles'
						}]
					}, {
						xtype : 'fieldset',
						title : _('Output format'),
						defaults : {
							width : 400
						},
						items : [{
							xtype : 'checkboxgroup',
							name : 'outputFormats',
							columns : 6,
							items : [
								{ name : 'outputFormats.PDF', boxLabel : _('PDF'), inputValue : 1 },
								{ name : 'outputFormats.HTML', boxLabel : _('HTML'), inputValue : 2 },
								{ name : 'outputFormats.XLS', boxLabel : _('Excel'), inputValue : 3 },
								{ name : 'outputFormats.CSV', boxLabel : _('CSV'), inputValue :4 },
								{ name : 'outputFormats.DOC', boxLabel : _('DOCX'), inputValue : 5 },
								{ name : 'outputFormats.RTF', boxLabel : _('RTF'), inputValue : 6 },
								{ name : 'outputFormats.ODT', boxLabel : _('ODT'), inputValue : 7 },
								{ name : 'outputFormats.ODS', boxLabel : _('ODS'), inputValue : 8 },
								{ name : 'outputFormats.XSLX', boxLabel : _('XSLX'), inputValue : 9 }
							]
						}]
					}, {
						xtype : 'fieldset',
						title : _('Email notification'),
						defaults : {
							width : 400
						},
						items :[{
							xtype : 'textarea',
							fieldLabel : _('To'),
							name : 'mailNotification.toAddresses'
						}, {
							xtype : 'label',
							text : ('Hint: use commas to separate addresses')
						}, {
							xtype : 'textfield',
							fieldLabel : _('Subject'),
							name : 'mailNotification.subject'
						}, {
							xtype : 'textarea',
							fieldLabel : _('Message'),
							name : 'mailNotification.messageText'
						}, {
							xtype : 'checkbox',
							boxLabel : _('Attach files'),
							name : 'mailNotification.resultSendType'
						}, {
							xtype : 'checkbox',
							boxLabel : _('Skip empty reports'),
							name : 'mailNotification.skipEmptyReports'
						}]
					}]
				}]
			}]
		});
		
		/*
		 * Need to prerender all items in tab panel. Because
		 * the form is not ready we don't do this.
		 * @todo Check why deferred rendering is not working in card layout
		 */
		this.formTabs.on('afterrender', function(component) {
			component.findBy(function(item, two) {
				item.show();
				if (['panel'].indexOf(item.getXType()) >= 0) {
					item.doLayout();
				}
			}, this);
			
			this.collapse(false);

		}, this, { single : true, delay : 400 });
		/*
		 * ----------------------------------------------------------------
		 */
		
		this.add(this.formTabs);
		
		this.doLayout();
		
	},
	
	processStartTimeToggle : function(checkboxChecked, checked) {
		var field = this.getForm().findField('trigger.startDate');
		if (field) {
			field.setDisabled(!checked);
		}
	},
	
	processTriggerTypeChange : function(radioGroup, radioChecked) {
		var h = ['recurrence-simple', 'recurrence-calendar'];
		i = h.length;
		while (i--) {
			var cmp = Ext.getCmp(String.format('{0}-{1}', this.getId(), h[i]));
			if (!cmp) {
				continue;
			}
			
			if (radioChecked.inputValue == h[i]) {
				cmp.show();
			}
			else {
				cmp.hide();
			}
		}
		
		
	},
	
	processRecurrenceChange : function(checkBox, checked, field) {
		var field = this.getForm().findField(field);
		if (field) {
			field.setDisabled(!checked);
		}
	},
	
	processFormSave : function() {
		var values = this.getForm().getFieldValues();
		AppKit.log(values);
	},
	
	processFormCancel : function() {
		this.cancelEdit();
	},
	
	cancelEdit : function() {
		try {
			this.getForm().reset();
		} catch (e) {
			// DO NOTHING
		}
		this.collapse(true);
	},
	
	startEdit : function(report_uri, job_id) {
		
		var params = {
			uri : report_uri
		};
		
		if (job_id) {
			params.job = job_id
		}
		
		if (params.uri) {
			this.formTabs.setActiveTab(0);
			
			Ext.Ajax.request({
				url : this.scheduler_get_url,
				params : params,
				success : function(response, options) {
//					try {
						var data = Ext.util.JSON.decode(response.responseText);
						this.applyFormData.defer(10, this, [data]);
//					} catch (e) {
//						AppKit.notifyMessage(_('Error'), _(String.format(_('Could not parse response: {0}'), e)));
//					}
				},
				scope : this
			})
			this.expand(true);
		}
	},
	
	applyFormData : function(data) {
		if (!Ext.isEmpty(data.inputControls)) {
			this.createReportParametersForm(data.inputControls);
		}
		
		if (!Ext.isEmpty(data.job)) {
			var dataTool = new Icinga.Reporting.util.JobFormValues({
				form : this.getForm(),
				data : data.job
			});
			
			dataTool.applyFormValues();
		}
	},
	
	createReportParametersForm : function(controls) {
		var fieldset = Ext.getCmp(this.getId() + '-parameter-target');
		if (fieldset) {
			var builder = new Icinga.Reporting.util.InputControlBuilder({
				target : fieldset,
				controlStruct : controls,
				removeAll : true,
				namePrefix : 'parameters.'
			});
			
			builder.applyToTarget();
		}
	},
	
	applyFormValues : function(job) {
		
	}
	
});